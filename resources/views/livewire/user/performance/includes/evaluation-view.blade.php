{{-- View Evaluation Modal --}}
<div wire:modal="showViewModal" class="modal fade" id="viewEvaluationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Evaluation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($viewingEvaluation)
                    <div class="row">
                        {{-- Employee Information --}}
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Employee Information</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Employee Name</label>
                                <p class="form-control-plaintext fw-semibold">{{ $viewingEvaluation->employee_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Email</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->email ?: 'Not provided' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Position</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->position ?: 'Not specified' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Department</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->department ?: 'Not specified' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Employment Date</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->employment_date ? $viewingEvaluation->employment_date->format('M d, Y') : 'Not specified' }}</p>
                            </div>
                        </div>

                        {{-- Evaluation Information --}}
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Evaluation Information</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Evaluation Date</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->evaluation_date->format('M d, Y') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Evaluator Name</label>
                                <p class="form-control-plaintext fw-semibold">{{ $viewingEvaluation->evaluator_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Evaluation Type</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->evaluation_type }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Status</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $viewingEvaluation->status == 'Completed' ? 'success' : ($viewingEvaluation->status == 'Ongoing' ? 'warning' : 'secondary') }}">
                                        {{ $viewingEvaluation->status }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Overall Score</label>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 10px;">
                                        <div class="progress-bar {{ $viewingEvaluation->overall_score >= 80 ? 'bg-success' : ($viewingEvaluation->overall_score >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $viewingEvaluation->overall_score }}%"></div>
                                    </div>
                                    <span class="badge {{ $viewingEvaluation->overall_score >= 80 ? 'bg-success' : ($viewingEvaluation->overall_score >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ $viewingEvaluation->overall_score }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Performance Scores --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Performance Scores</h6>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Job Knowledge</span>
                                        <span class="badge bg-secondary">{{ $viewingEvaluation->job_knowledge }}/100</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $viewingEvaluation->job_knowledge >= 80 ? 'bg-success' : ($viewingEvaluation->job_knowledge >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $viewingEvaluation->job_knowledge }}%"></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Work Quality</span>
                                        <span class="badge bg-secondary">{{ $viewingEvaluation->work_quality }}/100</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $viewingEvaluation->work_quality >= 80 ? 'bg-success' : ($viewingEvaluation->work_quality >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $viewingEvaluation->work_quality }}%"></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Initiative</span>
                                        <span class="badge bg-secondary">{{ $viewingEvaluation->initiative }}/100</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $viewingEvaluation->initiative >= 80 ? 'bg-success' : ($viewingEvaluation->initiative >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $viewingEvaluation->initiative }}%"></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Communication</span>
                                        <span class="badge bg-secondary">{{ $viewingEvaluation->communication }}/100</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $viewingEvaluation->communication >= 80 ? 'bg-success' : ($viewingEvaluation->communication >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $viewingEvaluation->communication }}%"></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Dependability</span>
                                        <span class="badge bg-secondary">{{ $viewingEvaluation->dependability }}/100</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $viewingEvaluation->dependability >= 80 ? 'bg-success' : ($viewingEvaluation->dependability >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $viewingEvaluation->dependability }}%"></div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Attendance</span>
                                        <span class="badge bg-secondary">{{ $viewingEvaluation->attendance }}/100</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $viewingEvaluation->attendance >= 80 ? 'bg-success' : ($viewingEvaluation->attendance >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                             style="width: {{ $viewingEvaluation->attendance }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Performance Areas --}}
                    @if($viewingEvaluation->performance_areas)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Performance Areas</h6>
                            <p class="form-control-plaintext">{{ $viewingEvaluation->performance_areas }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Comments Section --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">Comments & Feedback</h6>
                            
                            @if($viewingEvaluation->strengths)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Strengths</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->strengths }}</p>
                            </div>
                            @endif

                            @if($viewingEvaluation->areas_for_improvement)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Areas for Improvement</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->areas_for_improvement }}</p>
                            </div>
                            @endif

                            @if($viewingEvaluation->comments)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Evaluator Comments</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->comments }}</p>
                            </div>
                            @endif

                            @if($viewingEvaluation->employee_comments)
                            <div class="mb-3">
                                <label class="form-label text-muted small">Employee Comments</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->employee_comments }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Signatures --}}
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Signatures</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Evaluator Signature</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->evaluator_signature ?: 'Not signed' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Employee Signature</label>
                                <p class="form-control-plaintext">{{ $viewingEvaluation->employee_signature ?: 'Not signed' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Metadata --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Created: {{ $viewingEvaluation->created_at->format('M d, Y h:i A') }}</span>
                                <span>Last Updated: {{ $viewingEvaluation->updated_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
