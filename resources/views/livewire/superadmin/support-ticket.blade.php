@section('page-title', 'Ticket Requests')
@section('page-subtitle', 'Manage support requests')

<div>
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0 ps-0" placeholder="Search by subject or requester...">
            </div>
        </div>
        <div class="col-md-4">
            <select wire:model.live="statusFilter" class="form-select shadow-sm">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
            </select>
        </div>
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
                            <th class="ps-4 py-3">Requester</th>
                            <th>Issue</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold d-block text-dark">{{ $ticket->requester_name }}</span>
                                <small class="text-muted d-block">{{ $ticket->requester_position }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold d-block text-dark">{{ $ticket->subject }}</span>
                                <small class="text-muted">{{ Str::limit($ticket->description, 40) }}</small>
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
                                <span class="badge bg-soft-{{ $prioColor }} text-{{ $prioColor }} border border-{{ $prioColor }}">
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
                                {{ $ticket->created_at->diffForHumans() }}
                            </td>
                            <td class="text-end pe-4">
                                @if($ticket->status === 'Pending')
                                    <button wire:click="openActionModal({{ $ticket->id }}, 'Approve')" class="btn btn-sm btn-success me-1" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button wire:click="openActionModal({{ $ticket->id }}, 'Reject')" class="btn btn-sm btn-danger" title="Reject">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @else
                                    <span class="text-muted small">Processed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
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

    <!-- Action Modal -->
    @if($showActionModal)
    <div class="modal fade show d-block" style="background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header {{ $modalAction === 'Approve' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                    <h5 class="modal-title fw-bold">
                        <i class="bi {{ $modalAction === 'Approve' ? 'bi-check-circle' : 'bi-x-circle' }} me-2"></i>
                        {{ $modalAction }} Ticket
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="$set('showActionModal', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3">Are you sure you want to <strong>{{ strtolower($modalAction) }}</strong> this request from <strong>{{ $selectedTicket->requester_name }}</strong>?</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Ticket Details</label>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-1 fw-bold">{{ $selectedTicket->subject }}</p>
                            <p class="mb-0 small text-muted">{{ $selectedTicket->description }}</p>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Admin Notes (Optional)</label>
                        <textarea wire:model="adminNotes" class="form-control" rows="3" placeholder="Add any comments or reasons..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" wire:click="$set('showActionModal', false)">Cancel</button>
                    <button type="button" class="btn {{ $modalAction === 'Approve' ? 'btn-success' : 'btn-danger' }} px-4" wire:click="processTicket">
                        Confirm {{ $modalAction }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
