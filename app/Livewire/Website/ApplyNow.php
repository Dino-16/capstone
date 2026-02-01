<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Applicants\Application;
use App\Models\Applicants\FilteredResume;
use App\Models\Recruitment\JobListing;
use App\Data\NCRAddressData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\Admin\RecaptchaSetting;
use App\Models\Admin\RecaptchaLog;

class ApplyNow extends Component
{
    use WithFileUploads;
    use \App\Traits\WithHoneypot;

    public $applicantLastName, $applicantFirstName, $applicantMiddleName, $applicantSuffixName, $applicantPhone, $applicantEmail, $applicantResumeFile;
    public $applicantAge, $applicantGender, $applicantDateOfBirth;
    public $isUploading = false;
    public $lastAnalysisTime = null;
    public $job, $agreedToTerms = false, $showTerms = false, $showSuccessToast = false;
    public $regions = [], $provinces = [], $cities = [], $barangays = [];
    public $selectedRegion, $selectedProvince, $selectedCity, $selectedBarangay, $houseStreet;
    public $showRecaptchaModal = true;
    public $recaptchaVerified = false;

    public function mount($id)
    {
        $this->job = JobListing::findOrFail($id);
        
        // Check if reCAPTCHA is enabled
        $setting = RecaptchaSetting::first();
        if ($setting && !$setting->is_enabled) {
            $this->showRecaptchaModal = false;
        }

        try {
            $this->regions = Http::withoutVerifying()->get('https://psgc.cloud/api/regions')->json();
        } catch (\Exception $e) {
            $this->regions = [];
        }
    }

    public function verifyRecaptcha($recaptchaResponse)
    {
        $secretKey = config('recaptcha.secret_key');
        
        try {
            $response = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
                'remoteip' => request()->ip(),
            ]);
            
            $result = $response->json();
            
            // Log the reCAPTCHA attempt
            RecaptchaLog::create([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => ($result['success'] ?? false) ? 'success' : 'failed',
            ]);
            
            if ($result['success'] ?? false) {
                $this->recaptchaVerified = true;
                $this->showRecaptchaModal = false;
            } else {
                $this->addError('recaptcha', 'reCAPTCHA verification failed. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            $this->addError('recaptcha', 'An error occurred during verification. Please try again.');
        }
    }

    /**
     * Handle region selection change
     */
    public function updatedSelectedRegion($value)
    {
        // Reset dependent fields
        $this->provinces = [];
        $this->cities = [];
        $this->barangays = [];
        $this->selectedProvince = null;
        $this->selectedCity = null;
        $this->selectedBarangay = null;

        // Cast to string for reliable comparison (API may return int or string)
        $regionCode = (string) $value;

        \Log::info('Region selected', ['value' => $value, 'regionCode' => $regionCode, 'type' => gettype($value)]);

        if ($regionCode === '1300000000') {
            // NCR - Load cities directly from NCRAddressData
            $ncrCities = NCRAddressData::getCitiesAndBarangays();
            $this->cities = $ncrCities;
            \Log::info('NCR cities loaded', ['count' => count($ncrCities), 'cities' => collect($ncrCities)->pluck('name')]);
        } elseif (!empty($value)) {
            // Other regions - Fetch provinces from API
            try {
                $this->provinces = Http::withoutVerifying()->get("https://psgc.cloud/api/regions/{$value}/provinces")->json();
            } catch (\Exception $e) {
                $this->provinces = [];
            }
        }
    }

    /**
     * Handle province selection change
     */
    public function updatedSelectedProvince($value)
    {
        // Reset dependent fields
        $this->cities = [];
        $this->barangays = [];
        $this->selectedCity = null;
        $this->selectedBarangay = null;

        if (!empty($value)) {
            try {
                $this->cities = Http::withoutVerifying()->get("https://psgc.cloud/api/provinces/{$value}/cities-municipalities")->json();
            } catch (\Exception $e) {
                $this->cities = [];
            }
        }
    }

    /**
     * Handle city selection change
     */
    public function updatedSelectedCity($value)
    {
        // Reset dependent fields
        $this->barangays = [];
        $this->selectedBarangay = null;

        if ((string) $this->selectedRegion === '1300000000') {
            // NCR - Load barangays from NCRAddressData
            $ncrCities = NCRAddressData::getCitiesAndBarangays();
            $selectedCity = collect($ncrCities)->firstWhere('code', $value);
            
            if ($selectedCity && isset($selectedCity['barangays'])) {
                $this->barangays = collect($selectedCity['barangays'])->map(function ($barangay) {
                    return ['name' => $barangay];
                })->toArray();
            }
        } elseif (!empty($value)) {
            // Other regions - Fetch barangays from API
            try {
                $this->barangays = Http::withoutVerifying()->get("https://psgc.cloud/api/cities-municipalities/{$value}/barangays")->json();
            } catch (\Exception $e) {
                $this->barangays = [];
            }
        }
    }

    /**
     * Remove the uploaded resume file
     */
    public function removeResume()
    {
        $this->applicantResumeFile = null;
    }

    public function submitApplication()
    {
        // Increase execution time limit for AI analysis (2 minutes)
        set_time_limit(120);

        // Honeypot Check
        if (!$this->checkHoneypot('Job Application Form')) {
            return;
        }

        $this->validate([
            'applicantLastName' => 'required|max:50',
            'applicantFirstName' => 'required|max:50',
            'applicantMiddleName' => 'required|max:50',
            'applicantEmail' => 'required|email',
            'applicantPhone' => 'required',
            'applicantAge' => 'required|integer|min:18|max:65',
            'applicantDateOfBirth' => 'required|date|before:today',
            'applicantGender' => 'required|in:male,female',
            'applicantResumeFile' => 'required|file|mimes:pdf|max:2048',
            'selectedRegion' => 'required',
            'selectedProvince' => 'required_unless:selectedRegion,1300000000',
            'selectedCity' => 'required',
            'selectedBarangay' => 'required',
            'houseStreet' => 'required',
            'agreedToTerms' => 'accepted',
        ]);

        DB::beginTransaction();

        try {
            $regionName = collect($this->regions)->firstWhere('code', $this->selectedRegion)['name'] ?? $this->selectedRegion;
            $provinceName = ($this->selectedRegion === '1300000000') ? 'NCR' : (collect($this->provinces)->firstWhere('code', $this->selectedProvince)['name'] ?? $this->selectedProvince);
            $cityName = $this->resolveCityName($this->selectedRegion, $this->selectedCity);

            $path = $this->applicantResumeFile->store('resumes', 'public');

            // Save application first
            $application = Application::create([
                'applied_position' => $this->job->position,
                'department'       => $this->job->department,
                'first_name'       => $this->applicantFirstName,
                'middle_name'      => $this->applicantMiddleName,
                'last_name'        => $this->applicantLastName,
                'suffix_name'      => $this->applicantSuffixName,
                'age'              => $this->applicantAge,
                'date_of_birth'    => $this->applicantDateOfBirth,
                'gender'           => $this->applicantGender,
                'email'            => $this->applicantEmail,
                'phone'            => $this->applicantPhone,
                'region'           => $regionName,
                'province'         => $provinceName,
                'city'             => $cityName,
                'barangay'         => $this->selectedBarangay,
                'house_street'     => $this->houseStreet,
                'resume_path'      => $path,
                'agreed_to_terms'  => $this->agreedToTerms,
            ]);

            DB::commit();

            // --- Run AI analysis immediately (synchronous) ---
            $aiData = [];
            $resumeContent = null;
            $pdfParseSuccess = false;
            
            try {
                $filePath = storage_path('app/public/' . $path);
                
                // Log file existence and permissions
                if (!file_exists($filePath)) {
                    \Log::error("Resume file does not exist", [
                        'application_id' => $application->id,
                        'file_path' => $filePath,
                    ]);
                } elseif (!is_readable($filePath)) {
                    \Log::error("Resume file is not readable", [
                        'application_id' => $application->id,
                        'file_path' => $filePath,
                        'permissions' => substr(sprintf('%o', fileperms($filePath)), -4),
                    ]);
                }

                // Try to parse PDF to text
                try {
                    if (!class_exists('\Smalot\PdfParser\Parser')) {
                        \Log::error("PdfParser class not found - package may not be installed");
                        throw new \Exception("PdfParser package not available");
                    }
                    
                    $parser = new Parser();
                    $pdf = $parser->parseFile($filePath);
                    $resumeContent = $pdf->getText();
                    
                    \Log::info("PDF parsed with smalot/pdfparser", [
                        'application_id' => $application->id,
                        'content_length' => strlen($resumeContent),
                        'has_text' => !empty(trim($resumeContent)),
                    ]);
                    
                    // If PDF parser returns empty content, the PDF is likely image-based
                    if (empty(trim($resumeContent))) {
                        \Log::error("PDF contains no extractable text - likely an image-based/scanned PDF", [
                            'application_id' => $application->id,
                            'file_path' => $filePath,
                            'file_size' => filesize($filePath),
                            'recommendation' => 'Applicant should upload a text-based PDF resume',
                        ]);
                        
                        // Try to extract ANY text using alternative regex patterns
                        $rawContent = file_get_contents($filePath);
                        
                        // Extract anything that looks like readable text from PDF stream
                        if (preg_match_all('/\[(.*?)\]/s', $rawContent, $matches)) {
                            $extractedText = implode(' ', $matches[1]);
                            $cleanText = preg_replace('/[^\x20-\x7E\s]/', '', $extractedText);
                            
                            if (strlen(trim($cleanText)) > 50) {
                                $resumeContent = $cleanText;
                                \Log::info("Extracted text using regex fallback", [
                                    'application_id' => $application->id,
                                    'content_length' => strlen($resumeContent),
                                ]);
                            }
                        }
                    }
                    
                } catch (\Exception $e) {
                    \Log::error("PDF parsing failed for application ID {$application->id}", [
                        'error' => $e->getMessage(),
                        'error_class' => get_class($e),
                        'file_path' => $filePath,
                    ]);
                    $resumeContent = null;
                }

                // Only attempt AI analysis if we have content
                if (!empty(trim($resumeContent))) {
                    $prompt = "You are an AI that extracts structured data from resumes for automated screening.\n\nJob position: {$application->applied_position}.\n\nResume content:\n{$resumeContent}\n\nReturn a single valid JSON object with EXACTLY these keys and types:\n- skills: array of strings (each string is a single skill or technology, e.g. 'PHP', 'Laravel', 'Customer Service'). Always return at least 3 skills; if not explicit, infer from context or use generic skills like 'Communication', 'Teamwork'.\n- experience: array of strings (each string is one job or role, e.g. 'Software Developer at Company A (2019-2022)'). Always return at least 1 experience item; if no job is given, use a best-guess like 'Work experience not clearly specified'.\n- education: array of strings (each string is one degree or education item, e.g. 'BS Computer Science - University X (2018)'). Always return at least 1 education item; if missing, use a placeholder like 'Education not clearly specified'.\n- score: number from 0 to 100 (overall suitability rating for the job)\n\nNever leave skills, experience, or education as empty arrays; always infer reasonable values or use an 'not clearly specified' placeholder string.\n\nExample JSON format (do NOT wrap in markdown):\n{\n  \"skills\": [\"PHP\", \"Laravel\", \"Customer Support\"],\n  \"experience\": [\"Software Developer - Company A (2019-2022)\"],\n  \"education\": [\"BS Computer Science - University X (2018)\"],\n  \"score\": 85\n}.\n\nReturn ONLY the JSON object, with no extra text, labels, or explanations before or after.";

                    // Analyze resume with better error handling
                    try {
                        \Log::info("Attempting OpenAI API call", [
                            'application_id' => $application->id,
                            'api_key_set' => !empty(config('openai.api_key')),
                        ]);
                        
                        $response = OpenAI::chat()->create([
                            'model' => 'gpt-3.5-turbo',
                            'messages' => [
                                ['role' => 'system', 'content' => 'You are a helpful resume analyzer.'],
                                ['role' => 'user', 'content' => $prompt],
                            ],
                            'max_tokens' => 500,
                        ]);

                        $text = trim($response['choices'][0]['message']['content'] ?? '{}');
                        
                        \Log::info("OpenAI API response received", [
                            'application_id' => $application->id,
                            'response_length' => strlen($text),
                        ]);

                        // Try to decode full response first
                        $json = json_decode($text, true);

                        // If that fails, try to extract the JSON object from within the text
                        if (!is_array($json)) {
                            $start = strpos($text, '{');
                            $end = strrpos($text, '}');

                            if ($start !== false && $end !== false && $end > $start) {
                                $candidate = substr($text, $start, $end - $start + 1);
                                $json = json_decode($candidate, true);
                            }
                        }

                        $aiData = is_array($json) ? $json : [];

                        if (empty($aiData)) {
                            \Log::warning('AI resume analysis returned empty or invalid data', [
                                'application_id' => $application->id,
                                'raw_text' => $text,
                            ]);
                        } else {
                            \Log::info('AI resume analysis successful', [
                                'application_id' => $application->id,
                                'score' => $aiData['score'] ?? 'missing',
                            ]);
                        }
                    } catch (\Exception $apiException) {
                        \Log::error("OpenAI API call failed", [
                            'application_id' => $application->id,
                            'error' => $apiException->getMessage(),
                            'error_class' => get_class($apiException),
                        ]);
                        throw $apiException;
                    }
                } else {
                    \Log::warning('Resume content is empty after parsing and fallback', [
                        'application_id' => $application->id,
                        'file_path' => $filePath,
                        'file_exists' => file_exists($filePath),
                        'file_size' => file_exists($filePath) ? filesize($filePath) : 0,
                    ]);
                }

            } catch (\Exception $e) {
                \Log::error("Failed AI resume analysis for application ID {$application->id}", [
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            // Parse resume text sections and age as an additional fallback
            $parsedSections = $this->parseResumeSections($resumeContent ?? '');
            $parsedSkills = $parsedSections['skills'] ?? [];
            $parsedExperience = $parsedSections['experience'] ?? [];
            $parsedEducation = $parsedSections['education'] ?? [];

            // Normalize skills (accept array or comma/semicolon/newline-separated string)
            $rawSkills = $aiData['skills'] ?? $aiData['Skills'] ?? $parsedSkills;
            if (is_string($rawSkills)) {
                $skills = array_filter(array_map('trim', preg_split('/[,;\n]+/', $rawSkills)));
            } elseif (is_array($rawSkills)) {
                $skills = $rawSkills;
            } else {
                $skills = [];
            }
            if (empty($skills) && !empty($parsedSkills)) {
                $skills = $parsedSkills;
            }
            if (empty($skills)) {
                $skills = ['Skills not clearly specified'];
            }

            // Normalize experience
            $rawExperience = $aiData['experience'] ?? $aiData['Experience'] ?? $parsedExperience;
            if (is_string($rawExperience)) {
                $experience = array_filter(array_map('trim', preg_split('/[\n;]+/', $rawExperience)));
            } elseif (is_array($rawExperience)) {
                $experience = $rawExperience;
            } else {
                $experience = [];
            }
            if (empty($experience) && !empty($parsedExperience)) {
                $experience = $parsedExperience;
            }
            if (empty($experience)) {
                $experience = ['Work experience not clearly specified'];
            }

            // Normalize education
            $rawEducation = $aiData['education'] ?? $aiData['Education'] ?? $parsedEducation;
            if (is_string($rawEducation)) {
                $education = array_filter(array_map('trim', preg_split('/[\n;]+/', $rawEducation)));
            } elseif (is_array($rawEducation)) {
                $education = $rawEducation;
            } else {
                $education = [];
            }
            if (empty($education) && !empty($parsedEducation)) {
                $education = $parsedEducation;
            }
            if (empty($education)) {
                $education = ['Education not clearly specified'];
            }

            $ratingScore = $aiData['score'] ?? $aiData['Score'] ?? 50;
            
            // ALWAYS calculate qualification status from score (don't trust AI-provided qualification)
            // This ensures alignment between score and status
            if ($ratingScore >= 90) {
                $qualificationStatus = 'Exceptional';
            } elseif ($ratingScore >= 80) {
                $qualificationStatus = 'Highly Qualified';
            } elseif ($ratingScore >= 70) {
                $qualificationStatus = 'Qualified';
            } elseif ($ratingScore >= 60) {
                $qualificationStatus = 'Moderately Qualified';
            } elseif ($ratingScore >= 50) {
                $qualificationStatus = 'Marginally Qualified';
            } else {
                $qualificationStatus = 'Not Qualified';
            }

            // Save filtered resume
            $application->filteredResume()->create([
                'skills'               => $skills,
                'experience'           => $experience,
                'education'            => $education,
                'rating_score'         => (int) $ratingScore,
                'qualification_status' => $qualificationStatus,
            ]);

            $this->showSuccessToast = true;

            $this->reset([
                'applicantLastName', 'applicantFirstName', 'applicantMiddleName',
                'applicantSuffixName', 'applicantPhone', 'applicantEmail',
                'applicantAge', 'applicantGender', 'applicantDateOfBirth',
                'applicantResumeFile', 'selectedRegion', 'selectedProvince',
                'selectedCity', 'selectedBarangay', 'houseStreet', 'agreedToTerms'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('submission', 'Database Error: ' . $e->getMessage());
        }
    }

    private function extractAgeFromResume(string $text): ?int
    {
        if (trim($text) === '') {
            return null;
        }

        // Pattern 1: "Age: 27" or "Age - 27"
        if (preg_match('/\bage\s*[:\-]?\s*(\d{1,2})\b/i', $text, $m)) {
            $age = (int) $m[1];
            if ($age >= 15 && $age <= 70) {
                return $age;
            }
        }

        // Pattern 2: "Date of Birth ... 1999" or "DOB: 1999"
        if (preg_match('/\b(?:date of birth|dob|birth date|born)\b[^0-9]*(\d{4})/i', $text, $m)) {
            $year = (int) $m[1];
            $currentYear = now()->year;
            $age = $currentYear - $year;
            if ($age >= 15 && $age <= 70) {
                return $age;
            }
        }

        return null;
    }

    private function parseResumeSections(string $text): array
    {
        $sections = [
            'skills' => [],
            'experience' => [],
            'education' => [],
        ];

        if (trim($text) === '') {
            return $sections;
        }

        $normalized = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $normalized);
        $current = null;

        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '') {
                continue;
            }

            $lower = strtolower($trim);

            // Skills / Technical Skills / Key Skills heading, possibly with inline list
            if (preg_match('/^(skills|technical skills|key skills|core competencies)\s*[:\-]?(.*)$/i', $trim, $m)) {
                $current = 'skills';
                $rest = trim($m[2] ?? '');
                if ($rest !== '') {
                    $items = array_filter(array_map('trim', preg_split('/[,;]+/', $rest)));
                    foreach ($items as $it) {
                        $sections['skills'][] = $it;
                    }
                }
                continue;
            }

            if (preg_match('/^(work\s+experience|experience)\b/i', $lower)) {
                $current = 'experience';
                continue;
            }

            if (preg_match('/^education\b/i', $lower)) {
                $current = 'education';
                continue;
            }

            if ($current !== null) {
                $item = preg_replace('/^[\-\*•]+\s*/u', '', $trim);
                if ($item !== '') {
                    $sections[$current][] = $item;
                }
            }
        }

        return $sections;
    }

    private function resolveCityName($region, $cityCode)
    {
        if ($region === '1300000000') {
            $ncrCityNames = [
                '137504000' => 'Caloocan City',
                '137506000' => 'Las Piñas City',
                '137507000' => 'Makati City',
                '137508000' => 'Malabon City',
                '137509000' => 'Mandaluyong City',
                '137501000' => 'Manila',
                '137511000' => 'Marikina City',
                '137512000' => 'Muntinlupa City',
                '137513000' => 'Navotas City',
                '137514000' => 'Parañaque City',
                '137515000' => 'Pasay City',
                '137516000' => 'Pasig City',
                '137502000' => 'Quezon City',
                '137517000' => 'San Juan City',
                '137518000' => 'Taguig City',
                '137519000' => 'Valenzuela City',
                '137520000' => 'Pateros'
            ];
            return $ncrCityNames[$cityCode] ?? $cityCode;
        }
        return collect($this->cities)->firstWhere('code', $cityCode)['name'] ?? $cityCode;
    }

    public function render()
    {
        return view('livewire.website.apply-now')->layout('layouts.website');
    }
}
