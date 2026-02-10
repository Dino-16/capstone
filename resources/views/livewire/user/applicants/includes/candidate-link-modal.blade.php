    {{-- Send Scheduling Link Modal --}}
    @if($showSendLinkModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-send me-2"></i>Send Self-Scheduling Link</h5>
                    <button type="button" class="btn-close" wire:click="closeSendLinkModal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        A self-scheduling link will be sent to the candidate, allowing them to confirm or reschedule their interview slot.
                    </div>
                    
                    <p><strong>Candidate:</strong> {{ $sendLinkCandidateName }}</p>
                    <p><strong>Email:</strong> {{ $sendLinkCandidateEmail }}</p>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Make sure the email address is correct before sending.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeSendLinkModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="sendSchedulingLink" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-send me-1"></i>Send Link</span>
                        <span wire:loading>Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
