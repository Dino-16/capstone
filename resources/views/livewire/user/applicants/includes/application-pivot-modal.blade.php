    {{-- Shortlist Tool Modal - Pivot to Alternative Role --}}
    @if($showShortlistModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i>Pivot to Alternative Role</h5>
                    <button type="button" class="btn-close" wire:click="closeShortlistModal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>{{ $shortlistApplicantName }}</strong> may be a better fit for a different role. 
                        Use this tool to suggest an alternative position.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Position</label>
                        <input type="text" class="form-control" value="{{ $currentPosition }}" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Suggested Alternative Position</label>
                        <select class="form-select" wire:model="suggestedPosition">
                            <option value="">Select a position...</option>
                            @foreach($availablePositions as $position)
                                <option value="{{ $position }}">{{ $position }}</option>
                            @endforeach
                        </select>
                        @error('suggestedPosition') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Pivot (Optional)</label>
                        <textarea class="form-control" wire:model="shortlistReason" rows="3" placeholder="Why is this applicant better suited for the alternative role?"></textarea>
                        @error('shortlistReason') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeShortlistModal">Cancel</button>
                    <button type="button" class="btn Pelican-primary" wire:click="pivotToNewRole" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-check2 me-1"></i>Confirm Pivot</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
