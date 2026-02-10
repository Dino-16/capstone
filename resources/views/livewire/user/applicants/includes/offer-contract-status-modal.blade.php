    {{-- CONTRACT STATUS MODAL --}}
    @if($showContractModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i>Update Contract Status</h5>
                    <button type="button" class="btn-close" wire:click="closeContractModal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Candidate:</strong> {{ $contractCandidateName }}</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contract Status</label>
                        <select class="form-select" wire:model="newContractStatus">
                            <option value="pending">Pending</option>
                            <option value="sent">Sent</option>
                            <option value="approved">Approved</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Changing to "Approved" will enable the Document Email button.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeContractModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="updateContractStatus" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Status</span>
                        <span wire:loading>Updating...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
