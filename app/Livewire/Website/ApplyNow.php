<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Applicants\Application;
use App\Models\Recruitment\JobListing;
use App\Data\NCRAddressData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Client;

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
        } catch (\Exception $e) { $this->regions = []; }
    }

    public function updatedSelectedRegion($regionCode)
    {
        // Reset only the dependent fields
        $this->selectedProvince = null;
        $this->selectedCity = null;
        $this->selectedBarangay = null;
        $this->barangays = [];
        $this->provinces = [];
        $this->cities = [];
        
        if ($regionCode === '130000000') {
            // For NCR, populate cities using NCRAddressData
            $this->cities = collect(NCRAddressData::getCitiesAndBarangays())->map(function ($city) {
                return [
                    'code' => $city['code'],
                    'name' => $city['name']
                ];
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
        // Only process if not NCR (NCR doesn't have provinces)
        if ($this->selectedRegion !== '130000000') {
            $this->cities = Http::withoutVerifying()->get("https://psgc.cloud/api/provinces/{$provinceCode}/cities-municipalities")->json();
            $this->reset(['selectedCity', 'selectedBarangay', 'barangays']);
        }
    }

    public function updatedSelectedCity($cityCode)
    {
        $this->reset(['selectedBarangay', 'barangays']);
        
        if ($this->selectedRegion === '130000000') {
            // For NCR, use hardcoded barangay data
            $ncrBarangays = [
                '137504000' => ['Barangay 1', 'Barangay 2', 'Barangay 3', 'Barangay 4', 'Barangay 8', 'Barangay 10', 'Barangay 12', 'Barangay 18'],
                '137506000' => ['Almanza Uno', 'Pamplona Uno', 'Pamplona Dos', 'Pamplona Tres', 'Pilar Village', 'Pulang Lupa Uno', 'Pulang Lupa Dos', 'Zapote'],
                '137507000' => ['Palanan', 'Pio del Pilar', 'Pinagkaisahan', 'Bangkal', 'Bel-Air', 'Carmona', 'Dasmariñas', 'Forbes Park'],
                '137508000' => ['Panghulo', 'Tañong', 'Baritan', 'Bayan-bayanan', 'Catmon', 'Dampalit', 'Flores', 'Hulong Duhat'],
                '137509000' => ['Pag-asa', 'Plainview', 'Pleasant Hills', 'Barangka Ilaya', 'Namayan', 'Hulo', 'Central', 'Vergara'],
                '137501000' => ['Paco', 'Pandacan', 'Port Area', 'Tondo', 'Quiapo (district)', 'Santa Cruz (district)', 'Sampaloc (district)', 'Santa Ana (district)'],
                '137511000' => ['Barangka', 'Calumpang', 'Concepcion Uno', 'Concepcion Dos', 'Fortune', 'Industrial Valley', 'Jesus de la Peña', 'Tumana'],
                '137512000' => ['Alabang', 'Bayanan', 'Buli', 'Putatan', 'Poblacion', 'Sucat', 'Tunasan', 'Cupang'],
                '137513000' => ['Navotas East', 'Navotas West', 'Tangos', 'Tangos North', 'North Bay Boulevard North', 'North Bay Boulevard South', 'Daanghari', 'San Roque'],
                '137514000' => ['Baclaran', 'Don Galo', 'La Huerta', 'San Dionisio', 'Sto. Niño', 'Tambo', 'Vitalez', 'BF Homes'],
                '137515000' => ['Barangay 1', 'Barangay 10', 'Barangay 100', 'Barangay 145', 'Barangay 150', 'Barangay 175', 'Barangay 190', 'Barangay 200'],
                '137516000' => ['Palatiw', 'Pinagbuhatan', 'Pineda', 'Rosario', 'San Antonio', 'San Joaquin', 'Santa Lucia', 'Ugong'],
                '137502000' => ['Commonwealth', 'Hebreo', 'Kamuning', 'Matandang Balara', 'Payatas', 'Pinyahan', 'Project 6', 'Santolan'],
                '137517000' => ['Balong Bato', 'Corazon de Jesus', 'Greenhills', 'Little Baguio', 'Mindanao', 'Poblacion', 'Progreso', 'San Jose'],
                '137518000' => ['Palingon', 'Pitogo', 'Maharlika Village', 'Pembo', 'Pinagsama', 'Central Signal Village', 'Ususan', 'Western Bicutan'],
                '137519000' => ['Bignay', 'Dalandanan', 'Malinta', 'Mapulang Lupa', 'Palasan', 'Paso de Blas', 'Punturin', 'Karuhatan'],
                '137520000' => ['Aguho', 'Magtanggol', 'Martirez del 96', 'Poblacion', 'San Pedro', 'San Roque', 'Santa Ana', 'Santo Rosario–Kanluran']
            ];
            
            if (isset($ncrBarangays[$cityCode])) {
                $this->barangays = collect($ncrBarangays[$cityCode])->map(function($barangay) {
                    return ['name' => $barangay];
                })->toArray();
            }
        } else {
            // For other regions, use API
            $this->barangays = Http::withoutVerifying()->get("https://psgc.cloud/api/cities-municipalities/{$cityCode}/barangays")->json();
        }
    }

    public function removeResume() { 
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
            'applicantResumeFile' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'selectedRegion' => 'required',
            'selectedProvince' => 'required_if:selectedRegion,!=,130000000',
            'selectedCity' => 'required',
            'selectedBarangay' => 'required',
            'houseStreet' => 'required',
            'agreedToTerms' => 'accepted',
        ]);

        try {
            DB::beginTransaction();

            $regionName = collect($this->regions)->firstWhere('code', $this->selectedRegion)['name'] ?? $this->selectedRegion;
            $provinceName = ($this->selectedRegion === '130000000') ? 'NCR' : (collect($this->provinces)->firstWhere('code', $this->selectedProvince)['name'] ?? $this->selectedProvince);
            
            // Get city name based on region
            if ($this->selectedRegion === '130000000') {
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
                $cityName = $ncrCityNames[$this->selectedCity] ?? $this->selectedCity;
            } else {
                $cityName = collect($this->cities)->firstWhere('code', $this->selectedCity)['name'] ?? $this->selectedCity;
            }

            $path = $this->applicantResumeFile->store('resumes', 'public');

            // CRITICAL FIX: Mapping properties to Migration column names
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

            // Resume analysis integration
            $resumeContent = $this->extractResumeContent($path);
            $analysis = $this->analyzeResumeAgainstJob($resumeContent, $this->job);

            // Store analysis results in the database (new columns needed in the applications table)
            $application->update([
                'resume_score' => $analysis['score'],
                'resume_analysis' => $analysis['explanation'],
            ]);

            DB::commit();

            $this->showSuccessToast = true;
            
            // Clear inputs after success
            $this->reset([
                'applicantLastName', 'applicantFirstName', 'applicantMiddleName', 
                'applicantSuffixName', 'applicantPhone', 'applicantEmail', 
                'applicantResumeFile', 'selectedRegion', 'selectedProvince', 
                'selectedCity', 'selectedBarangay', 'houseStreet', 'agreedToTerms'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // This will show the error on your screen if the insert fails
            $this->addError('submission', 'Database Error: ' . $e->getMessage());
        }
    }

    private function extractResumeContent($filePath)
    {
        $filePath = storage_path('app/public/' . $filePath);
        $fileContent = file_get_contents($filePath);

        // Use Smalot PDF parser to extract text from PDF file
        $parser = new Parser();
        $pdf = $parser->parseContent($fileContent);
        $text = $pdf->getText();

        return $text;
    }

    private function analyzeResumeAgainstJob($resumeContent, $job)
    {
        try {
            // Rate limiting: Check if last analysis was less than 30 seconds ago
            if ($this->lastAnalysisTime && (time() - $this->lastAnalysisTime) < 30) {
                return ['score' => 0, 'explanation' => 'Please wait before requesting another analysis.'];
            }
            
            $this->lastAnalysisTime = time();

            $prompt = "Given the following job description: \n" . $job->description . "\n\n" .
                      "And the following resume content: \n" . $resumeContent . "\n\n" .
                      "Rate the relevance of the resume to the job description on a scale of 0 to 100 and provide a brief explanation.";

            // Create custom client with SSL verification disabled
            $client = \OpenAI::factory()
                ->withApiKey(env('OPENAI_API_KEY'))
                ->withHttpHeader('Content-Type', 'application/json')
                ->withHttpClient(new \GuzzleHttp\Client([
                    'verify' => false,
                    'timeout' => 30,
                ]))
                ->make();

            $response = $client->completions()->create([
                'model' => 'gpt-3.5-turbo-instruct',
                'prompt' => $prompt,
                'max_tokens' => 200,
            ]);

            $analysis = $response['choices'][0]['text'] ?? null;

            if ($analysis) {
                // Extract score and explanation from the response
                preg_match('/\d+/', $analysis, $scoreMatch);
                $score = $scoreMatch[0] ?? 0;
                $explanation = str_replace($score, '', $analysis);

                return [
                    'score' => (int) $score,
                    'explanation' => trim($explanation),
                ];
            }

            return ['score' => 0, 'explanation' => 'Unable to analyze resume.'];

        } catch (\OpenAI\Exceptions\RateLimitException $e) {
            // Handle rate limit exceeded
            return ['score' => 0, 'explanation' => 'AI analysis temporarily unavailable due to rate limits. Please try again later.'];
        } catch (\OpenAI\Exceptions\ErrorException $e) {
            // Handle other OpenAI errors
            return ['score' => 0, 'explanation' => 'AI analysis service temporarily unavailable.'];
        } catch (\Exception $e) {
            // Handle general errors
            return ['score' => 0, 'explanation' => 'Unable to analyze resume at this time.'];
        }
    }

    public function render() 
    { 
        return view('livewire.website.apply-now')->layout('layouts.website'); 
    }
}