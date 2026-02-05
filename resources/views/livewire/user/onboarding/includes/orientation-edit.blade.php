    @if($showEditModal)
    <div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
        <div @class('modal-dialog modal-lg modal-dialog-centered')>
            <div @class('modal-content border-0 shadow-lg')>
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold">Edit Orientation</h5>
                    <button type="button" class="btn-close" wire:click="$set('showEditModal', false)"></button>
                </div>

                <form wire:submit.prevent="updateOrientation">
                    <div @class('modal-body p-4')>
                        <div @class('mb-3')>
                            <label @class('form-label fw-bold')>Employee Name</label>
                            <input type="text" @class('form-control') value="{{ $employeeName }}" readonly>
                        </div>

                        <div @class('mb-3')>
                            <label @class('form-label fw-bold')>Email</label>
                            <input type="email" @class('form-control') wire:model="email" placeholder="employee@example.com">
                        </div>

                        <div @class('mb-3')>
                            <label @class('form-label fw-bold')>Position</label>
                            <input type="text" @class('form-control') wire:model="position" placeholder="Job Position" readonly>
                        </div>

                        <div @class('row')>
                            <div @class('col-md-6')>
                                <div @class('mb-3')>
                                    <label @class('form-label fw-bold')>Date & Time</label>
                                    <input type="datetime-local" @class('form-control') wire:model="orientationDate">
                                    @error('orientationDate') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div @class('col-md-6')>
                                <div @class('mb-3')>
                                    <label @class('form-label fw-bold')>Location / Facility <span class="text-danger">*</span></label>
                                    <select class="form-select @error('location') is-invalid @enderror" 
                                            wire:model.live="selectedFacility">
                                        <option value="">-- Select Approved Facility --</option>
                                        @foreach($approvedFacilities as $facility)
                                            <option value="{{ $facility['id'] }}">{{ $facility['details'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('location') <div @class('invalid-feedback d-block')>{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div @class('mb-3')>
                            <label @class('form-label fw-bold')>Facilitator</label>
                            <input type="text" @class('form-control') wire:model="facilitator" placeholder="John Smith">
                            @error('facilitator') <div @class('invalid-feedback')>{{ $message }}</div> @enderror
                        </div>

                        <div @class('mb-3')>
                            <label @class('form-label fw-bold')>Notes</label>
                            <textarea @class('form-control') wire:model="notes" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>

                    <div @class('modal-footer')>
                        <button type="button" @class('btn btn-secondary') wire:click="$set('showEditModal', false)">Cancel</button>
                        <button type="submit" @class('btn btn-primary')>
                            <span wire:loading.remove wire:target="updateOrientation">Update Orientation</span>
                            <span wire:loading wire:target="updateOrientation" @class('spinner-border spinner-border-sm')></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif