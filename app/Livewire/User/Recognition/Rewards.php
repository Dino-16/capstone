<?php

namespace App\Livewire\User\Recognition;

use App\Models\Recognition\Reward;
use Livewire\Component;
use Livewire\WithPagination;

class Rewards extends Component
{
    use WithPagination;
    use \App\Livewire\Traits\HandlesToasts;

    public $search = '';
    public $showModal = false;
    public $editing = false;
    public $rewardId = null;
    public $typeFilter = '';
    public $statusFilter = '';
    public $showDrafts = false;
    public $eligibleEmployees = [];
    public $loadingEligible = false;

    // Form fields
    public $name = '';
    public $description = '';
    public $type = 'monetary';
    public $benefits = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'type' => 'required|in:monetary,non_monetary',
        'benefits' => 'required|string',
    ];

    public function mount()
    {
        $this->resetForm();
        $this->loadEligibleEmployees();
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'description', 'type', 'benefits', 
            'editing', 'rewardId', 'typeFilter', 'statusFilter'
        ]);
        $this->type = 'monetary';
        $this->typeFilter = '';
        $this->statusFilter = '';
    }



    public function editReward($id)
    {
        $reward = Reward::find($id);
        
        if ($reward) {
            $this->rewardId = $id;
            $this->name = $reward->name;
            $this->description = $reward->description;
            $this->type = $reward->type;
            $this->benefits = $reward->benefits;
            $this->editing = true;
            $this->showModal = true;
        }
    }



    public function updateReward()
    {
        $this->validate();

        $reward = Reward::find($this->rewardId);
        
        if ($reward) {
            $reward->update([
                'name' => $this->name,
                'description' => $this->description,
                'type' => $this->type,
                'benefits' => $this->benefits,
            ]);

            $this->toast('Reward updated successfully!');
            $this->showModal = false;
        }
    }

    public function draft($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->status = 'draft';
        $reward->save();
        $this->toast('Reward drafted successfully!');
    }

    public function restore($id) 
    {
        $reward = Reward::findOrFail($id);
        $reward->status = 'active';
        $reward->save();    
        $this->toast('Reward restored successfully!');
    }

    public $showDeleteModal = false;
    public $rewardIdToDelete = null;

    public function confirmDelete($id)
    {
        $this->rewardIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteReward()
    {
        if (!in_array(session('user.position'), ['Super Admin', 'HR Manager'])) {
            $this->toast('Unauthorized action.', 'error');
            return;
        }

        if ($this->rewardIdToDelete) {
             $reward = Reward::findOrFail($this->rewardIdToDelete);
             $reward->delete();
             $this->toast('Reward deleted successfully!');
        }

        $this->showDeleteModal = false;
        $this->rewardIdToDelete = null;
    }

    // Drafted Section
    public function openDraft()
    {
        $this->showDrafts = true;
        $this->resetPage();
    }

   public function showAll()
    {
        $this->showDrafts = false;
        $this->resetPage();
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Recognition\RewardsExport(), 'rewards.xlsx');
    }

        public function loadEligibleEmployees()
    {
        $this->loadingEligible = true;
        
        try {
            // 1. Fetch Employees
            $empResponse = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()->get('http://hr4.jetlougetravels-ph.com/api/employees', ['per_page' => 1000]);
            $employees = $empResponse->successful() ? ($empResponse->json()['data'] ?? $empResponse->json()) : [];

            // 2. Fetch Attendance
            $attnResponse = \Illuminate\Support\Facades\Http::timeout(10)->withoutVerifying()->get('https://hr3.jetlougetravels-ph.com/api/attendance');
            $attendance = $attnResponse->successful() ? ($attnResponse->json()['data'] ?? $attnResponse->json()) : [];
            if (isset($attendance['data'])) $attendance = $attendance['data']; // Handle paginated wrapper

            // 3. Process
            $now = \Carbon\Carbon::now();
            $eligible = [];

            foreach ($employees as $employee) {
                $name = $employee['full_name'] ?? ($employee['first_name'] . ' ' . ($employee['last_name'] ?? '')) ?? 'N/A';
                $email = $employee['email'] ?? '';
                $hireDate = !empty($employee['date_hired']) ? \Carbon\Carbon::parse($employee['date_hired']) : null;
                
                // Try multiple keys for birthday
                $birthdayStr = $employee['birthday'] ?? $employee['date_of_birth'] ?? $employee['dob'] ?? null;
                $birthday = $birthdayStr ? \Carbon\Carbon::parse($birthdayStr) : null;

                $reasons = [];

                // Check Birthday (Current Month)
                if ($birthday && $birthday->month === $now->month) {
                    $reasons[] = [
                        'type' => 'Birthday',
                        'detail' => 'Birthday on ' . $birthday->format('M d'),
                        'icon' => 'bi-cake2'
                    ];
                }

                // Check Work Anniversary (Current Month, 1+ years)
                if ($hireDate && $hireDate->month === $now->month && $hireDate->year < $now->year) {
                    $years = $now->year - $hireDate->year;
                    $reasons[] = [
                        'type' => 'Service',
                        'detail' => $years . ' Year' . ($years > 1 ? 's' : '') . ' Anniversary',
                        'icon' => 'bi-award'
                    ];
                }

                // Check Performance
                $latestEval = \App\Models\Performance\Evaluation::where('employee_name', $name)
                    ->where('status', 'Completed')
                    ->latest('evaluation_date')
                    ->first();

                if ($latestEval) {
                    $score = $latestEval->overall_score;
                    $isCentum = $score > 5;
                    $threshold = $isCentum ? 80 : 4;
                    $max = $isCentum ? 100 : 5;

                    if ($score >= $threshold) {
                        $reasons[] = [
                            'type' => 'Performance',
                            'detail' => 'High Rating: ' . $score . '/' . $max,
                            'icon' => 'bi-graph-up-arrow'
                        ];
                    }
                }

                // Check Attendance (records in this month)
                $empAttendance = collect($attendance)->filter(function($record) use ($email) {
                    $recEmail = $record['employee']['email'] ?? '';
                    if ($recEmail !== $email) return false;
                    
                    $date = \Carbon\Carbon::parse($record['date'] ?? $record['created_at'] ?? 'now');
                    return $date->isCurrentMonth() && strtolower($record['status'] ?? '') === 'present';
                });

                if ($empAttendance->count() >= 1) { 
                    $reasons[] = [
                        'type' => 'Attendance',
                        'detail' => $empAttendance->count() . ' Days Present (Month)',
                        'icon' => 'bi-calendar-check'
                    ];
                }

                if (!empty($reasons)) {
                    $eligible[] = [
                        'id' => $employee['id'] ?? uniqid(),
                        'name' => $name,
                        'position' => $employee['position'] ?? 'N/A',
                        'department' => $employee['department']['name'] ?? $employee['department'] ?? 'N/A',
                        'reasons' => $reasons,
                        'avatar' => substr($name, 0, 1)
                    ];
                }
            }

            $this->eligibleEmployees = collect($eligible)->take(10)->toArray(); // Limit to 10 for view

        } catch (\Exception $e) {
            // Silently handle
            $this->eligibleEmployees = [];
        } finally {
            $this->loadingEligible = false;
        }
    }


    public function render()
    {
        // Numerical Status 
        $statusCounts = [
            'Active'   => Reward::where('status', 'active')->count(),
            'Draft'    => Reward::where('status', 'draft')->count(),
            'Inactive' => Reward::where('status', 'inactive')->count(),
            'All'      => Reward::count(),
        ];

        // Query
        $query = Reward::query()->latest();

        // Filters and Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('benefits', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by type
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        // Filter by status
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        // Exclude draft rewards from main table
        $query->where('status', '!=', 'draft');

        $rewards = $query->paginate(10);

        if ($this->showDrafts) {
            $drafts = Reward::where('status', 'draft')
                        ->latest()
                        ->paginate(10);

            return view('livewire.user.recognition.rewards', [
                'statusCounts' => $statusCounts,
                'rewards' => null,
                'drafts'  => $drafts,
            ])->layout('layouts.app');
        }

        return view('livewire.user.recognition.rewards', [
                'rewards' => $rewards,
                'statusCounts' => $statusCounts,
        ])->layout('layouts.app');
    }
}
