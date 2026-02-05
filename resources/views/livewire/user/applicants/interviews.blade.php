@section('page-title', 'Interviews')
@section('page-subtitle', 'Exam & Scoring Hub')
@section('breadcrumbs', 'Interviews')

<div @class('pt-2')>
    {{-- TOAST --}}
    <x-toast />

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center mb-4')>
        <div @class('d-flex align-items-center gap-3')>
            {{-- SEARCH BAR --}}
            <div>
                <x-search-input
                    wire:model.live="search" 
                    placeholder="Search candidates..."
                />
            </div>

            {{-- STATUS FILTER --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: {{ $statusFilter ? ($statusFilter === 'interview_ready' ? 'Ready' : ucfirst($statusFilter)) : 'All' }}
                </button>

                <ul @class('dropdown-menu')>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">All Status</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'interview_ready')">Ready</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'interviewed')">Interviewed</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- WORKFLOW INFO --}}
    <div @class('card mb-4 border-0 shadow-sm bg-light')>
        <div @class('card-body py-3')>
            <div @class('d-flex align-items-center gap-3')>
                <i class="bi bi-clipboard-check-fill text-success fs-4"></i>
                <div>
                    <strong>Step 3: Interview & Exam Assessment</strong>
                    <p class="mb-0 text-muted small">Click on a candidate to open the Interview Modal. Score their responses and mark Pass/Fail to proceed.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- CANDIDATES FOR INTERVIEW TABLE --}}
    <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
        <h3><i class="bi bi-clipboard-check me-2"></i>Candidates Ready for Interview</h3>
        <p @class('text-secondary mb-0')>
            Click on a candidate to start the interview assessment
        </p>
    </div>
    <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
        <table @class('table table-hover')>
            <thead>
                <tr @class('bg-dark')>
                    <th @class('text-secondary')>Name</th>
                    <th @class('text-secondary')>Position</th>
                    <th @class('text-secondary')>AI Rating</th>
                    <th @class('text-secondary')>Interview Schedule</th>
                    <th @class('text-secondary')>Status</th>
                    <th @class('text-secondary')>Interview Score</th>
                    <th @class('text-secondary')>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidates as $candidate)
                    <tr wire:key="{{ $candidate->id }}" 
                        class="cursor-pointer {{ $candidate->status === 'interviewed' ? 'table-success' : '' }}"
                    >
                        <td>
                            <div class="fw-semibold">{{ $candidate->candidate_name }}</div>
                            <small class="text-muted">{{ $candidate->candidate_email }}</small>
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
                            @if($candidate->interview_schedule)
                                <div>
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
                            @php
                                $statusColors = [
                                    'interview_ready' => 'primary',
                                    'interviewed' => 'info',
                                ];
                                $statusLabels = [
                                    'interview_ready' => 'Ready',
                                    'interviewed' => 'Interviewed',
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$candidate->status] ?? 'secondary' }}">
                                {{ $statusLabels[$candidate->status] ?? ucfirst($candidate->status) }}
                            </span>
                        </td>
                        <td>
                            @if($candidate->interview_total_score !== null)
                                <span class="badge bg-{{ $candidate->interview_total_score >= 70 ? 'success' : ($candidate->interview_total_score >= 50 ? 'warning' : 'danger') }} fs-6">
                                    {{ number_format($candidate->interview_total_score, 1) }}%
                                </span>
                            @else
                                <span class="text-muted">Pending</span>
                            @endif
                        </td>
                        <td>
                                <button
                                    type="button"
                                    @class('btn btn-sm btn-primary')
                                    wire:click="openInterviewModal({{ $candidate->id }})"
                                    title="Start Interview Assessment"
                                >
                                    <i @class('bi bi-play-circle me-1')></i>
                                    {{ $candidate->status === 'interviewed' ? 'Review' : 'Start' }}
                                </button>
                                    @if(session('user.position') === 'Super Admin')
                                    <button
                                        type="button"
                                        @class('btn btn-sm btn-danger')
                                        wire:click="deleteCandidate({{ $candidate->id }})"
                                        wire:confirm="Are you sure you want to delete this candidate?"
                                        title="Delete"
                                    >
                                        <i @class('bi bi-trash')></i>
                                    </button>
                                @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" @class('text-center text-muted py-5')>
                            @if($search)
                                <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates found matching "{{ $search }}".</div>
                            @elseif($statusFilter)
                                <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                <div class="mt-3">No {{ $statusFilter === 'interview_ready' ? 'ready' : $statusFilter }} candidates found.</div>
                            @else
                                <i @class('bi bi-clipboard-x d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates ready for interview.</div>
                                <small>Promote candidates from the Candidates page to see them here.</small>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $candidates->links() }}
    </div>

    {{-- INTERVIEW ASSESSMENT MODAL --}}
    @if($showInterviewModal && $selectedCandidate)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.7);">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-check me-2"></i>
                        Interview Assessment - {{ $selectedCandidate->candidate_name }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeInterviewModal"></button>
                </div>
                <div class="modal-body" style="overflow-y: auto; max-height: calc(100vh - 180px);">
                    <div class="container-fluid">
                        {{-- Candidate Info Header --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Position:</strong> {{ $selectedCandidate->applied_position ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Department:</strong> {{ $selectedCandidate->department ?? 'N/A' }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>AI Rating:</strong>
                                                @if($selectedCandidate->rating_score)
                                                    <span class="badge bg-{{ \App\Models\Applicants\Candidate::getRatingBadgeColor($selectedCandidate->rating_score) }}">
                                                        {{ number_format($selectedCandidate->rating_score, 1) }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Schedule:</strong>
                                                @if($selectedCandidate->interview_schedule)
                                                    {{ $selectedCandidate->interview_schedule->format('M d, Y h:i A') }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Interview Questions Section --}}
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0 fw-bold"><i class="bi bi-chat-quote me-2"></i>Interview Questions</h6>
                                    </div>
                                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                        @if(count($interviewQuestions) > 0)
                                            @foreach($interviewQuestions as $index => $question)
                                                <div class="mb-4 p-3 border rounded bg-light">
                                                    <label class="form-label fw-semibold text-primary">
                                                        Question {{ $index + 1 }}
                                                    </label>
                                                    <p class="mb-2">{{ $question }}</p>
                                                    <textarea 
                                                        class="form-control mb-2" 
                                                        wire:model="interviewScores.{{ $index }}.answer"
                                                        rows="2"
                                                        placeholder="Enter candidate's answer..."
                                                    ></textarea>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <label class="form-label mb-0 small">Score (0-10):</label>
                                                        <input 
                                                            type="number" 
                                                            class="form-control form-control-sm" 
                                                            style="width: 80px;"
                                                            wire:model="interviewScores.{{ $index }}.score"
                                                            min="0" 
                                                            max="10" 
                                                            step="0.5"
                                                        >
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                No interview questions available for this position.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Practical Examination Section --}}
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Practical Examination</h6>
                                    </div>
                                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                        @if(count($practicalExams) > 0)
                                            @foreach($practicalExams as $index => $exam)
                                                <div class="mb-4 p-3 border rounded bg-light">
                                                    <label class="form-label fw-semibold text-warning">
                                                        Practical Task {{ $index + 1 }}
                                                    </label>
                                                    <p class="mb-2">{{ $exam }}</p>
                                                    <textarea 
                                                        class="form-control mb-2" 
                                                        wire:model="practicalScores.{{ $index }}.response"
                                                        rows="3"
                                                        placeholder="Enter candidate's response or solution..."
                                                    ></textarea>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <label class="form-label mb-0 small">Score (0-10):</label>
                                                        <input 
                                                            type="number" 
                                                            class="form-control form-control-sm" 
                                                            style="width: 80px;"
                                                            wire:model="practicalScores.{{ $index }}.score"
                                                            min="0" 
                                                            max="10" 
                                                            step="0.5"
                                                        >
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                No practical exams available for this position.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Overall Notes --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Overall Notes & Observations</h6>
                                    </div>
                                    <div class="card-body">
                                        <textarea 
                                            class="form-control" 
                                            wire:model="overallNotes"
                                            rows="4"
                                            placeholder="Enter any additional observations, notes, or comments about the candidate's performance..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" wire:click="closeInterviewModal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-lg" wire:click="submitInterview" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-check2-circle me-1"></i>Submit Assessment</span>
                        <span wire:loading>Submitting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- PASS/FAIL RESULT MODAL --}}
    @if($showResultModal && $resultCandidate)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.7);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-award me-2"></i>Interview Result</h5>
                    <button type="button" class="btn-close" wire:click="closeResultModal"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <h4 class="mb-3">{{ $resultCandidate->candidate_name }}</h4>
                    <p class="text-muted mb-4">{{ $resultCandidate->applied_position }}</p>
                    
                    <div class="display-1 fw-bold mb-3 text-{{ $resultCandidate->interview_total_score >= 70 ? 'success' : ($resultCandidate->interview_total_score >= 50 ? 'warning' : 'danger') }}">
                        {{ number_format($resultCandidate->interview_total_score, 1) }}%
                    </div>
                    <p class="text-muted">Interview Score</p>
                    
                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Pass</strong> will trigger the contract preparation API to the external department.
                    </div>
                </div>
                <div class="modal-footer justify-content-center gap-3">
                    <button type="button" class="btn btn-danger btn-lg px-5" wire:click="markAsFailed" wire:loading.attr="disabled">
                        <i class="bi bi-x-circle me-2"></i>FAIL
                    </button>
                    <button type="button" class="btn btn-success btn-lg px-5" wire:click="markAsPassed" wire:loading.attr="disabled">
                        <i class="bi bi-check-circle me-2"></i>PASS
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .cursor-pointer:hover {
            background-color: #f8f9fa;
        }
    </style>
</div>
