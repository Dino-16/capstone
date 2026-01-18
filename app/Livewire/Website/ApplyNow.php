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

class ApplyNow extends Component
{
    use WithFileUploads;

    public $applicantLastName, $applicantFirstName, $applicantMiddleName, $applicantSuffixName, $applicantPhone, $applicantEmail, $applicantResumeFile;
    public $isUploading = false;
    public $lastAnalysisTime = null;
    public $job, $agreedToTerms = false, $showTerms = false, $showSuccessToast = false;
    public $regions = [], $provinces = [], $cities = [], $barangays = [];
    public $selectedRegion, $selectedProvince, $selectedCity, $selectedBarangay, $houseStreet;

    public function mount($id)
    {
        $this->job = JobListing::findOrFail($id);
        try {
            $this->regions = Http::withoutVerifying()->get('https://psgc.cloud/api/regions')->json();
        } catch (\Exception $e) {
            $this->regions = [];
        }
    }

    public function updatedSelectedRegion($regionCode)
    {
        $this->selectedProvince = null;
        $this->selectedCity = null;
        $this->selectedBarangay = null;
        $this->barangays = [];
        $this->provinces = [];
        $this->cities = [];

        if ($regionCode === '130000000') {
            $this->cities = collect(NCRAddressData::getCitiesAndBarangays())->map(function ($city) {
                return ['code' => $city['code'], 'name' => $city['name']];
            })->toArray();
        } else {
            try {
                $this->provinces = Http::withoutVerifying()->get("https://psgc.cloud/api/regions/{$regionCode}/provinces")->json();
            } catch (\Exception $e) {
                $this->provinces = [];
            }
        }
    }

    public function updatedSelectedProvince($provinceCode)
    {
        if ($this->selectedRegion !== '130000000') {
            $this->cities = Http::withoutVerifying()->get("https://psgc.cloud/api/provinces/{$provinceCode}/cities-municipalities")->json();
            $this->reset(['selectedCity', 'selectedBarangay', 'barangays']);
        }
    }

    public function updatedSelectedCity($cityCode)
    {
        $this->reset(['selectedBarangay', 'barangays']);

        if ($this->selectedRegion === '130000000') {
            $ncrBarangays = [
                // NCR city to barangay mapping (same as before) ...
            ];

            if (isset($ncrBarangays[$cityCode])) {
                $this->barangays = collect($ncrBarangays[$cityCode])->map(fn($barangay) => ['name' => $barangay])->toArray();
            }
        } else {
            $this->barangays = Http::withoutVerifying()->get("https://psgc.cloud/api/cities-municipalities/{$cityCode}/barangays")->json();
        }
    }

    public function removeResume()
    {
        $this->applicantResumeFile = null;
        $this->isUploading = false;
    }

    public function uploading($property, $value)
    {
        $this->isUploading = true;
    }

    public function updated($property)
    {
        if ($property === 'applicantResumeFile' && $this->applicantResumeFile) {
            $this->isUploading = false;
        }
    }

    public function submitApplication()
    {
        $this->validate([
            'applicantLastName' => 'required|max:50',
            'applicantFirstName' => 'required|max:50',
            'applicantMiddleName' => 'required|max:50',
            'applicantEmail' => 'required|email',
            'applicantPhone' => 'required',
            'applicantResumeFile' => 'required|file|mimes:pdf|max:2048',
            'selectedRegion' => 'required',
            'selectedProvince' => 'required_if:selectedRegion,!=,130000000',
            'selectedCity' => 'required',
            'selectedBarangay' => 'required',
            'houseStreet' => 'required',
            'agreedToTerms' => 'accepted',
        ]);

        DB::beginTransaction();

        try {
            $regionName = collect($this->regions)->firstWhere('code', $this->selectedRegion)['name'] ?? $this->selectedRegion;
            $provinceName = ($this->selectedRegion === '130000000') ? 'NCR' : (collect($this->provinces)->firstWhere('code', $this->selectedProvince)['name'] ?? $this->selectedProvince);
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

                // First try to parse PDF to text
                try {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($filePath);
                    $resumeContent = $pdf->getText();
                } catch (\Exception $e) {
                    \Log::warning("PDF parsing failed for application ID {$application->id}, falling back to raw file content: {$e->getMessage()}");
                    $resumeContent = @file_get_contents($filePath) ?: null;
                }

                if (!empty($resumeContent)) {
                    $prompt = "You are an AI that extracts structured data from resumes for automated screening.\n\nJob position: {$application->applied_position}.\n\nResume content:\n{$resumeContent}\n\nReturn a single valid JSON object with EXACTLY these keys and types:\n- age: number (best estimate of candidate age, or null)\n- gender: string (e.g. 'Male', 'Female', or 'Unknown')\n- skills: array of strings (each string is a single skill or technology, e.g. 'PHP', 'Laravel', 'Customer Service'). Always return at least 3 skills; if not explicit, infer from context or use generic skills like 'Communication', 'Teamwork'.\n- summary: string (2-4 sentence summary of the candidate)\n- experience: array of strings (each string is one job or role, e.g. 'Software Developer at Company A (2019-2022)'). Always return at least 1 experience item; if no job is given, use a best-guess like 'Work experience not clearly specified'.\n- education: array of strings (each string is one degree or education item, e.g. 'BS Computer Science - University X (2018)'). Always return at least 1 education item; if missing, use a placeholder like 'Education not clearly specified'.\n- score: number from 0 to 100 (overall suitability rating for the job)\n- qualification: string, either 'Qualified' or 'Not Qualified'.\n\nNever leave skills, experience, or education as empty arrays; always infer reasonable values or use an 'not clearly specified' placeholder string.\n\nExample JSON format (do NOT wrap in markdown):\n{\n  \"age\": 25,\n  \"gender\": \"Male\",\n  \"skills\": [\"PHP\", \"Laravel\", \"Customer Support\"],\n  \"summary\": \"Short summary here...\",\n  \"experience\": [\"Software Developer - Company A (2019-2022)\"],\n  \"education\": [\"BS Computer Science - University X (2018)\"],\n  \"score\": 85,\n  \"qualification\": \"Qualified\"\n}.\n\nReturn ONLY the JSON object, with no extra text, labels, or explanations before or after.";

                    // Analyze resume
                    $response = OpenAI::chat()->create([
                        'model' => 'gpt-3.5-turbo',
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a helpful resume analyzer.'],
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'max_tokens' => 500,
                    ]);

                    $text = trim($response['choices'][0]['message']['content'] ?? '{}');

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
                    }
                } else {
                    \Log::warning('Resume content is empty after parsing and fallback', [
                        'application_id' => $application->id,
                        'file_path' => $filePath,
                    ]);
                }

            } catch (\Exception $e) {
                \Log::error("Failed AI resume analysis for application ID {$application->id}: {$e->getMessage()}");
            }

            // Parse resume text sections and age as an additional fallback
            $parsedSections = $this->parseResumeSections($resumeContent ?? '');
            $parsedSkills = $parsedSections['skills'] ?? [];
            $parsedExperience = $parsedSections['experience'] ?? [];
            $parsedEducation = $parsedSections['education'] ?? [];

            $ageFromResume = $this->extractAgeFromResume($resumeContent ?? '');

            // Normalize AI data so important fields are never null/empty
            $age = $aiData['age'] ?? $aiData['Age'] ?? $ageFromResume;
            if ($age === null || $age === '') {
                $age = 0; // fallback when AI cannot infer age
            }

            $gender = $aiData['gender'] ?? $aiData['Gender'] ?? null;
            if (empty($gender)) {
                $gender = 'Unknown';
            }

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

            $summary = $aiData['summary'] ?? $aiData['Summary'] ?? $resumeContent;

            $ratingScore = $aiData['score'] ?? $aiData['Score'] ?? 0;
            $qualificationStatus = $aiData['qualification'] ?? $aiData['Qualification'] ?? 'Not Qualified';

            // Save filtered resume
            $application->filteredResume()->create([
                'age'                  => $age,
                'gender'               => $gender,
                'skills'               => $skills,
                'ai_summary'           => $summary,
                'experience'           => $experience,
                'education'            => $education,
                'rating_score'         => $ratingScore,
                'qualification_status' => $qualificationStatus,
            ]);

            $this->showSuccessToast = true;

            $this->reset([
                'applicantLastName', 'applicantFirstName', 'applicantMiddleName',
                'applicantSuffixName', 'applicantPhone', 'applicantEmail',
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
        if ($region === '130000000') {
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
