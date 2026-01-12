<?php

namespace App\Livewire\User\Recruitment;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Recruitment\Requisition;
use App\Exports\Recruitment\RequisitionsExport;

class Requisitions extends Component
{   
    use WithPagination;

    public $search;
    public $statusFilter = 'All';
    public $showDrafts = false;

    // Actions 
    public function approve($id)
    {
        $requisition = Requisition::findOrFail($id);
        $requisition->status = 'Accepted';
        $requisition->save();
        session()->push('status', 'Accepted Successfully!');
    }

    public function draft($id)
    {
        $requisition = Requisition::findOrFail($id);
        $requisition->status = 'Drafted';
        $requisition->save();
        session()->push('status', 'Drafted Successfully!');
    }

    public function restore($id) 
    {
        $requisition = Requisition::findOrFail($id);
        $requisition->status = 'Pending';
        $requisition->save();    
        session()->push('status', 'Draft Restored Successfully!');
    }

    public function export()
    {
        $export = new RequisitionsExport();
        return $export->export();
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

    public function render()
    {

        // Numerical Status 
        $statusCounts = [
            'Pending'   => Requisition::where('status', 'Pending')->count(),
            'Accepted'  => Requisition::where('status', 'Accepted')->count(),
            'Drafted'   => Requisition::where('status', 'Drafted')->count(),
            'All'       => Requisition::count(),
        ];
        

        // Query
        $query = Requisition::query()->latest();

        // Filters and Search

        if  ($this->search) {
            $query->where(function ($q) {
                $q->where('requested_by', 'like', "%{$this->search}%")
                  ->orWhere('department', 'like', "%{$this->search}%")
                  ->orWhere('position', 'like', "%{$this->search}%");
            }); 
        }

        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        $requisitions = $query->paginate(10);

        if ($this->showDrafts) {
            $drafts = Requisition::where('status', 'Drafted')
                        ->latest()
                        ->paginate(10);

            return view('livewire.user.recruitment.requisitions', [
                'statusCounts' => $statusCounts,
                'requisitions' => null,
                'drafts'       => $drafts,
            ])->layout('layouts.app');
        }

        return view('livewire.user.recruitment.requisitions', [
                'requisitions' => $requisitions,
                'statusCounts' => $statusCounts,
        ])->layout('layouts.app');
    }
}
