<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Applicants\Application;
use App\Models\Recruitment\JobListing;
use App\Jobs\ProcessResumeAI;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ApplyNow extends Component
{
    use WithFileUploads;

    public $applicantLastName, $applicantFirstName, $applicantMiddleName, $applicantSuffixName, $applicantPhone, $applicantEmail, $applicantResumeFile;
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
        $this->reset(['selectedProvince', 'selectedCity', 'selectedBarangay', 'cities', 'barangays', 'provinces']);
        if ($regionCode === '130000000') {
            $this->selectedProvince = 'NCR';
            $this->cities = Http::withoutVerifying()->get("https://psgc.cloud/api/regions/{$regionCode}/cities-municipalities")->json();
        } else {
            $this->provinces = Http::withoutVerifying()->get("https://psgc.cloud/api/regions/{$regionCode}/provinces")->json();
        }
    }

    public function updatedSelectedProvince($provinceCode)
    {
        $this->cities = Http::withoutVerifying()->get("https://psgc.cloud/api/provinces/{$provinceCode}/cities-municipalities")->json();
        $this->reset(['selectedCity', 'selectedBarangay', 'barangays']);
    }

    public function updatedSelectedCity($cityCode)
    {
        $this->barangays = Http::withoutVerifying()->get("https://psgc.cloud/api/cities-municipalities/{$cityCode}/barangays")->json();
        $this->reset(['selectedBarangay']);
    }

    public function removeResume() { $this->applicantResumeFile = null; }

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
            'selectedProvince' => 'required',
            'selectedCity' => 'required',
            'selectedBarangay' => 'required',
            'houseStreet' => 'required',
            'agreedToTerms' => 'accepted',
        ]);

        try {
            DB::beginTransaction();

            $regionName = collect($this->regions)->firstWhere('code', $this->selectedRegion)['name'] ?? $this->selectedRegion;
            $provinceName = ($this->selectedRegion === '130000000') ? 'Metro Manila' : (collect($this->provinces)->firstWhere('code', $this->selectedProvince)['name'] ?? $this->selectedProvince);
            $cityName = collect($this->cities)->firstWhere('code', $this->selectedCity)['name'] ?? $this->selectedCity;

            $path = $this->applicantResumeFile->store('resumes', 'public');

            // CRITICAL FIX: Mapping properties to Migration column names
            $application = Application::create([
                'applied_position' => $this->job->position,
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

            // Dispatch AI processing job
            ProcessResumeAI::dispatch($application);

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

    public function render() 
    { 
        return view('livewire.website.apply-now')->layout('layouts.website'); 
    }
}