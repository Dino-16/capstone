@section('page-title', 'Positions')
@section('page-subtitle', 'Manage job positions')
@section('breadcrumbs', 'Positions')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- STATUS CARDS --}}
    @include('livewire.user.recruitment.includes.requisition-cards')

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-search-input
                    wire:model.live="search" 
                    placeholder="Search..."
                />
            </div>

            {{-- FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="filterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: {{ $statusFilter }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="filterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'All')">
                            All
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Pending')">
                           Pending
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Accepted')">
                            Accepted
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                
                <button
                    @class('btn btn-success')
                    wire:click="export"
                >
                    Export
                </button>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    @if($requisitions)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>All Positions</h3>
            <p @class('text-secondary mb-0')>
                Overview of positions
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Requested by</th>
                        <th @class('text-secondary')>Department</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>Opening</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $req)
                        <tr wire:key="{{ $req->id }}">
                            <td>{{ $req->requested_by }}</td>
                            <td>{{ $req->department }}</td>
                            <td>{{ $req->position }}</td>
                            <td>{{ $req->opening }}</td>
                            <td>
                                @if($req->status === 'Accepted')
                                    <span @class('badge bg-success')>{{ $req->status }}</span>
                                @elseif($req->status === 'Pending')
                                    <span @class('badge bg-warning text-dark')>{{ $req->status }}</span>
                                @else
                                    <span @class('badge bg-secondary')>No Data</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button
                                        @class('btn btn-sm btn-outline-primary')
                                        wire:click="viewRequisition({{ $req->id }})"
                                        title="View Details"
                                    >
                                        <i @class('bi bi-eye')></i>
                                    </button>

                                    @if($req->status === 'Pending')
                                        @if(session('user.position') === 'HR Manager')
                                            <button
                                                class="btn btn-sm btn-outline-primary"
                                                wire:click="editRequisition({{ $req->id }})"
                                                title="Edit"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endif
                                        <button
                                            @class('btn btn-sm btn-success')
                                            wire:click="approve({{ $req->id }})"
                                            title="Approve"
                                        >
                                            <i @class('bi bi-check-lg')></i>
                                        </button>
                                        
                                        @if(session('user.position') === 'Super Admin')
                                            <button
                                                @class('btn btn-sm btn-danger')
                                                wire:click="deleteRequisition({{ $req->id }})"
                                                wire:confirm="Are you sure you want to delete this requisition?"
                                                title="Delete"
                                            >
                                                <i @class('bi bi-trash')></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        @if($search)
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No positions found matching "{{ $search }}".</div>
                                </td>
                            </tr>
                        @elseif($statusFilter === 'Pending')
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-hourglass-split d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No pending position found.</div>
                                </td>
                            </tr>
                        @elseif($statusFilter === 'Accepted')
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-check-circle d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No approved position found.</div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" @class('text-center text-muted py-5')>
                                    <i @class('bi bi-inbox d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No position found.</div>
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
            {{ $requisitions->links() }}
        </div>
    @endif

    {{-- View Modal --}}
    @if($showViewModal && $selectedRequisition)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom p-4">
                    <h5 class="modal-title fw-bold">Requisition Details</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Position</label>
                            <p class="mb-0 fw-semibold fs-5 text-dark">{{ $selectedRequisition->position }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Status</label>
                            <div>
                                @if($selectedRequisition->status === 'Accepted')
                                    <span @class('badge bg-success px-3 py-2')>{{ $selectedRequisition->status }}</span>
                                @elseif($selectedRequisition->status === 'Pending')
                                    <span @class('badge bg-warning text-dark px-3 py-2')>{{ $selectedRequisition->status }}</span>
                                @else
                                    <span @class('badge bg-secondary px-3 py-2')>No Data</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Department</label>
                            <p class="mb-0 fw-semibold text-dark">{{ $selectedRequisition->department }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Opening</label>
                            <p class="mb-0 fw-semibold text-dark">{{ $selectedRequisition->opening }} slots</p>
                        </div>
                        <div class="col-12">
                            <hr class="my-2 opacity-10">
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Requested By</label>
                            <p class="mb-0 fw-semibold text-dark">{{ $selectedRequisition->requested_by }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small text-uppercase fw-bold mb-1 d-block">Date Created</label>
                            <p class="mb-0 fw-semibold text-dark">{{ $selectedRequisition->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-4">
                    <button type="button" class="btn btn-secondary px-4" wire:click="closeViewModal">Close</button>
                    @if($selectedRequisition->status === 'Pending' && session('user.position') === 'HR Manager')
                        <button type="button" class="btn btn-primary px-4" wire:click="editRequisition({{ $selectedRequisition->id }}); closeViewModal();">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Edit Modal --}}
    @if($showEditModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title">Edit Requisition</h5>
                    <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateRequisition">
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" wire:model="position">
                            @error('position') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" wire:model="department">
                            @error('department') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Opening</label>
                            <input type="number" class="form-control" wire:model="opening">
                            @error('opening') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="updateRequisition">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

