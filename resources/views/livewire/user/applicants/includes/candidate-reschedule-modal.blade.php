    {{-- Reschedule Modal --}}
    @if($showRescheduleModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-calendar2-plus me-2"></i>Reschedule Interview</h5>
                    <button type="button" class="btn-close" wire:click="closeRescheduleModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">New Interview Date</label>
                            <input type="date" class="form-control" wire:model="new_interview_date">
                            @error('new_interview_date') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">New Interview Time</label>
                            <input type="time" class="form-control" wire:model="new_interview_time">
                            @error('new_interview_time') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeRescheduleModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="rescheduleInterview" wire:loading.attr="disabled">
                        <span wire:loading.remove>Reschedule</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
