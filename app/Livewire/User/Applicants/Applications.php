<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Applicants\Application;
use App\Models\Applicants\FilteredResume;
use App\Models\Applicants\Candidate;
use Illuminate\Support\Facades\Storage;

class Applications extends Component
{   
    use WithPagination;

    public $search;
    
    // Modal properties for filtered resume
    public $showFilteredResumeModal = false;
    public $filteredResume = [];
    public $applicantName = '';

    public $showEditFilteredResumeModal = false;
    public $editingApplicationId = null;
    public $resumeUrl = null;

    public $edit_skills = [];
    public $edit_experience = [];
    public $edit_education = [];
    public $edit_rating_score = null;
    public $edit_qualification_status = null;

    // Interview scheduling properties
    public $showScheduleModal = false;
    public $schedulingApplicationId = null;
    public $interview_date = '';
    public $interview_time = '';
    public $applicantData = [];

    // Pagination Page when Filtered
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function UpdatedStatusFilter()
    {
        $this->resetPage();
    }

    // Clear Message Status
    public function clearStatus()
    {
        session()->forget('status');
    }

    // View filtered resume
    public function viewFilteredResume($applicationId)
    {
        logger('View filtered resume called for ID: ' . $applicationId);
        $application = Application::find($applicationId);
        if (!$application) {
            logger('Application not found');
            return;
        }
        
        $this->applicantName = $application->first_name . ' ' . $application->last_name;
        
        $resume = FilteredResume::where('application_id', $applicationId)->first();
        $this->filteredResume = $resume ? $resume->toArray() : [];
        $this->showFilteredResumeModal = true;
        logger('Modal should be showing: ' . $this->showFilteredResumeModal);
    }

    // Close filtered resume modal
    public function closeFilteredResumeModal()
    {
        $this->showFilteredResumeModal = false;
        $this->filteredResume = [];
        $this->applicantName = '';
    }

    public function openEditFilteredResume($applicationId)
    {
        logger('Edit filtered resume called for ID: ' . $applicationId);
        $application = Application::find($applicationId);
        if (!$application) {
            logger('Application not found');
            return;
        }

        $this->editingApplicationId = $application->id;
        $this->applicantName = $application->first_name . ' ' . $application->last_name;
        $this->resumeUrl = $application->resume_path ? Storage::url($application->resume_path) : null;

        $resume = FilteredResume::where('application_id', $applicationId)->first();
        $resumeArray = $resume ? $resume->toArray() : [];

        $this->edit_rating_score = $resumeArray['rating_score'] ?? null;
        $this->edit_qualification_status = $resumeArray['qualification_status'] ?? null;

        $skills = $resumeArray['skills'] ?? [];
        $this->edit_skills = is_array($skills) ? array_values($skills) : [];
        if (empty($this->edit_skills)) {
            $this->edit_skills = [''];
        }

        $experience = $resumeArray['experience'] ?? [];
        $this->edit_experience = is_array($experience) ? array_values($experience) : [];
        if (empty($this->edit_experience)) {
            $this->edit_experience = [''];
        }

        $education = $resumeArray['education'] ?? [];
        $this->edit_education = is_array($education) ? array_values($education) : [];
        if (empty($this->edit_education)) {
            $this->edit_education = [''];
        }

        $this->showEditFilteredResumeModal = true;
        logger('Edit modal should be showing: ' . $this->showEditFilteredResumeModal);
    }

    public function closeEditFilteredResumeModal()
    {
        $this->showEditFilteredResumeModal = false;
        $this->editingApplicationId = null;
        $this->resumeUrl = null;

        $this->edit_skills = [];
        $this->edit_experience = [];
        $this->edit_education = [];
        $this->edit_rating_score = null;
        $this->edit_qualification_status = null;
    }

    public function addSkill(): void
    {
        $this->edit_skills[] = '';
    }

    public function removeSkill(int $index): void
    {
        unset($this->edit_skills[$index]);
        $this->edit_skills = array_values($this->edit_skills);
        if (empty($this->edit_skills)) {
            $this->edit_skills = [''];
        }
    }

    public function addExperience(): void
    {
        $this->edit_experience[] = '';
    }

    public function removeExperience(int $index): void
    {
        unset($this->edit_experience[$index]);
        $this->edit_experience = array_values($this->edit_experience);
        if (empty($this->edit_experience)) {
            $this->edit_experience = [''];
        }
    }

    public function addEducation(): void
    {
        $this->edit_education[] = '';
    }

    public function removeEducation(int $index): void
    {
        unset($this->edit_education[$index]);
        $this->edit_education = array_values($this->edit_education);
        if (empty($this->edit_education)) {
            $this->edit_education = [''];
        }
    }

    public function updateFilteredResume()
    {
        $this->validate([
            'editingApplicationId' => ['required', 'integer', 'exists:applications,id'],
            'edit_skills' => ['nullable', 'array'],
            'edit_skills.*' => ['nullable', 'string'],
            'edit_experience' => ['nullable', 'array'],
            'edit_experience.*' => ['nullable', 'string'],
            'edit_education' => ['nullable', 'array'],
            'edit_education.*' => ['nullable', 'string'],
            'edit_rating_score' => ['nullable', 'numeric'],
            'edit_qualification_status' => ['nullable', 'string', 'max:50'],
        ]);

        $skills = array_values(array_filter(array_map('trim', $this->edit_skills ?? []), fn ($v) => $v !== ''));
        $experience = array_values(array_filter(array_map('trim', $this->edit_experience ?? []), fn ($v) => $v !== ''));
        $education = array_values(array_filter(array_map('trim', $this->edit_education ?? []), fn ($v) => $v !== ''));

        FilteredResume::updateOrCreate(
            ['application_id' => $this->editingApplicationId],
            [
                'skills' => $skills,
                'experience' => $experience,
                'education' => $education,
                'rating_score' => $this->edit_rating_score,
                'qualification_status' => $this->edit_qualification_status,
            ]
        );

        $this->closeEditFilteredResumeModal();
    }

    // Schedule interview methods
    public function openScheduleModal($applicationId)
    {
        $application = Application::find($applicationId);
        if (!$application) {
            return;
        }

        $this->schedulingApplicationId = $applicationId;
        $this->applicantData = [
            'name' => $application->first_name . ' ' . $application->middle_name . ' ' . $application->last_name . ' ' . $application->suffix_name,
            'email' => $application->email,
            'phone' => $application->phone,
            'position' => $application->applied_position,
            'department' => $application->department,
        ];
        $this->showScheduleModal = true;
    }

    public function closeScheduleModal()
    {
        $this->showScheduleModal = false;
        $this->schedulingApplicationId = null;
        $this->interview_date = '';
        $this->interview_time = '';
        $this->applicantData = [];
    }

    public function scheduleInterview()
    {
        $this->validate([
            'interview_date' => ['required', 'date', 'after_or_equal:today'],
            'interview_time' => ['required', 'string'],
        ]);

        $application = Application::find($this->schedulingApplicationId);
        if (!$application) {
            return;
        }

        // Get filtered resume data
        $filteredResume = FilteredResume::where('application_id', $this->schedulingApplicationId)->first();
        $resumeArray = $filteredResume ? $filteredResume->toArray() : [];

        // Combine date and time
        $interviewDateTime = $this->interview_date . ' ' . $this->interview_time;

        // Create candidate record
        Candidate::create([
            'candidate_name' => $application->first_name . ' ' . $application->middle_name . ' ' . $application->last_name . ' ' . $application->suffix_name,
            'candidate_email' => $application->email,
            'candidate_phone' => $application->phone,
            'candidate_sex' => $application->sex ?? null,
            'candidate_birth_date' => $application->birth_date ?? null,
            'candidate_civil_status' => $application->civil_status ?? null,
            'candidate_age' => $application->age ?? null,
            'candidate_region' => $application->region ?? null,
            'candidate_province' => $application->province ?? null,
            'candidate_city' => $application->city ?? null,
            'candidate_barangay' => $application->barangay ?? null,
            'candidate_house_street' => $application->house_street ?? null,
            'skills' => $resumeArray['skills'] ?? [],
            'experience' => $resumeArray['experience'] ?? [],
            'education' => $resumeArray['education'] ?? [],
            'resume_url' => $application->resume_path ? Storage::url($application->resume_path) : null,
            'status' => 'scheduled',
            'interview_schedule' => $interviewDateTime,
        ]);

        // Delete the application record
        $application->delete();

        $this->closeScheduleModal();
        session()->flash('message', 'Candidate scheduled successfully and application moved to candidates.');
    }

    public function render()
    {
        $query = Application::query()->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                ->orWhere('last_name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('applied_position', 'like', "%{$this->search}%")
                ->orWhere('department', 'like', "%{$this->search}%");
            });
        }

        $applications = $query->paginate(10);
        
        // Load qualification status for each application
        foreach ($applications as $app) {
            $filteredResume = FilteredResume::where('application_id', $app->id)->first();
            $app->qualification_status = $filteredResume ? $filteredResume->qualification_status : null;
        }

        return view('livewire.user.applicants.applications', [
            'applications' => $applications,
        ])->layout('layouts.app');
    }
}
