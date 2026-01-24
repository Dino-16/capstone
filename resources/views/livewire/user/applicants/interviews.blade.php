<div>
@section('page-title', 'Interview Assessment')
@section('page-subtitle', 'Conduct interviews and practical exams')
@section('breadcrumbs', 'Interviews')

<div class="container-fluid py-4">
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('message') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Candidates Search Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-search me-2"></i>
                        Search Candidates
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" 
                                   class="form-control" 
                                   wire:model.live="search"
                                   placeholder="Search candidates by name, email, or phone...">
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small mt-2">
                                <i class="bi bi-info-circle"></i>
                                Type to search candidates
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidates Table -->
    @if($candidates->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="bi bi-people me-2"></i>
                            Available Candidates ({{ $candidates->total() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Interview Schedule</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($candidates as $candidate)
                                        <tr wire:key="{{ $candidate->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person fs-6"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $candidate->candidate_name }}</strong>
                                                        @if($candidate->interview_schedule)
                                                            <br><small class="text-muted">Interview: {{ $candidate->interview_schedule->format('M d, Y h:i A') }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $candidate->candidate_email }}</td>
                                            <td>{{ $candidate->candidate_phone }}</td>
                                            <td>
                                                <span class="badge bg-{{ $candidate->status == 'scheduled' ? 'warning' : ($candidate->status == 'completed' ? 'success' : 'secondary') }}">
                                                    {{ ucfirst($candidate->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($candidate->interview_schedule)
                                                    <span class="text-muted">
                                                        {{ $candidate->interview_schedule->format('M d, Y') }} at {{ $candidate->interview_schedule->format('h:i A') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Not scheduled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary" 
                                                            wire:click="selectCandidate({{ $candidate->id }})"
                                                            title="Select for interview">
                                                        <i class="bi bi-check-circle"></i> Select
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="bi bi-search d-block mb-2" style="font-size: 2rem;"></i>
                                                <div>
                                                    @if($search)
                                                        No candidates found for "<strong>{{ $search }}</strong>"
                                                    @else
                                                        No candidates found. Try adjusting your search criteria.
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($candidates->hasPages())
                            <div class="card-footer bg-white border-top">
                                {{ $candidates->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Progress Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-gradient-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-white mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Interview Assessment System
                        </h5>
                        <div class="badge bg-white text-primary">
                            Step {{ $currentStep }} of 4
                        </div>
                    </div>
                    
                    <!-- Progress Steps -->
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-white" 
                             role="progressbar" 
                             style="width: {{ ($currentStep / 4) * 100 }}%"
                             aria-valuenow="{{ ($currentStep / 4) * 100 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                    
                    <!-- Step Indicators -->
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-center">
                            <div class="rounded-circle {{ $currentStep >= 1 ? 'bg-white text-primary' : 'bg-white-50 text-white' }} d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <small class="text-white d-block mt-1">Candidate Info</small>
                        </div>
                        <div class="text-center">
                            <div class="rounded-circle {{ $currentStep >= 2 ? 'bg-white text-primary' : 'bg-white-50 text-white' }} d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <small class="text-white d-block mt-1">Interview</small>
                        </div>
                        <div class="text-center">
                            <div class="rounded-circle {{ $currentStep >= 3 ? 'bg-white text-primary' : 'bg-white-50 text-white' }} d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <small class="text-white d-block mt-1">Practical</small>
                        </div>
                        <div class="text-center">
                            <div class="rounded-circle {{ $currentStep >= 4 ? 'bg-white text-primary' : 'bg-white-50 text-white' }} d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-check2"></i>
                            </div>
                            <small class="text-white d-block mt-1">Review</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            
            <!-- Step 1: Candidate Information -->
            @if($currentStep == 1)
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                        <i class="bi bi-person-plus fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Candidate Information</h6>
                                        <small class="text-muted">Enter candidate details and select position</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="candidateName" class="form-label fw-semibold">
                                            <i class="bi bi-person me-1"></i> Candidate Name
                                        </label>
                                        <input type="text" 
                                               class="form-control form-control-lg" 
                                               id="candidateName" 
                                               wire:model.live="candidateName"
                                               placeholder="Enter candidate's full name">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="selectedPosition" class="form-label fw-semibold">
                                            <i class="bi bi-briefcase me-1"></i> Position Applied For
                                        </label>
                                        <select class="form-select form-select-lg" 
                                                id="selectedPosition" 
                                                wire:model.live="selectedPosition">
                                            <option value="">Select a position...</option>
                                            @foreach($jobs as $job)
                                                <option value="{{ $job->position }}">{{ $job->position }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                @if($candidateName && $selectedPosition)
                                    <div class="alert alert-info border-0 shadow-sm">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <div>
                                                <strong>Ready to start interview:</strong><br>
                                                <span class="text-muted">{{ $candidateName }} - {{ $selectedPosition }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary btn-lg" disabled>
                                        <i class="bi bi-arrow-left me-2"></i> Previous
                                    </button>
                                    <button type="button" 
                                            class="btn btn-primary btn-lg px-4" 
                                            wire:click="startInterview"
                                            @if(!$candidateName || !$selectedPosition) disabled @endif>
                                        <i class="bi bi-play-circle me-2"></i> Start Interview
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Interview Questions -->
            @if($currentStep == 2)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="bi bi-chat-dots fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Interview Questions</h6>
                                    <small class="text-muted">{{ $candidateName }} - {{ $selectedPosition }}</small>
                                </div>
                            </div>
                            <div class="badge bg-success">
                                {{ count($interviewQuestions) }} Questions
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if(count($interviewQuestions) > 0)
                            <div class="space-y-4">
                                @foreach($interviewQuestions as $index => $question)
                                    <div class="border rounded-lg p-4 bg-light">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 32px; height: 32px;">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2">Question {{ $index + 1 }}</h6>
                                                <p class="text-muted mb-3">{{ $question }}</p>
                                                <textarea class="form-control" 
                                                          wire:model="interviewAnswers.{{ $index }}"
                                                          rows="3"
                                                          placeholder="Enter candidate's answer..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning border-0 shadow-sm">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No interview questions available for this position.
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary btn-lg" wire:click="previousStep">
                                <i class="bi bi-arrow-left me-2"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-lg px-4" wire:click="nextStep">
                                Next <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 3: Practical Examination -->
            @if($currentStep == 3)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                    <i class="bi bi-pencil-square fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Practical Examination</h6>
                                    <small class="text-muted">{{ $candidateName }} - {{ $selectedPosition }}</small>
                                </div>
                            </div>
                            <div class="badge bg-warning">
                                {{ count($practicalExams) }} Tasks
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if(count($practicalExams) > 0)
                            <div class="space-y-4">
                                @foreach($practicalExams as $index => $exam)
                                    <div class="border rounded-lg p-4 bg-light">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 32px; height: 32px;">
                                                <i class="bi bi-pencil"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-2">Practical Task {{ $index + 1 }}</h6>
                                                <p class="text-muted mb-3">{{ $exam }}</p>
                                                <textarea class="form-control" 
                                                          wire:model="practicalAnswers.{{ $index }}"
                                                          rows="4"
                                                          placeholder="Enter candidate's practical exam response or solution..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning border-0 shadow-sm">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                No practical exams available for this position.
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary btn-lg" wire:click="previousStep">
                                <i class="bi bi-arrow-left me-2"></i> Previous
                            </button>
                            <button type="button" class="btn btn-primary btn-lg px-4" wire:click="nextStep">
                                Review & Submit <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 4: Review and Submit -->
            @if($currentStep == 4)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                <i class="bi bi-check2 fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Review and Submit</h6>
                                <small class="text-muted">{{ $candidateName }} - {{ $selectedPosition }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-primary text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-person fs-2 mb-2"></i>
                                        <h6 class="card-title">{{ $candidateName }}</h6>
                                        <small>{{ $selectedPosition }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-success text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-chat-dots fs-2 mb-2"></i>
                                        <h6 class="card-title">{{ count($interviewAnswers) }} / {{ count($interviewQuestions) }}</h6>
                                        <small>Interview Questions</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 bg-warning text-white">
                                    <div class="card-body text-center">
                                        <i class="bi bi-pencil-square fs-2 mb-2"></i>
                                        <h6 class="card-title">{{ count($practicalAnswers) }} / {{ count($practicalExams) }}</h6>
                                        <small>Practical Tasks</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Answer Previews -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-white border-bottom">
                                        <h6 class="mb-0">
                                            <i class="bi bi-chat-dots me-2"></i>Interview Answers
                                        </h6>
                                    </div>
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        @foreach($interviewAnswers as $index => $answer)
                                            @if($answer)
                                                <div class="mb-2 pb-2 border-bottom">
                                                    <small class="text-muted">Q{{ $index + 1 }}:</small>
                                                    <p class="mb-0">{{ Str::limit($answer, 100) }}</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-white border-bottom">
                                        <h6 class="mb-0">
                                            <i class="bi bi-pencil-square me-2"></i>Practical Answers
                                        </h6>
                                    </div>
                                    <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                        @foreach($practicalAnswers as $index => $answer)
                                            @if($answer)
                                                <div class="mb-2 pb-2 border-bottom">
                                                    <small class="text-muted">Task{{ $index + 1 }}:</small>
                                                    <p class="mb-0">{{ Str::limit($answer, 100) }}</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Please review all answers before submitting. This action will finalize the interview assessment.
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary btn-lg" wire:click="previousStep">
                                <i class="bi bi-arrow-left me-2"></i> Previous
                            </button>
                            <button type="button" class="btn btn-success btn-lg px-4" wire:click="submitInterview">
                                <i class="bi bi-check-circle me-2"></i> Submit Interview
                            </button>
                        </div>
                    </div>
                </div>
            @endif
            
        </div>
    </div>
</div>

<style>
.space-y-4 > * + * {
    margin-top: 1.5rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.rounded-lg {
    border-radius: 0.75rem !important;
}

.border-light {
    border-color: #e9ecef !important;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.form-control, .form-select {
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    transition: all 0.2s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

.progress-bar {
    transition: width 0.6s ease;
}
</style>
</div>
