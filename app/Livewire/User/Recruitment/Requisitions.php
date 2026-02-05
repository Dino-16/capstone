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

    // Edit Modal Properties
    public $showEditModal = false;
    public $editingId;
    public $position;
    public $department;
    public $opening;

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
    }

   public function showAll()
    {
        $this->showDrafts = false;
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

    public function deleteRequisition($id)
    {
        if (auth()->user()->role !== 'Super Admin') {
            session()->push('status', 'Unauthorized action.');
            return;
        }
        
        $requisition = Requisition::findOrFail($id);
        $requisition->delete();
        session()->push('status', 'Requisition deleted successfully!');
    }

    public function editRequisition($id)
    {
        $requisition = Requisition::findOrFail($id);
        $this->editingId = $id;
        $this->position = $requisition->position;
        $this->department = $requisition->department;
        $this->opening = $requisition->opening;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetValidation();
        $this->reset(['editingId', 'position', 'department', 'opening']);
    }

    public function updateRequisition()
    {
        $this->validate([
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'opening' => 'required|integer|min:1',
        ]);

        $requisition = Requisition::findOrFail($this->editingId);
        $requisition->update([
            'position' => $this->position,
            'department' => $this->department,
            'opening' => $this->opening,
        ]);

        $this->showEditModal = false;
        session()->push('status', 'Requisition updated successfully!');
    }

    public function render()
    {
        $statusCounts = [
            'Pending'  => Requisition::where('status', 'Pending')->count(),
            'Accepted' => Requisition::where('status', 'Accepted')->count(),
            'Drafted'  => Requisition::where('status', 'Drafted')->count(),
            'All'      => Requisition::count(),
        ];

        // When drafts are open → only draft data
        if ($this->showDrafts) {
            return view('livewire.user.recruitment.requisitions', [
                'statusCounts' => $statusCounts,
                'requisitions' => null,
                'drafts'       => Requisition::where('status', 'Drafted')->latest()->paginate(10),
            ])->layout('layouts.app');
        }

        // Otherwise show normal list
        $query = Requisition::query()->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('requested_by', 'like', "%{$this->search}%")
                ->orWhere('department', 'like', "%{$this->search}%")
                ->orWhere('position', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        return view('livewire.user.recruitment.requisitions', [
            'statusCounts' => $statusCounts,
            'requisitions' => $query->paginate(10),
            'drafts'       => null,
        ])->layout('layouts.app');
    }

}
