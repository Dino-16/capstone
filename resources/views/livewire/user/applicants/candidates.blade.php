@section('page-title', 'Candidates')
@section('page-subtitle', 'Shortlist & Scheduling Hub')
@section('breadcrumbs', 'Candidates')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center mb-4')>

        {{-- LEFT SIDE --}}
        <div @class('d-flex align-items-center gap-3')>
            {{-- SEARCH BAR --}}
            <div>
                <x-text-input
                    type="search"
                    wire:model.live="search" 
                    placeholder="Search candidates..."
                />
            </div>
            {{-- STATUS FILTER --}}
            <div>
                <select class="form-select" wire:model.live="statusFilter">
                    <option value="">All Status</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="interview_ready">Interview Ready</option>
                </select>
            </div>
        </div>
    </div>

    {{-- WORKFLOW INFO --}}
    <div @class('card mb-4 border-0 shadow-sm bg-light')>
        <div @class('card-body py-3')>
            <div @class('d-flex align-items-center gap-3')>
                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                <div>
                    <strong>Step 2: Shortlist & Scheduling</strong>
                    <p class="mb-0 text-muted small">Candidates here have been promoted from applications. Send them a self-scheduling link to book their interview.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    @if($candidates)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3><i class="bi bi-people-fill me-2"></i>All Candidates</h3>
            <p @class('text-secondary mb-0')>
                Candidates awaiting interview scheduling
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table table-hover')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Name</th>
                        <th @class('text-secondary')>Contact</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>AI Rating</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Interview Schedule</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                        <tr wire:key="{{ $candidate->id }}">
                            <td>
                                <div class="fw-semibold">{{ $candidate->candidate_name }}</div>
                                @if($candidate->skills && is_array($candidate->skills) && count($candidate->skills) > 0)
                                    <small class="text-muted">{{ count($candidate->skills) }} skills</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ $candidate->candidate_email }}</div>
                                <small class="text-muted">{{ $candidate->candidate_phone }}</small>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $candidate->applied_position ?? 'N/A' }}</div>
                                <small class="text-muted">{{ $candidate->department ?? '' }}</small>
                            </td>
                            <td>
                                @if($candidate->rating_score)
                                    <span class="badge bg-{{ \App\Models\Applicants\Candidate::getRatingBadgeColor($candidate->rating_score) }}">
                                        {{ number_format($candidate->rating_score, 1) }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'scheduled' => 'warning',
                                        'interview_ready' => 'success',
                                    ];
                                    $statusLabels = [
                                        'scheduled' => 'Scheduled',
                                        'interview_ready' => 'Interview Ready',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$candidate->status] ?? 'secondary' }}">
                                    {{ $statusLabels[$candidate->status] ?? ucfirst($candidate->status) }}
                                </span>
                                @if($candidate->self_scheduled)
                                    <br><small class="text-success"><i class="bi bi-check-circle"></i> Self-scheduled</small>
                                @endif
                            </td>
                            <td>
                                @if($candidate->interview_schedule)
                                    <div class="fw-medium">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ $candidate->interview_schedule->format('M d, Y') }}
                                    </div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $candidate->interview_schedule->format('h:i A') }}
                                    </small>
                                @else
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    {{-- View Details --}}
                                    <button
                                        type="button"
                                        @class('btn btn-outline-info btn-sm')
                                        wire:click="viewCandidate({{ $candidate->id }})"
                                        title="View Details"
                                    >
                                        <i @class('bi bi-eye-fill')></i>
                                    </button>
                                    
                                    {{-- Send Scheduling Link --}}
                                    @if($candidate->status === 'scheduled' && !$candidate->self_scheduled)
                                        <button
                                            type="button"
                                            @class('btn btn-outline-primary btn-sm')
                                            wire:click="openSendLinkModal({{ $candidate->id }})"
                                            title="Send Self-Scheduling Link"
                                        >
                                            <i @class('bi bi-send')></i>
                                        </button>
                                    @endif

                                    {{-- Reschedule --}}
                                    <button
                                        type="button"
                                        @class('btn btn-outline-warning btn-sm')
                                        wire:click="openRescheduleModal({{ $candidate->id }})"
                                        title="Reschedule Interview"
                                    >
                                        <i @class('bi bi-calendar2-plus')></i>
                                    </button>

                                    {{-- Promote to Interview --}}
                                    @if($candidate->interview_schedule && $candidate->status === 'scheduled')
                                        <button
                                            type="button"
                                            @class('btn btn-success btn-sm')
                                            wire:click="promoteToInterview({{ $candidate->id }})"
                                            title="Move to Interviews"
                                        >
                                            <i @class('bi bi-arrow-right-circle')></i> Ready
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted py-5')>
                                <i @class('bi bi-person-x d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $candidates->links() }}
        </div>
    @endif

    {{-- View Candidate Modal --}}
    @if($showViewModal && $selectedCandidate)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Candidate Details</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
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

    {{-- Reschedule Modal --}}
    @if($showRescheduleModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
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

    {{-- Send Scheduling Link Modal --}}
    @if($showSendLinkModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-send me-2"></i>Send Self-Scheduling Link</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeSendLinkModal"></button>
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

</div>
