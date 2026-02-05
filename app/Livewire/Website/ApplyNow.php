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
use App\Models\Admin\RecaptchaSetting;
use App\Models\Admin\RecaptchaLog;

class ApplyNow extends Component
{
    use WithFileUploads;
    use \App\Traits\WithHoneypot;

    public $applicantLastName, $applicantFirstName, $applicantMiddleName, $applicantSuffixName, $applicantPhone, $applicantEmail, $applicantResumeFile;
    public $applicantAge, $applicantGender, $applicantCivilStatus, $applicantDateOfBirth;
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
            'applicantCivilStatus' => 'required|in:Single,Married,Widowed,Separated,Divorced',
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
                'civil_status'     => $this->applicantCivilStatus,
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
            
            try {
                $filePath = storage_path('app/public/' . $path);
                
                // 1. Try smalot/pdfparser with more robust page-by-page extraction
                try {
                    $parser = new Parser();
                    $pdf    = $parser->parseFile($filePath);
                    
                    // Try getting text from the document directly
                    $resumeContent = $pdf->getText();
                    
                    // If still empty, try iterating through pages (sometimes more reliable)
                    if (empty(trim($resumeContent))) {
                        $pages = $pdf->getPages();
                        $pageCount = count($pages);
                        \Log::info("PdfParser found no text in document object, trying pages", ['page_count' => $pageCount]);
                        foreach ($pages as $page) {
                            $resumeContent .= $page->getText() . "\n";
                        }
                    }
                    
                    \Log::info("Smalot PDF Parser final result", ['len' => strlen($resumeContent ?? '')]);
                } catch (\Exception $e) {
                    \Log::warning("Smalot PDF Parser failed: " . $e->getMessage());
                }

                // 2. Fallback: pdftotext command line (usually fails on Windows but worth a shot for servers)
                if (empty(trim($resumeContent))) {
                    try {
                        $outputFile = storage_path('app/temp_pdf_' . $application->id . '.txt');
                        // Use escapeshellarg for security
                        $command = "pdftotext -layout " . escapeshellarg($filePath) . " " . escapeshellarg($outputFile);
                        exec($command, $output, $returnVar);
                        
                        if ($returnVar === 0 && file_exists($outputFile)) {
                            $resumeContent = file_get_contents($outputFile);
                            \Log::info("pdftotext fallback succeeded", ['len' => strlen($resumeContent)]);
                            @unlink($outputFile);
                        } else {
                            \Log::warning("pdftotext fallback failed with code $returnVar");
                        }
                    } catch (\Exception $e) {
                        \Log::warning("pdftotext fallback exception: " . $e->getMessage());
                    }
                }

                // 3. AI Analysis with OpenAI
                $apiKey = env('OPENAI_API_KEY');
                if (!empty($apiKey)) {
                    // Check if we actually have text. If not, we can't do much with Chat completions
                    if (empty(trim($resumeContent))) {
                        \Log::warning("No text extracted for Application #{$application->id}. AI analysis skipped.");
                    } else {
                        // More explicit prompt to ensure AI returns the exact structure expected by the view
                        $prompt = "You are a professional HR Resume Parser. Extract the following data from the resume text provided.
                        
                        Position to evaluate for: {$application->applied_position}
                        
                        Structure your response as a valid JSON object with these EXACT keys:
                        - skills: (array of strings)
                        - experience: (array of objects with 'title', 'company', 'period', 'description' keys)
                        - education: (array of objects with 'degree', 'field', 'institution', 'year' keys)
                        - score: (integer 0-100 reflecting suitability for the position)
                        
                        Resume Text:
                        " . substr($resumeContent, 0, 12000); // 12k chars is safe for context limits

                        $apiUrl = "https://api.openai.com/v1/chat/completions";
                        
                        try {
                            $payload = [
                                "model" => "gpt-4o",
                                "messages" => [
                                    ["role" => "system", "content" => "You output ONLY raw JSON. Do not include markdown code blocks or any other text."],
                                    ["role" => "user", "content" => $prompt]
                                ],
                                "temperature" => 0.1, // Lower temperature for more consistent JSON
                                "response_format" => ["type" => "json_object"]
                            ];

                            $response = Http::withToken($apiKey)
                                ->withOptions([
                                    'verify' => env('OPENAI_SSL_VERIFY', false), 
                                    'timeout' => 60 // Increased timeout
                                ])
                                ->post($apiUrl, $payload);
                            
                            if ($response->successful()) {
                                $responseData = $response->json();
                                $content = $responseData['choices'][0]['message']['content'] ?? '{}';
                                $aiData = json_decode($content, true) ?: [];
                                \Log::info("OpenAI Analysis successful for #{$application->id}");
                            } else {
                                \Log::error("OpenAI API Error for #{$application->id}: " . $response->body());
                            }
                        } catch (\Exception $e) {
                            \Log::error("OpenAI request failed for #{$application->id}: " . $e->getMessage());
                        }
                    }
                }

            } catch (\Exception $e) {
                \Log::error("Main analysis block error: " . $e->getMessage());
            }

            // --- Final Data Assembly & Fallbacks ---
            $parsedSections = $this->parseResumeSections($resumeContent ?? '');
            
            // Heuristic scoring if AI fails
            $fallbackScore = $this->calculateHeuristicScore($resumeContent ?? '', $application->applied_position);
            
            $skills      = !empty($aiData['skills'])     ? $aiData['skills']     : ($parsedSections['skills'] ?? ['Manual review required']);
            $experience  = !empty($aiData['experience']) ? $aiData['experience'] : ($parsedSections['experience'] ?? ['Manual review required']);
            $education   = !empty($aiData['education'])  ? $aiData['education']  : ($parsedSections['education'] ?? ['Manual review required']);
            $ratingScore = $aiData['score'] ?? $fallbackScore;

            // Qualification Status mapping
            if ($ratingScore >= 90) $qualificationStatus = 'Exceptional';
            elseif ($ratingScore >= 80) $qualificationStatus = 'Highly Qualified';
            elseif ($ratingScore >= 70) $qualificationStatus = 'Qualified';
            elseif ($ratingScore >= 60) $qualificationStatus = 'Moderately Qualified';
            elseif ($ratingScore >= 50) $qualificationStatus = 'Marginally Qualified';
            else $qualificationStatus = 'Not Qualified';

            // Create the filtered resume record
            // We now always create it even if empty, so the user can see it in the list 
            // and potentially perform a manual override/edit
            $application->filteredResume()->create([
                'skills'               => is_array($skills) ? $skills : [$skills],
                'experience'           => is_array($experience) ? $experience : [$experience],
                'education'            => is_array($education) ? $education : [$education],
                'rating_score'         => (int) $ratingScore,
                'qualification_status' => $qualificationStatus,
            ]);

            if (empty(trim($resumeContent))) {
                \Log::warning("Record created with empty text extraction for #{$application->id}. Scanned PDF suspected.");
            }

            $this->showSuccessToast = true;

            $this->reset([
                'applicantLastName', 'applicantFirstName', 'applicantMiddleName',
                'applicantSuffixName', 'applicantPhone', 'applicantEmail',
                'applicantAge', 'applicantGender', 'applicantCivilStatus', 'applicantDateOfBirth',
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

    private function calculateHeuristicScore(string $text, string $position): int
    {
        if (empty(trim($text))) return 0;

        $score = 50; // Base score if text exists
        $textLower = strtolower($text);
        $posLower = strtolower($position);

        // 1. Position match in text (+15)
        if (str_contains($textLower, $posLower)) {
            $score += 15;
        }

        // 2. Keyword matching based on common job terms (+5 to +20)
        $keywords = [
            'experience' => 5,
            'skills' => 5,
            'education' => 5,
            'degree' => 5,
            'certified' => 5,
            'management' => 3,
            'technical' => 3,
            'proficient' => 2,
        ];

        foreach ($keywords as $word => $points) {
            if (str_contains($textLower, $word)) {
                $score += $points;
            }
        }

        // 3. Length heuristic (too short is suspicious)
        if (strlen($text) < 500) {
            $score -= 10;
        } elseif (strlen($text) > 2000) {
            $score += 5;
        }

        return min(max($score, 0), 75); // Cap at 75 for manual heuristic to encourage AI verification
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
            if ($trim === '') continue;

            $lower = strtolower($trim);
            $normalizedHeaderLine = preg_replace('/\s+/', '', $lower); 
            
            // Detection with flexible regex
            if (preg_match('/^(?:professional|technical|hard|soft|key|core)?\s*(?:skills|competencies|technologies|tech\s+stack)\s*[:\-]?$/i', $trim) || 
                in_array($normalizedHeaderLine, ['skills', 'technicalskills', 'coreskills', 'keycompetencies'])) {
                $current = 'skills'; continue;
            }

            if (preg_match('/^(?:work|professional|employment)?\s*(?:experience|history|background)\s*[:\-]?$/i', $trim) ||
                in_array($normalizedHeaderLine, ['experience', 'workexperience', 'employmenthistory', 'workhistory'])) {
                $current = 'experience'; continue;
            }

            if (preg_match('/^(?:educational|academic)?\s*(?:education|background|history|records|academics)\s*[:\-]?$/i', $trim) ||
                in_array($normalizedHeaderLine, ['education', 'educationalbackground', 'academichistory'])) {
                $current = 'education'; continue;
            }

            if ($current !== null) {
                $item = preg_replace('/^[\-\*•]+\s*/u', '', $trim);
                if (strlen($item) > 2 && !preg_match('/^(?:references|hobbies|personal|interests)$/i', $item)) {
                    $sections[$current][] = $item;
                    if (count($sections[$current]) > 15) $current = null; // Prevent runaway sections
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
