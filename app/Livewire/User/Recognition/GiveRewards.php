<?php

namespace App\Livewire\User\Recognition;

use App\Models\Recognition\Reward;
use App\Models\Recognition\GiveReward;
use App\Exports\Recognition\GiveRewardsExport;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class GiveRewards extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editing = false;
    public $rewardGivenId = null;
    public $statusFilter = '';
    public $dateFilter = '';
    public $inTodayCount = 0;

    // Form fields
    public $rewardId = '';
    public $employeeName = '';
    public $employeeEmail = '';
    public $employeePosition = '';
    public $employeeDepartment = '';
    public $givenDate = '';
    public $givenBy = '';
    public $reason = '';
    public $status = 'pending';
    public $notes = '';

    // Employee search
    public $employees = [];
    public $filteredEmployees = [];
    public $showEmployeeDropdown = false;

    protected $rules = [
        'rewardId' => 'required|exists:rewards,id',
        'employeeName' => 'required|string|max:255',
        'employeeEmail' => 'required|email',
        'employeePosition' => 'nullable|string|max:255',
        'employeeDepartment' => 'nullable|string|max:255',
        'givenDate' => 'required|date',
        'givenBy' => 'required|string|max:255',
        'reason' => 'nullable|string',
        'status' => 'required|in:pending,approved,rejected',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->resetForm();
        $this->givenDate = now()->format('Y-m-d');
        
        // Fetch employees from API
        $response = Http::get('http://hr4.jetlougetravels-ph.com/api/employees');

        if ($response->successful() && is_array($response->json())) {
            $this->employees = $response->json();
            $this->filteredEmployees = $this->employees;
        } else {
            $this->employees = [];
            $this->filteredEmployees = [];
        }
    }

    public function resetForm()
    {
        $this->reset([
            'rewardId', 'employeeName', 'employeeEmail', 'employeePosition', 
            'employeeDepartment', 'givenDate', 'givenBy', 'reason', 'status', 
            'notes', 'editing', 'rewardGivenId', 'statusFilter', 'dateFilter'
        ]);
        $this->givenDate = now()->format('Y-m-d');
        $this->status = 'pending';
        $this->statusFilter = '';
        $this->dateFilter = '';
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function updatedEmployeeName()
    {
        if (empty($this->employeeName)) {
            $this->filteredEmployees = $this->employees;
            $this->showEmployeeDropdown = false;
            return;
        }

        $this->filteredEmployees = collect($this->employees)->filter(function ($employee) {
            $name = strtolower($employee['name'] ?? $employee['employee_name'] ?? '');
            return strpos($name, strtolower($this->employeeName)) !== false;
        })->take(10)->toArray();

        $this->showEmployeeDropdown = true;
    }

    public function selectEmployee($name)
    {
        $this->employeeName = $name;
        
        // Find employee details and set email
        $employee = collect($this->employees)->first(function ($emp) use ($name) {
            return ($emp['name'] ?? $emp['employee_name'] ?? '') === $name;
        });

        if ($employee) {
            $this->employeeEmail = $employee['email'] ?? '';
        }

        $this->showEmployeeDropdown = false;
    }

    public function editRewardGiven($id)
    {
        $rewardGiven = RewardGiven::find($id);
        
        if ($rewardGiven) {
            $this->rewardGivenId = $id;
            $this->rewardId = $rewardGiven->reward_id;
            $this->employeeName = $rewardGiven->employee_name;
            $this->employeeEmail = $rewardGiven->employee_email;
            $this->employeePosition = $rewardGiven->employee_position;
            $this->employeeDepartment = $rewardGiven->employee_department;
            $this->givenDate = $rewardGiven->given_date->format('Y-m-d');
            $this->givenBy = $rewardGiven->given_by;
            $this->reason = $rewardGiven->reason;
            $this->status = $rewardGiven->status;
            $this->notes = $rewardGiven->notes;
            $this->editing = true;
            $this->showModal = true;
        }
    }

    public function addRewardGiving()
    {
        $this->validate();

        GiveReward::create([
            'reward_id' => $this->rewardId,
            'employee_name' => $this->employeeName,
            'employee_email' => $this->employeeEmail,
            'employee_position' => $this->employeePosition,
            'employee_department' => $this->employeeDepartment,
            'given_date' => $this->givenDate,
            'given_by' => $this->givenBy,
            'reason' => $this->reason,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        $this->dispatch('reward-given-added', 'Reward given successfully!');
        $this->showModal = false;
    }

    public function updateRewardGiving()
    {
        $this->validate();

        $rewardGiven = RewardGiven::find($this->rewardGivenId);
        
        if ($rewardGiven) {
            $rewardGiven->update([
                'reward_id' => $this->rewardId,
                'employee_name' => $this->employeeName,
                'employee_email' => $this->employeeEmail,
                'employee_position' => $this->employeePosition,
                'employee_department' => $this->employeeDepartment,
                'given_date' => $this->givenDate,
                'given_by' => $this->givenBy,
                'reason' => $this->reason,
                'status' => $this->status,
                'notes' => $this->notes,
            ]);

            $this->dispatch('reward-given-updated', 'Reward given updated successfully!');
            $this->showModal = false;
        }
    }

    public function deleteRewardGiven($id)
    {
        $rewardGiven = RewardGiven::find($id);
        
        if ($rewardGiven) {
            $rewardGiven->delete();
            session()->flash('message', 'Reward given deleted successfully!');
        }
    }

    public function export()
    {
        return Excel::download(new GiveRewardsExport, 'give-rewards-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function render()
    {
        $query = GiveReward::with('reward');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('employee_name', 'like', '%' . $this->search . '%')
                  ->orWhere('employee_email', 'like', '%' . $this->search . '%')
                  ->orWhere('given_by', 'like', '%' . $this->search . '%')
                  ->orWhereHas('reward', function ($subQuery) {
                      $subQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filter by status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Filter by date
        if ($this->dateFilter) {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('given_date', today());
                    break;
                case 'week':
                    $query->whereBetween('given_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('given_date', now()->month)->whereYear('given_date', now()->year);
                    break;
                case 'year':
                    $query->whereYear('given_date', now()->year);
                    break;
            }
        }

        $rewardsGiven = $query->orderBy('created_at', 'desc')->paginate(10);
        $rewards = Reward::where('is_active', true)->get();
        
        // Calculate today's count
        $this->inTodayCount = GiveReward::whereDate('given_date', today())->count();

        return view('livewire.user.recognition.give-rewards', [
            'rewardsGiven' => $rewardsGiven,
            'rewards' => $rewards
        ])->layout('layouts.app');
    }
}
