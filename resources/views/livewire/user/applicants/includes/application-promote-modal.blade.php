    {{-- Schedule Interview Modal --}}
    @if($showScheduleModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-calendar-check me-2"></i>Promote to Candidate - {{ $applicantData['name'] ?? '' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeScheduleModal"></button>
                </div>
                <div class="modal-body">
                    @if(!empty($applicantData))
                        {{-- Rating Summary --}}
                        @if(isset($applicantData['rating_score']))
                        <div class="card mb-4 border-0 bg-light">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="display-6 fw-bold text-{{ $applicantData['rating_badge_color'] ?? 'secondary' }}">
                                    {{ number_format($applicantData['rating_score'], 1) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">AI Rating Score</div>
                                    <small class="text-muted">{{ $applicantData['rating_description'] ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Email:</strong> {{ $applicantData['email'] }}
                            </div>
                            <div class="col-md-6">
                                <strong>Phone:</strong> {{ $applicantData['phone'] }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Position:</strong> {{ $applicantData['position'] }}
                            </div>
                            <div class="col-md-6">
                                <strong>Department:</strong> {{ $applicantData['department'] }}
                            </div>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Interview Location / Facility <span class="text-danger">*</span></label>
                        <select class="form-select @error('selectedFacility') is-invalid @enderror" 
                                wire:model.live="selectedFacility"
                                {{ count($approvedFacilities) == 0 ? 'disabled' : '' }}>
                            <option value="">
                                {{ count($approvedFacilities) == 0 ? 'No Approved Facility' : '-- Select Approved Facility --' }}
                            </option>
                            @foreach($approvedFacilities as $facility)
                                <option value="{{ $facility['id'] }}">{{ $facility['details'] }}</option>
                            @endforeach
                        </select>
                        @error('selectedFacility') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Select an approved facility reservation to auto-fill Scheduled Date & Time.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Interview Date</label>
                            <input type="date" class="form-control" wire:model="interview_date">
                            @error('interview_date') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Interview Time</label>
                            <input type="time" class="form-control" wire:model="interview_time">
                            @error('interview_time') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="bi bi-arrow-right-circle-fill me-2"></i>
                        <strong>Note:</strong> This will promote the application to <strong>Candidate</strong> status and schedule the interview. 
                        The candidate will receive a self-scheduling link via email to confirm or reschedule.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeScheduleModal">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="scheduleInterview" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-check2 me-1"></i>Promote & Schedule</span>
                        <span wire:loading>Scheduling...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
