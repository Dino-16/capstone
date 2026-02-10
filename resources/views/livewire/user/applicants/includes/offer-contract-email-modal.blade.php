    {{-- CONTRACT EMAIL MODAL --}}
    @if($showContractEmailModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom shadow-sm">
                    <h5 class="modal-title text-primary"><i class="bi bi-file-earmark-medical me-2"></i>Send Employment Contract</h5>
                    <button type="button" class="btn-close" wire:click="closeContractEmailModal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Subject</label>
                        <input type="text" class="form-control" wire:model="contractEmailSubject">
                        @error('contractEmailSubject') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Message Body</label>
                        <textarea 
                            class="form-control" 
                            wire:model="contractEmailContent" 
                            rows="5"
                            placeholder="Enter your email message here..."
                        ></textarea>
                        @error('contractEmailContent') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-paperclip me-1"></i>Attach Contract File
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" wire:model="contractFile" accept=".pdf,.doc,.docx">
                        <div class="form-text">Accepted formats: PDF, DOC, DOCX (Max 10MB)</div>
                        @error('contractFile') <div class="text-danger small">{{ $message }}</div> @enderror
                        
                        <div wire:loading wire:target="contractFile" class="text-info small mt-1">
                            <span class="spinner-border spinner-border-sm me-1"></span>Uploading...
                        </div>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            Sending this email will automatically update the candidate's contract status to <strong>"Sent"</strong> and record the timestamp.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeContractEmailModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" wire:click="sendContractEmail" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-send-check-fill me-2"></i>Send Contract</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
