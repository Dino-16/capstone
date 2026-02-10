<?php

namespace App\Livewire\User\Recruitment;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Recruitment\Requisition;
use App\Exports\Recruitment\RequisitionsExport;
use Maatwebsite\Excel\Facades\Excel;

class Requisitions extends Component
{   
    use WithPagination;
    use \App\Livewire\Traits\HandlesToasts;

    public $search;
    public $statusFilter = 'All';
    public $departmentFilter = '';
    public $positionFilter = '';

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
        $this->toast('Accepted Successfully!');
    }

    public function export()
    {
        return Excel::download(new RequisitionsExport, 'requisitions.xlsx');
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

    public function updatedDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatedPositionFilter()
    {
        $this->resetPage();
    }


    public $showDeleteModal = false;
    public $requisitionIdToDelete = null;

    public function confirmDelete($id)
    {
        $this->requisitionIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteRequisition()
    {
        if (!in_array(session('user.position'), ['Super Admin', 'HR Manager'])) {
            $this->toast('Unauthorized action.', 'error');
            return;
        }
        
        if ($this->requisitionIdToDelete) {
            $requisition = Requisition::findOrFail($this->requisitionIdToDelete);
            $requisition->delete();
            $this->toast('Deleted successfully!');
        }

        $this->showDeleteModal = false;
        $this->requisitionIdToDelete = null;
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
        $this->toast('Updated successfully!');
    }

    public function render()
    {
        $statusCounts = [
            'Pending'  => Requisition::where('status', 'Pending')->count(),
            'Accepted' => Requisition::where('status', 'Accepted')->count(),
            'All'      => Requisition::count(),
        ];

        $departments = Requisition::select('department')->distinct()->orderBy('department')->pluck('department');
        $positions = Requisition::select('position')->distinct()->orderBy('position')->pluck('position');

        // Prioritize Pending status, then show latest
        $query = Requisition::query()
            ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
            ->latest();

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

        if ($this->departmentFilter) {
            $query->where('department', $this->departmentFilter);
        }

        if ($this->positionFilter) {
            $query->where('position', $this->positionFilter);
        }

        return view('livewire.user.recruitment.requisitions', [
            'statusCounts' => $statusCounts,
            'requisitions' => $query->paginate(10),
            'departments' => $departments,
            'positions' => $positions,
        ])->layout('layouts.app');
    }
}
