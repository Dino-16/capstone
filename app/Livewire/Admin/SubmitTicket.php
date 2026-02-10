<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupportTicket;

class SubmitTicket extends Component
{
    use WithPagination;

    // List Filtering properties
    public $search = '';
    public $statusFilter = '';

    // Create Form Properties
    public $subject;
    public $description;
    public $priority = 'Low';
    public $showCreateModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    protected $rules = [
        'subject' => 'required|min:5|max:255',
        'description' => 'required|min:20',
        'priority' => 'required|in:Low,Medium,High',
    ];

    public function openCreateModal()
    {
        $this->reset(['subject', 'description', 'priority']);
        $this->priority = 'Low';
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetValidation();
    }

    public function submit()
    {
        $this->validate();

        $user = session('user');

        SupportTicket::create([
            'requester_name' => $user['name'],
            'requester_email' => $user['email'],
            'requester_position' => $user['position'],
            'subject' => $this->subject,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => 'Pending',
        ]);

        $this->showCreateModal = false;
        $this->reset(['subject', 'description', 'priority']);
        
        session()->flash('success', 'Support ticket submitted successfully. Pending Super Admin approval.');
    }

    public function exportData()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SupportTicketsExport(session('user.email')), 'my_tickets.xlsx');
    }

    public function render()
    {
        $userEmail = session('user.email');

        $tickets = SupportTicket::where('requester_email', $userEmail)
            ->when($this->search, function ($query) {
                $query->where('subject', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.submit-ticket', [
            'tickets' => $tickets
        ])->layout('layouts.app');
    }
}
