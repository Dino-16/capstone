    {{-- View Candidate Modal --}}
    @if($showViewModal && $selectedCandidate)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Candidate Details</h5>
                    <button type="button" class="btn-close" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Personal Information</h6>
                            <p><strong>Name:</strong> {{ $selectedCandidate->candidate_name }}</p>
                            <p><strong>Email:</strong> {{ $selectedCandidate->candidate_email }}</p>
                            <p><strong>Phone:</strong> {{ $selectedCandidate->candidate_phone }}</p>
                            <p><strong>Age:</strong> {{ $selectedCandidate->candidate_age ?? 'N/A' }}</p>
                            <p><strong>Gender:</strong> {{ ucfirst($selectedCandidate->candidate_sex ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Application Details</h6>
                            <p><strong>Position:</strong> {{ $selectedCandidate->applied_position ?? 'N/A' }}</p>
                            <p><strong>Department:</strong> {{ $selectedCandidate->department ?? 'N/A' }}</p>
                            @if($selectedCandidate->rating_score)
                                <p>
                                    <strong>AI Rating:</strong> 
                                    <span class="badge bg-{{ \App\Models\Applicants\Candidate::getRatingBadgeColor($selectedCandidate->rating_score) }}">
                                        {{ number_format($selectedCandidate->rating_score, 1) }}
                                    </span>
                                </p>
                                <p class="text-muted small">{{ $selectedCandidate->rating_description }}</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Address</h6>
                            <p class="mb-1">{{ $selectedCandidate->candidate_house_street ?? '' }}</p>
                            <p class="mb-1">{{ $selectedCandidate->candidate_barangay ?? '' }}, {{ $selectedCandidate->candidate_city ?? '' }}</p>
                            <p>{{ $selectedCandidate->candidate_province ?? '' }}, {{ $selectedCandidate->candidate_region ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Interview Status</h6>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $selectedCandidate->status === 'interview_ready' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $selectedCandidate->status)) }}
                                </span>
                            </p>
                            @if($selectedCandidate->interview_schedule)
                                <p><strong>Schedule:</strong> {{ $selectedCandidate->interview_schedule->format('M d, Y \a\t h:i A') }}</p>
                            @endif
                        </div>
                    </div>

                    @if($selectedCandidate->skills && is_array($selectedCandidate->skills) && count($selectedCandidate->skills) > 0)
                        <hr>
                        <h6 class="fw-bold text-primary mb-3">Skills</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($selectedCandidate->skills as $skill)
                                <span class="badge bg-light text-dark border">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($selectedCandidate->resume_url)
                        <hr>
                        <h6 class="fw-bold text-primary mb-3">Resume</h6>
                        <a href="{{ $selectedCandidate->resume_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> View Resume
                        </a>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeViewModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
