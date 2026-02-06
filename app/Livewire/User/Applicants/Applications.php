<?php

namespace App\Livewire\User\Applicants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Applicants\Application;
use App\Models\Applicants\FilteredResume;
use App\Models\Applicants\Candidate;
use App\Models\Recruitment\JobListing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Applications extends Component
{   
    use WithPagination;

    public $search;
    public $qualificationFilter = '';
    
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
    public $schedulingApplicationId;
    public $interview_date;
    public $interview_time;
    public $approvedFacilities = [];
    public $selectedFacility = '';
    public $applicantData = [];

    // Draft tool properties
    public $showDraftModal = false;
    public $draftingApplicationId = null;
    public $draftApplicantName = '';
    public $currentPosition = '';
    public $suggestedPosition = '';
    public $draftReason = '';
    public $availablePositions = [];

    // Reset pagination on mount to ensure we see data
    public function mount()
    {
        $this->resetPage();
    }

    // Pagination Page when Filtered
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function UpdatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedQualificationFilter()
    {
        $this->resetPage();
    }

    // Clear Message Status
    public function clearStatus()
    {
        session()->forget('status');
    }

    // Get AI Rating Description based on score
    private function getRatingDescription($score)
    {
        if ($score === null) return null;
        
        if ($score >= 90) {
            return 'Exceptional - Outstanding qualifications, exceeds all requirements';
        } elseif ($score >= 80) {
            return 'Highly Qualified - Strong technical background and experience';
        } elseif ($score >= 70) {
            return 'Qualified - Meets requirements with good potential';
        } elseif ($score >= 60) {
            return 'Moderately Qualified - Meets basic requirements';
        } elseif ($score >= 50) {
            return 'Marginally Qualified - Some gaps in qualifications';
        } else {
            return 'Not Qualified - Does not meet minimum requirements';
        }
    }

    // Get rating badge color (aligned with AI Rating Scale legend)
    private function getRatingBadgeColor($score)
    {
        if ($score === null) return 'secondary';
        
        if ($score >= 80) {
            return 'success';  // 90-100: Exceptional, 80-89: Highly Qualified
        } elseif ($score >= 60) {
            return 'warning';  // 70-79: Qualified, 60-69: Moderately Qualified
        } else {
            return 'danger';   // 50-59: Marginally Qualified, 0-49: Not Qualified
        }
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
        
        // Add rating description
        if (isset($this->filteredResume['rating_score'])) {
            $this->filteredResume['rating_description'] = $this->getRatingDescription($this->filteredResume['rating_score']);
            $this->filteredResume['rating_badge_color'] = $this->getRatingBadgeColor($this->filteredResume['rating_score']);
        }
        
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
            'edit_rating_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'edit_qualification_status' => ['nullable', 'string', 'max:50'],
        ]);

        $skills = array_values(array_filter(array_map('trim', $this->edit_skills ?? []), fn ($v) => $v !== ''));
        $experience = array_values(array_filter(array_map('trim', $this->edit_experience ?? []), fn ($v) => $v !== ''));
        $education = array_values(array_filter(array_map('trim', $this->edit_education ?? []), fn ($v) => $v !== ''));

        // Auto-set qualification status based on score
        $qualificationStatus = $this->edit_qualification_status;
        
        // If user didn't manually select a status, OR if they are editing limits, recalculate based on score
        if (empty($qualificationStatus) && $this->edit_rating_score !== null) {
            if ($this->edit_rating_score >= 90) {
                $qualificationStatus = 'Exceptional';
            } elseif ($this->edit_rating_score >= 80) {
                $qualificationStatus = 'Highly Qualified';
            } elseif ($this->edit_rating_score >= 70) {
                $qualificationStatus = 'Qualified';
            } elseif ($this->edit_rating_score >= 60) {
                $qualificationStatus = 'Moderately Qualified';
            } elseif ($this->edit_rating_score >= 50) {
                $qualificationStatus = 'Marginally Qualified';
            } else {
                $qualificationStatus = 'Not Qualified';
            }
        }

        FilteredResume::updateOrCreate(
            ['application_id' => $this->editingApplicationId],
            [
                'skills' => $skills,
                'experience' => $experience,
                'education' => $education,
                'rating_score' => $this->edit_rating_score,
                'qualification_status' => $qualificationStatus,
            ]
        );

        session()->flash('message', 'Filtered resume updated successfully!');
        $this->closeEditFilteredResumeModal();
    }

    // Draft tool methods - for pivoting candidates to alternative roles
    public function openDraftModal($applicationId)
    {
        $application = Application::find($applicationId);
        if (!$application) {
            return;
        }

        $this->draftingApplicationId = $applicationId;
        $this->draftApplicantName = $application->first_name . ' ' . $application->middle_name . ' ' . $application->last_name;
        $this->currentPosition = $application->applied_position;
        $this->suggestedPosition = '';
        $this->draftReason = '';

        // Get available positions from local database (excluding current)
        $localPositions = JobListing::where('position', '!=', $application->applied_position)
            ->pluck('position')
            ->toArray();

        // Fetch positions from HR2 API
        $apiPositions = [];
        try {
            $response = Http::withoutVerifying()->get('https://hr2.jetlougetravels-ph.com/api/positions');
            if ($response->successful()) {
                $apiData = $response->json();
                // Extract position names from API response
                if (is_array($apiData)) {
                    foreach ($apiData as $position) {
                        $positionName = $position['position_name'] ?? $position['name'] ?? null;
                        if ($positionName && $positionName !== $application->applied_position) {
                            $apiPositions[] = $positionName;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently handle API errors
        }

        // Merge and remove duplicates, then sort
        $this->availablePositions = collect(array_merge($localPositions, $apiPositions))
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        $this->showDraftModal = true;
    }

    public function closeDraftModal()
    {
        $this->showDraftModal = false;
        $this->draftingApplicationId = null;
        $this->draftApplicantName = '';
        $this->currentPosition = '';
        $this->suggestedPosition = '';
        $this->draftReason = '';
        $this->availablePositions = [];
    }

    public function pivotToNewRole()
    {
        $this->validate([
            'suggestedPosition' => ['required', 'string'],
            'draftReason' => ['nullable', 'string', 'max:500'],
        ]);

        $application = Application::find($this->draftingApplicationId);
        if (!$application) {
            return;
        }

        $oldPosition = $application->applied_position;
        $application->applied_position = $this->suggestedPosition;
        $application->save();

        session()->flash('message', "Applicant pivoted from '{$oldPosition}' to '{$this->suggestedPosition}' successfully!");
        $this->closeDraftModal();
    }

    // Schedule interview methods
    public function openScheduleModal($applicationId)
    {
        $application = Application::find($applicationId);
        if (!$application) {
            return;
        }

        // Get the filtered resume data for rating info
        $filteredResume = FilteredResume::where('application_id', $applicationId)->first();
        $ratingScore = $filteredResume ? $filteredResume->rating_score : null;

        $this->schedulingApplicationId = $applicationId;
        $this->applicantData = [
            'name' => $application->first_name . ' ' . $application->middle_name . ' ' . $application->last_name . ' ' . $application->suffix_name,
            'email' => $application->email,
            'phone' => $application->phone,
            'position' => $application->applied_position,
            'department' => $application->department,
            'rating_score' => $ratingScore,
            'rating_description' => $this->getRatingDescription($ratingScore),
            'rating_badge_color' => $this->getRatingBadgeColor($ratingScore),
        ];

        // Fetch Approved Facilities for Location Dropdown
        $this->approvedFacilities = [];
        try {
            $response = Http::withoutVerifying()->get('https://facilities-admin.jetlougetravels-ph.com/reservation_status_api.php');
            if ($response->successful()) {
                $data = $response->json();
                $reservations = $data['reservations'] ?? [];
                
                $this->approvedFacilities = collect($reservations)
                    ->filter(function($r) {
                        $status = strtolower($r['status'] ?? '');
                        if ($status !== 'approved') {
                            return false;
                        }

                        $name = strtolower($r['requested_by'] ?? $r['full_name'] ?? '');
                        $dept = strtolower($r['department_name'] ?? $r['department'] ?? '');
                        
                        // Visible if status is approved AND name or department contains HR keywords
                        $hrKeywords = ['hr staff', 'hr manager', 'hr', 'human resource'];
                        
                        $matchesName = false;
                        foreach ($hrKeywords as $keyword) {
                            if (str_contains($name, $keyword)) {
                                $matchesName = true;
                                break;
                            }
                        }

                        $matchesDept = false;
                        foreach ($hrKeywords as $keyword) {
                            if (str_contains($dept, $keyword)) {
                                $matchesDept = true;
                                break;
                            }
                        }
                        
                        return $matchesName || $matchesDept;
                    })
                    ->map(function($r) {
                        return [
                            'id' => $r['request_id'],
                            'name' => $r['facility_name'],
                            'location' => $r['location'],
                            'date' => $r['booking_date'],
                            'start_time' => $r['start_time'],
                            'end_time' => $r['end_time'],
                            'details' => $r['facility_name'] . ' (' . $r['location'] . ') - ' . $r['booking_date'] . ' ' . $r['start_time']
                        ];
                    })
                    ->values()
                    ->toArray();
            }
        } catch (\Exception $e) {
            // calculated silently
        }

        $this->showScheduleModal = true;
    }

    public function updatedSelectedFacility($value)
    {
        if ($value) {
            $facility = collect($this->approvedFacilities)->firstWhere('id', $value);
            if ($facility) {
                $this->interview_date = $facility['date'];
                $this->interview_time = $facility['start_time']; 
            }
        }
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
            'selectedFacility' => ['required'],
            'interview_date' => ['required', 'date', 'after_or_equal:today'],
            'interview_time' => ['required', 'string'],
        ], [
            'selectedFacility.required' => 'Please select an interview location / facility.'
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

        // Get rating info
        $ratingScore = $resumeArray['rating_score'] ?? null;
        $ratingDescription = $ratingScore ? $this->getRatingDescription($ratingScore) : null;

        // Create candidate record with all enhanced fields
        $candidate = Candidate::create([
            'candidate_name' => $application->first_name . ' ' . $application->middle_name . ' ' . $application->last_name . ' ' . $application->suffix_name,
            'candidate_email' => $application->email,
            'candidate_phone' => $application->phone,
            'candidate_sex' => $application->gender ?? null,
            'candidate_birth_date' => $application->date_of_birth ?? null,
            'candidate_civil_status' => $application->civil_status ?? null,
            'candidate_age' => $application->age ?? null,
            'candidate_region' => $application->region ?? null,
            'candidate_province' => $application->province ?? null,
            'candidate_city' => $application->city ?? null,
            'candidate_barangay' => $application->barangay ?? null,
            'candidate_house_street' => $application->house_street ?? null,
            'applied_position' => $application->applied_position,
            'department' => $application->department,
            'rating_score' => $ratingScore,
            'rating_description' => $this->getRatingDescription($ratingScore),
            'skills' => $resumeArray['skills'] ?? [],
            'experience' => $resumeArray['experience'] ?? [],
            'education' => $resumeArray['education'] ?? [],
            'resume_url' => $application->resume_path ? Storage::url($application->resume_path) : null,
            'status' => 'scheduled',
            'interview_schedule' => $interviewDateTime,
            'scheduling_token' => Str::random(32),
            'self_scheduled' => false,
            'interview_result' => 'pending',
            'contract_status' => 'pending',
        ]);

        // Delete the filtered resume record
        if ($filteredResume) {
            $filteredResume->delete();
        }

        // Delete the application record
        $application->delete();

        $this->closeScheduleModal();
        session()->flash('message', 'Candidate scheduled successfully and moved to candidates list.');
    }

    public function toggleStatus($applicationId)
    {
        $application = Application::findOrFail($applicationId);
        $newStatus = $application->status === 'active' ? 'drafted' : 'active';
        $application->update(['status' => $newStatus]);
        
        session()->flash('status', 'Application marked as ' . ucfirst($newStatus));
    }

    public $showDrafts = false; // Toggle for draft view

    public function openDrafts()
    {
        $this->showDrafts = true;
        $this->resetPage();
    }

    public function closeDrafts()
    {
        $this->showDrafts = false;
        $this->resetPage();
    }

    public function draft($id)
    {
        $app = Application::findOrFail($id);
        $app->update(['status' => 'drafted']);
        session()->flash('status', 'Application moved to drafts.');
    }
    
    public function restore($id)
    {
        $app = Application::findOrFail($id);
        $app->update(['status' => 'active']);
        session()->flash('status', 'Application restored to active list.');
    }

    public function delete($id)
    {
        // Security check: Only Super Admin can delete
        if (strcasecmp(session('user.position'), 'Super Admin') !== 0) {
            session()->flash('error', 'Unauthorized: Only Super Admin can delete applications.');
            return;
        }

        $app = Application::findOrFail($id);
        
        // Delete associated filtered resume if exists
        FilteredResume::where('application_id', $id)->delete();
        
        // Delete application record
        $app->delete();
        
        session()->flash('status', 'Application deleted successfully.');
    }

    public function exportData()
    {
        $export = new \App\Exports\Applicants\ApplicationsExport();
        return $export->export();
    }

    public function render()
    {
        $query = Application::query()
            ->select('applications.*')
            ->leftJoin('filtered_resumes', 'applications.id', '=', 'filtered_resumes.application_id')
            ->latest('applications.created_at');

        // Status Filter
        if ($this->showDrafts) {
            $query->where('applications.status', 'drafted');
        } else {
            $query->where('applications.status', '!=', 'drafted');
        }

        // Search Filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('applications.first_name', 'like', "%{$this->search}%")
                ->orWhere('applications.last_name', 'like', "%{$this->search}%")
                ->orWhere('applications.email', 'like', "%{$this->search}%")
                ->orWhere('applications.applied_position', 'like', "%{$this->search}%")
                ->orWhere('applications.department', 'like', "%{$this->search}%");
            });
        }

        // Qualification Filter
        if ($this->qualificationFilter) {
            if ($this->qualificationFilter === 'Pending Review') {
                $query->whereNull('filtered_resumes.qualification_status');
            } else {
                $query->where('filtered_resumes.qualification_status', $this->qualificationFilter);
            }
        }

        $applications = $query->paginate(10);
        
        // Load qualification status and rating info for each application (populating for view)
        foreach ($applications as $app) {
            $filteredResume = FilteredResume::where('application_id', $app->id)->first();
            $app->qualification_status = $filteredResume ? $filteredResume->qualification_status : null;
            $app->rating_score = $filteredResume ? $filteredResume->rating_score : null;
            $app->rating_description = $filteredResume ? $this->getRatingDescription($filteredResume->rating_score) : null;
            $app->rating_badge_color = $filteredResume ? $this->getRatingBadgeColor($filteredResume->rating_score) : 'secondary';
        }

        return view('livewire.user.applicants.applications', [
            'applications' => $applications,
        ])->layout('layouts.app');
    }
}

