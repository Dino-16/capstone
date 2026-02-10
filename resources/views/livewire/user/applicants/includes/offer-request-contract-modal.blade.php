    {{-- REQUEST CONTRACT MODAL --}}
    @if($showRequestContractModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i>Request Contract</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeRequestContractModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Requesting Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" wire:model="requestDepartment" placeholder="Enter department">
                            @error('requestDepartment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-secondary">Requestor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" wire:model="requestorName" placeholder="Enter your name">
                            @error('requestorName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Requestor Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control border-2" wire:model="requestorEmail" placeholder="Enter your email">
                            @error('requestorEmail') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Contract Type Requested <span class="text-danger">*</span></label>
                            <select class="form-select border-2" wire:model="requestContractType">
                                <option value="">Select Contract Type</option>
                                @foreach($contractTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('requestContractType') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary">Purpose <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control border-2" 
                                wire:model="requestPurpose" 
                                rows="5" 
                                placeholder="Describe the purpose of this contract request..."
                            ></textarea>
                            @error('requestPurpose') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-4">
                    <button type="button" class="btn btn-secondary px-4 py-2" wire:click="closeRequestContractModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4 py-2 fw-bold" wire:click="submitContractRequest" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-send-fill me-2"></i>Submit Request</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Submitting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
