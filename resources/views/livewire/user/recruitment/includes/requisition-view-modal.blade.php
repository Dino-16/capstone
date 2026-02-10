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
