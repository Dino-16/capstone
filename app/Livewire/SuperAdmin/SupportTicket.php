<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupportTicket as TicketModel;

class SupportTicket extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'Pending';
    public $selectedTicket = null;
    public $adminNotes = '';

    // Action Modal Logic
    public $showActionModal = false;
    public $modalAction = 'Approve'; // Approve or Reject

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openActionModal($ticketId, $action)
    {
        $this->selectedTicket = TicketModel::find($ticketId);
        $this->modalAction = $action;
        $this->adminNotes = ''; // Reset notes on open
        $this->showActionModal = true;
    }

    public function processTicket()
    {
        if (!$this->selectedTicket) return;

        $status = ($this->modalAction === 'Approve') ? 'Approved' : 'Rejected';

        $this->selectedTicket->update([
            'status' => $status,
            'admin_notes' => $this->adminNotes,
        ]);

        $this->showActionModal = false;
        $this->selectedTicket = null;
        
        session()->flash('success', "Ticket {$status} successfully.");
    }

    public function exportData()
    {
        $export = new \App\Exports\SupportTicketsExport();
        return $export->export();
    }

    public function render()
    {
        $tickets = TicketModel::query()
            ->when($this->search, function ($query) {
                $query->where('subject', 'like', '%' . $this->search . '%')
                      ->orWhere('requester_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.superadmin.support-ticket', [
            'tickets' => $tickets
        ])->layout('layouts.superadmin');
    }
}
