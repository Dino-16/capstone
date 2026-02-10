    {{-- DOCUMENT EMAIL MODAL --}}
    @if($showEmailModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-envelope-fill me-2"></i>Send Document Requirements Email</h5>
                    <button type="button" class="btn-close" wire:click="closeEmailModal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-person-check me-2"></i>
                        Sending to: <strong>{{ $emailCandidateName }}</strong> ({{ $emailCandidateEmail }})
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Subject</label>
                        <input type="text" class="form-control" wire:model="emailSubject">
                        @error('emailSubject') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Content (Document Explainer)</label>
                        <textarea 
                            class="form-control font-monospace" 
                            wire:model="emailContent" 
                            rows="20"
                            style="font-size: 0.85rem;"
                        ></textarea>
                        @error('emailContent') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Review the document list before sending. This email explains exactly what physical documents the candidate needs to prepare.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEmailModal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-lg" wire:click="sendDocumentEmail" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-send me-1"></i>Send Email</span>
                        <span wire:loading>Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
