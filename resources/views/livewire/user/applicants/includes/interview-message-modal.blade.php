{{-- Interview Message Modal --}}
@if($showMessageModal && $selectedCandidateForMessage)
<div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-envelope-fill me-2"></i>
                    Message Candidate: {{ $selectedCandidateForMessage->candidate_name }}
                </h5>
                <button type="button" class="btn-close" wire:click="closeMessageModal"></button>
            </div>
            <form wire:submit="sendMessage">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">To:</label>
                        <input type="text" class="form-control" value="{{ $selectedCandidateForMessage->candidate_email }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject *</label>
                        <input type="text" class="form-control" wire:model="messageSubject">
                        @error('messageSubject') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Message Content *</label>
                        <textarea class="form-control" rows="8" wire:model="messageBody"></textarea>
                        @error('messageBody') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="alert alert-info py-2 small">
                        <i class="bi bi-info-circle me-1"></i>
                        The candidate will receive this message via their provided email address.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeMessageModal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-send-fill me-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
