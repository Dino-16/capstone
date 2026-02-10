{{-- Request Contract Modal --}}
@if($showRequestContractModal)
<div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i>Request Contract</h5>
                <button type="button" class="btn-close" wire:click="closeRequestContractModal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase">Requesting Department <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-2" wire:model="requestDepartment" placeholder="Enter department">
                                @error('requestDepartment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase">Requestor Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-2" wire:model="requestorName" placeholder="Enter your name">
                                @error('requestorName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small text-uppercase">Requestor Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control border-2" wire:model="requestorEmail" placeholder="Enter your email">
                                @error('requestorEmail') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                             </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small text-uppercase">Contract Type Requested <span class="text-danger">*</span></label>
                                <select class="form-select border-2" wire:model="requestContractType">
                                    <option value="">Select Contract Type</option>
                                    @foreach($contractTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('requestContractType') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small text-uppercase">Purpose <span class="text-danger">*</span></label>
                                <textarea 
                                    class="form-control border-2" 
                                    wire:model="requestPurpose" 
                                    rows="5" 
                                    placeholder="Describe the purpose of this contract request..."
                                    style="resize: none;"
                                ></textarea>
                                @error('requestPurpose') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-0">
                    <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                    <small>This request will be sent to the Legal Administration system for processing. You will be notified once the contract is ready.</small>
                </div>
            </div>
            <div class="modal-footer border-top p-4 bg-white">
                <button type="button" class="btn btn-outline-secondary px-4 py-2" wire:click="closeRequestContractModal">Cancel</button>
                <button type="button" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" wire:click="submitContractRequest" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="bi bi-send-fill me-2"></i>Send Request</span>
                    <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Submitting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
