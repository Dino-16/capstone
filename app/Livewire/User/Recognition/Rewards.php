<?php

namespace App\Livewire\User\Recognition;

use App\Models\Recognition\Reward;
use Livewire\Component;
use Livewire\WithPagination;

class Rewards extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editing = false;
    public $rewardId = null;
    public $typeFilter = '';
    public $statusFilter = '';
    public $showDrafts = false;

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

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
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

    public function addReward()
    {
        $this->validate();

        Reward::create([
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'benefits' => $this->benefits,
        ]);

        session()->push('status', 'Reward created successfully!');
        $this->showModal = false;
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

            session()->push('status', 'Reward updated successfully!');
            $this->showModal = false;
        }
    }

    public function draft($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->status = 'draft';
        $reward->save();
        session()->push('status', 'Reward drafted successfully!');
    }

    public function restore($id) 
    {
        $reward = Reward::findOrFail($id);
        $reward->status = 'active';
        $reward->save();    
        session()->push('status', 'Reward restored successfully!');
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
        $export = new \App\Exports\Recognition\RewardsExport();
        return $export->export();
    }

        // Clear Message Status
    public function clearStatus()
    {
        session()->forget('status');
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
