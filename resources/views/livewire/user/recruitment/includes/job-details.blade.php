@if($showModal && $jobDetail)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(15, 35, 85, 0.5); backdrop-filter: blur(4px);">
    <div @class('modal-dialog modal-lg modal-dialog-centered')>
        <div @class('modal-content border-0 shadow-lg rounded-3')>
            <div @class('modal-header border-0 px-4 pt-4 pb-0')>
                <h4 @class('fw-bold text-dark m-0') style="letter-spacing: -0.01em;">
                    {{ $jobDetail->position }}
                </h4>
                <button type="button" @class('btn-close small shadow-none') wire:click="closeModal"></button>
            </div>
            <div @class('modal-body px-4 py-4')>
                <div @class('row g-4 mb-5')>
                    <div @class('col-md-6')>
                        <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Description</span>
                        <p @class('text-secondary small leading-relaxed mb-0')>
                            {{ $jobDetail->description }}
                        </p>
                    </div>
                    <div @class('col-md-6')>
                        <span @class('text-uppercase text-muted fw-bold small ls-1 d-block mb-2')>Qualifications</span>
                        <p @class('text-secondary small leading-relaxed mb-0')>
                            {{ $jobDetail->qualifications }}
                        </p>
                    </div>
                </div>
                <div @class('pt-4 border-top')>
                    @error('type')
                        <div @class('alert alert-danger py-2 mb-3')>{{ $message }}</div>
                    @enderror
                    @error('arrangement')
                        <div @class('alert alert-danger py-2 mb-3')>{{ $message }}</div>
                    @enderror
                    @error('expiration_date')
                        <div @class('alert alert-danger py-2 mb-3')>{{ $message }}</div>
                    @enderror
                    <div @class('row g-3')>
                        <div @class('col-md-4')>
                            <label @class('form-label x-small fw-bold text-dark')>Employment Type</label>
                            <select @class('form-select border-light-subtle shadow-none') wire:model="type">
                                <option value="On-Site" {{ ($jobDetail->type ?? 'On-Site') === 'On-Site' ? 'selected' : '' }}>On-Site</option>
                                <option value="Remote" {{ ($jobDetail->type ?? 'On-Site') === 'Remote' ? 'selected' : '' }}>Remote</option>
                                <option value="Hybrid" {{ ($jobDetail->type ?? 'On-Site') === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                        </div>
                        <div @class('col-md-4')>
                            <label @class('form-label x-small fw-bold text-dark')>Arrangement</label>
                            <select @class('form-select border-light-subtle shadow-none') wire:model="arrangement">
                                <option value="Full-Time" {{ ($jobDetail->arrangement ?? 'Full-Time') === 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="Part-Time" {{ ($jobDetail->arrangement ?? 'Full-Time') === 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                            </select>
                        </div>

                        <div @class('col-md-4')>
                            <label @class('form-label x-small fw-bold text-dark')>Expiration</label>
                            <input type="date" @class('form-control border-light-subtle shadow-none') wire:model="expiration_date">
                        </div>
                    </div>
                </div>
            </div>
            <div @class('modal-footer border-0 px-4 pb-4 pt-2 justify-content-between')>
                <button type="button" @class('btn btn-link text-muted text-decoration-none small p-0') wire:click="closeModal">
                    Cancel
                </button>
                <div @class('d-flex gap-2')>
                    <button type="button" @class('btn btn-dark px-4 py-2 rounded-2 fw-semibold shadow-sm') wire:click="publishJob" style="background: #213A5C; border: none;">
                        @if($jobDetail->status === 'Inactive')
                            <span wire:loading.remove wire:target="publishJob">Activate Post</span>
                        @else
                            <span wire:loading.remove wire:target="publishJob">Update Details</span>
                        @endif
                        <span wire:loading wire:target="publishJob" @class('small')>
                            <span @class('spinner-border spinner-border-sm me-2')></span>Processing
                        </span>
                    </button>
                </div>
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