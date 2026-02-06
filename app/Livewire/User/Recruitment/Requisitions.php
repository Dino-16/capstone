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

    // Modal Properties
    public $showEditModal = false;
    public $showViewModal = false;
    public $editingId;
    public $position;
    public $department;
    public $opening;
    public $selectedRequisition = null;

    // Actions 
    public function approve($id)
    {
        $requisition = Requisition::findOrFail($id);
        $requisition->status = 'Accepted';
        $requisition->save();
        session()->push('status', 'Accepted Successfully!');
    }

    public function export()
    {
        $export = new RequisitionsExport();
        return $export->export();
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
        session()->push('status', 'Deleted successfully!');
    }

    public function viewRequisition($id)
    {
        $this->selectedRequisition = Requisition::findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedRequisition = null;
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
        session()->push('status', 'Updated successfully!');
    }

    public function render()
    {
        $statusCounts = [
            'Pending'  => Requisition::where('status', 'Pending')->count(),
            'Accepted' => Requisition::where('status', 'Accepted')->count(),
            'All'      => Requisition::count(),
        ];

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
        ])->layout('layouts.app');
    }
}
