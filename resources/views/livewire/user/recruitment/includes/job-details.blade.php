@if($showModal && $jobDetail)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
    <div @class('modal-dialog modal-lg modal-dialog-centered')>
        <div @class('modal-content border-0 shadow')>
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase"></i>
                    {{ $jobDetail->status === 'Active' ? 'Edit Job' : 'Activate Job' }}: {{ $jobDetail->position }}
                </h5>
                <button type="button" class="btn-close" wire:click="closeModal"></button>
            </div>
            <div @class('modal-body p-4')>
                {{-- Job Info --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Department</label>
                        <p class="fw-semibold">{{ $jobDetail->department ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Location</label>
                        <p class="fw-semibold">{{ $jobDetail->location ?? 'Ever Gotesco Commonwealth' }}</p>
                    </div>
                </div>

                <div @class('row g-4 mb-4')>
                    <div @class('col-md-6')>
                        <label class="form-label text-muted small">Description</label>
                        <p @class('text-secondary small mb-0') style="max-height: 100px; overflow-y: auto;">
                            {{ $jobDetail->description ?: 'No description provided.' }}
                        </p>
                    </div>
                    <div @class('col-md-6')>
                        <label class="form-label text-muted small">Qualifications</label>
                        <p @class('text-secondary small mb-0') style="max-height: 100px; overflow-y: auto;">
                            {{ $jobDetail->qualifications ?: 'No qualifications specified.' }}
                        </p>
                    </div>
                </div>

                {{-- Editable Fields --}}
                <div @class('pt-4 border-top')>
                    <h6 class="fw-bold mb-3">Job Settings</h6>
                    @error('expiration_date')
                        <div @class('alert alert-danger py-2 mb-3')>{{ $message }}</div>
                    @enderror
                    <div @class('row g-3')>
                        <div @class('col-md-4')>
                            <label @class('form-label small fw-bold')>Work Type</label>
                            <select @class('form-select') wire:model="type">
                                <option value="On-Site">On-Site</option>
                                <option value="Remote">Remote</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                        <div @class('col-md-4')>
                            <label @class('form-label small fw-bold')>Arrangement</label>
                            <select @class('form-select') wire:model="arrangement">
                                <option value="Full-Time">Full-Time</option>
                                <option value="Part-Time">Part-Time</option>
                            </select>
                        </div>
                        <div @class('col-md-4')>
                            <label @class('form-label small fw-bold')>Expiration Date <span class="text-danger">*</span></label>
                            <input type="date" @class('form-control') wire:model="expiration_date">
                        </div>
                    </div>
                </div>
            </div>
            <div @class('modal-footer border-top')>
                <button type="button" class="btn btn-secondary" wire:click="closeModal">
                    Cancel
                </button>
                @if($jobDetail->status === 'Active')
                    <button type="button" class="btn btn-success" wire:click="saveJobEdit">
                        <span wire:loading.remove wire:target="saveJobEdit"><i class="bi bi-check-lg me-1"></i> Save Changes</span>
                        <span wire:loading wire:target="saveJobEdit" class="spinner-border spinner-border-sm"></span>
                    </button>
                @else
                    <button type="button" class="btn btn-success" wire:click="publishJob">
                        <span wire:loading.remove wire:target="publishJob"><i class="bi bi-check-lg me-1"></i> Activate Post</span>
                        <span wire:loading wire:target="publishJob" class="spinner-border spinner-border-sm"></span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<style>
    .ls-1 { letter-spacing: 0.05em; }
    .x-small { font-size: 0.75rem; }
    .leading-relaxed { line-height: 1.6; }
    .form-select, .form-control { font-size: 0.9rem; border-radius: 4px; padding: 0.6rem; }
    .form-select:focus, .form-control:focus { border-color: #213A5C; }
</style>