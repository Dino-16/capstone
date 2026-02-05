@section('page-title', 'Support Tickets')
@section('page-subtitle', 'Track your requests')

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
           <h4 class="mb-0 fw-bold">My Tickets</h4>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary d-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i> New Ticket
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Admin Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold d-block">{{ $ticket->subject }}</span>
                                <small class="text-muted">{{ Str::limit($ticket->description, 50) }}</small>
                            </td>
                            <td>
                                @php
                                    $prioColor = match($ticket->priority) {
                                        'High' => 'danger',
                                        'Medium' => 'warning',
                                        'Low' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-soft-{{ $prioColor }} text-{{ $prioColor }} border border-{{ $prioColor }} rounded-pill px-3">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColor = match($ticket->status) {
                                        'Approved' => 'success',
                                        'Rejected' => 'danger',
                                        'Pending' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">{{ $ticket->status }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ $ticket->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td>
                                @if($ticket->admin_notes)
                                    <span class="text-muted small fst-italic">{{ Str::limit($ticket->admin_notes, 30) }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                    <p class="mb-0">No tickets found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $tickets->links() }}
        </div>
    </div>

    <!-- Create Ticket Modal -->
    @if($showCreateModal)
    <div class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-ticket-perforated me-2"></i> Create Support Ticket
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="submit">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject</label>
                            <input type="text" wire:model="subject" class="form-control" placeholder="Briefly describe the issue">
                            @error('subject') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Priority</label>
                                <select wire:model="priority" class="form-select">
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                                @error('priority') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <textarea wire:model="description" class="form-control" rows="6" placeholder="Provide detailed information about your request..."></textarea>
                            @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                             <button type="button" class="btn btn-outline-secondary" wire:click="closeCreateModal">Cancel</button>
                            <button type="submit" class="btn btn-primary d-flex align-items-center">
                                <i class="bi bi-send me-2"></i> Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
