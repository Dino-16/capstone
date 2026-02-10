    {{-- INTERVIEW ASSESSMENT MODAL --}}
    @if($showInterviewModal && $selectedCandidate)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(15, 23, 42, 0.8); backdrop-filter: blur(8px);">
        <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-0 shadow-2xl overflow-hidden" style="max-height: calc(100vh - 40px);">
                <div class="modal-header bg-white border-bottom py-3 px-5 sticky-top z-3">
                    <h5 class="modal-title d-flex align-items-center fw-bold text-dark">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-person-badge text-primary fs-4"></i>
                        </div>
                        Interview Assessment: <span class="text-primary ms-2">{{ $selectedCandidate->candidate_name }}</span>
                    </h5>
                    <button type="button" class="btn-close shadow-none" wire:click="closeInterviewModal"></button>
                </div>
                <div class="modal-body p-0" style="overflow-y: auto; max-height: calc(100vh - 140px);">
                    {{-- Interview Stage Progress --}}
                    @php
                        $currentStage = $selectedCandidate->interview_stage ?? 'initial';
                        $stages = ['initial', 'practical', 'demo'];
                        $currentIndex = array_search($currentStage, $stages);
                    @endphp
                    <div class="bg-white border-bottom py-4 px-5">
                        <div class="container-max mx-auto">
                            <div class="d-flex justify-content-between align-items-start position-relative">
                                {{-- Background Line --}}
                                <div class="position-absolute top-50 start-0 translate-middle-y w-100" style="height: 2px; background: #e2e8f0; z-index: 1;"></div>
                                
                                @foreach($interviewStages as $stageKey => $stage)
                                    @php
                                        $stageIndex = array_search($stageKey, $stages);
                                        $isActive = $stageKey === $currentStage;
                                        $isComplete = $stageIndex < $currentIndex;
                                        $color = $stage['color'];
                                    @endphp
                                    <div class="d-flex flex-column align-items-center text-center position-relative" style="z-index: 2; width: 120px;">
                                        <div 
                                            class="rounded-circle d-flex align-items-center justify-content-center border-4 transition-all {{ $isActive ? 'bg-'.$color.' border-white shadow-lg scale-110' : ($isComplete ? 'bg-success border-white shadow-sm' : 'bg-white border-light text-muted') }}" 
                                            style="width: 54px; height: 54px; cursor: pointer; transition: all 0.3s ease;"
                                            wire:click="updateInterviewStage({{ $selectedCandidate->id }}, '{{ $stageKey }}')"
                                        >
                                            <i class="bi {{ $isComplete ? 'bi-check-lg' : $stage['icon'] }} {{ $isActive || $isComplete ? 'text-white' : 'text-secondary' }} fs-5"></i>
                                        </div>
                                        <div class="mt-3">
                                            <div class="fw-bold small {{ $isActive ? 'text-primary' : ($isComplete ? 'text-success' : 'text-secondary') }}">{{ $stage['label'] }}</div>
                                            <div class="text-muted" style="font-size: 0.7rem;">{{ $stage['description'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="container-max mx-auto px-4 py-4">
                        {{-- Candidate Info Header --}}
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden rounded-4">
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    <div class="col-md-3 border-end p-4 bg-white">
                                        <label class="text-uppercase tracking-wider text-muted fw-semibold mb-1" style="font-size: 0.65rem;">Position & Dept</label>
                                        <div class="fw-bold text-dark fs-6">{{ $selectedCandidate->applied_position ?? 'N/A' }}</div>
                                        <div class="small text-primary fw-medium">{{ $selectedCandidate->department ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-3 border-end p-4 bg-white text-center">
                                        <label class="text-uppercase tracking-wider text-muted fw-semibold mb-1" style="font-size: 0.65rem;">Current Assessment</label>
                                        <div>
                                            @php $modalStageInfo = $interviewStages[$currentStage] ?? $interviewStages['initial']; @endphp
                                            <span class="badge rounded-pill bg-{{ $modalStageInfo['color'] }} bg-opacity-10 text-{{ $modalStageInfo['color'] }} px-3 py-2">
                                                <i class="bi {{ $modalStageInfo['icon'] }} me-1"></i>
                                                {{ $modalStageInfo['label'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 border-end p-4 bg-white text-center">
                                        <label class="text-uppercase tracking-wider text-muted fw-semibold mb-1" style="font-size: 0.65rem;">Candidate AI Rating</label>
                                        <div class="d-flex align-items-center justify-content-center">
                                            @if($selectedCandidate->rating_score)
                                                <div class="bg-{{ \App\Models\Applicants\Candidate::getRatingBadgeColor($selectedCandidate->rating_score) }} text-white fw-bold rounded px-2 py-1" style="font-size: 0.9rem;">
                                                    {{ number_format($selectedCandidate->rating_score, 1) }}
                                                </div>
                                            @else
                                                <span class="text-muted small">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3 p-4 bg-white text-end">
                                        <label class="text-uppercase tracking-wider text-muted fw-semibold mb-1" style="font-size: 0.65rem;">Scheduled Date</label>
                                        <div class="fw-bold text-dark">
                                            @if($selectedCandidate->interview_schedule)
                                                <i class="bi bi-calendar3 me-1 text-primary"></i>
                                                {{ $selectedCandidate->interview_schedule->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">Not Set</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">
                                            @if($selectedCandidate->interview_schedule)
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $selectedCandidate->interview_schedule->format('h:i A') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            @php 
                                $stageTitle = ['initial' => 'Interview Questions', 'practical' => 'Practical Examination', 'demo' => 'Demo Presentation'];
                                $stageIcon = ['initial' => 'bi-chat-left-dots', 'practical' => 'bi-pencil-square', 'demo' => 'bi-display'];
                                $stageColor = ['initial' => 'info', 'practical' => 'warning', 'demo' => 'success'];
                            @endphp
                            
                            {{-- Active Assessment Section --}}
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-{{ $stageColor[$currentStage] }} bg-opacity-10 p-2 rounded-3 me-3">
                                                <i class="bi {{ $stageIcon[$currentStage] }} text-{{ $stageColor[$currentStage] }} fs-5"></i>
                                            </div>
                                            <h6 class="mb-0 fw-bold">{{ $stageTitle[$currentStage] }}</h6>
                                        </div>
                                        <span class="badge rounded-pill bg-light text-dark border px-3">Stage Assessment</span>
                                    </div>
                                    <div class="card-body p-4 bg-light bg-opacity-50">
                                        <div class="alert bg-white border shadow-xs py-3 mb-3 rounded-3 d-flex align-items-center">
                                            <div class="text-primary fs-4 me-3">
                                                <i class="bi bi-lightbulb"></i>
                                            </div>
                                            <div class="small text-secondary">
                                                <strong>Evaluator Tip:</strong> Please provide objective scores and detailed answers/observations to ensure accurate candidate profiling.
                                            </div>
                                        </div>

                                        {{-- Scoring Guide --}}
                                        <div class="row g-2 mb-4">
                                            <div class="col-3">
                                                <div class="text-center p-2 rounded-3 bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">
                                                    <div class="fw-bold small">1 - 3</div>
                                                    <div style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em;">Developing</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="text-center p-2 rounded-3 bg-warning bg-opacity-10 text-dark border border-warning border-opacity-10">
                                                    <div class="fw-bold small">4 - 6</div>
                                                    <div style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em;">Competent</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="text-center p-2 rounded-3 bg-info bg-opacity-10 text-info border border-info border-opacity-10">
                                                    <div class="fw-bold small">7 - 8</div>
                                                    <div style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em;">Proficient</div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="text-center p-2 rounded-3 bg-success bg-opacity-10 text-success border border-success border-opacity-10">
                                                    <div class="fw-bold small">9 - 10</div>
                                                    <div style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em;">Exceptional</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($currentStage === 'initial')
                                            @forelse($interviewQuestions as $index => $question)
                                                <div class="mb-4 bg-white p-4 rounded-4 border shadow-xs transition-hover">
                                                    <div class="d-flex justify-content-between mb-3">
                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Question {{ $index + 1 }}</span>
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-3 pe-5" style="line-height: 1.5;">{{ $question }}</h6>
                                                    <textarea 
                                                        class="form-control SaaS-input mb-3" 
                                                        wire:model="interviewScores.{{ $index }}.answer"
                                                        rows="3"
                                                        placeholder="Type candidate's response here..."
                                                    ></textarea>
                                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                                        <span class="small text-muted fw-medium">Proficiency Score:</span>
                                                        <div class="d-flex align-items-center bg-light rounded-3 p-1 border">
                                                            <input 
                                                                type="range" 
                                                                class="form-range mx-2" 
                                                                min="1" max="10" step="0.5" 
                                                                wire:model.live="interviewScores.{{ $index }}.score"
                                                                style="width: 150px;"
                                                            >
                                                            <span class="badge bg-primary text-white fs-6 py-2 px-3 rounded-3" style="min-width: 45px;">
                                                                {{ $interviewScores[$index]['score'] ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-5">
                                                    <img src="https://illustrations.popsy.co/gray/box.svg" alt="Empty" style="width: 150px;" class="mb-3">
                                                    <h6 class="text-muted">No questions found for this position.</h6>
                                                </div>
                                            @endforelse

                                        @elseif($currentStage === 'practical')
                                            @forelse($practicalExams as $index => $exam)
                                                <div class="mb-4 bg-white p-4 rounded-4 border shadow-xs">
                                                    <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-3 mb-3">Task {{ $index + 1 }}</span>
                                                    <h6 class="fw-bold text-dark mb-3" style="line-height: 1.5;">{{ $exam }}</h6>
                                                    <textarea 
                                                        class="form-control SaaS-input mb-3" 
                                                        wire:model="practicalScores.{{ $index }}.response"
                                                        rows="4"
                                                        placeholder="Describe how the candidate performed this task..."
                                                    ></textarea>
                                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                                        <span class="small text-muted fw-medium">Execution Grade:</span>
                                                        <div class="d-flex align-items-center bg-light rounded-3 p-1 border">
                                                            <input 
                                                                type="range" 
                                                                class="form-range mx-2" 
                                                                min="1" max="10" step="0.5" 
                                                                wire:model.live="practicalScores.{{ $index }}.score"
                                                                style="width: 150px;"
                                                            >
                                                            <span class="badge bg-warning text-dark fs-6 py-2 px-3 rounded-3" style="min-width: 45px;">
                                                                {{ $practicalScores[$index]['score'] ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-5">
                                                    <h6 class="text-muted">No physical examination tasks assigned.</h6>
                                                </div>
                                            @endforelse

                                        @elseif($currentStage === 'demo')
                                            @forelse($demoInstructions as $index => $instruction)
                                                <div class="mb-4 bg-white p-4 rounded-4 border shadow-xs">
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 mb-3">Scenario {{ $index + 1 }}</span>
                                                    <h6 class="fw-bold text-dark mb-3" style="line-height: 1.5;">{{ $instruction }}</h6>
                                                    <textarea 
                                                        class="form-control SaaS-input mb-3" 
                                                        wire:model="demoScores.{{ $index }}.notes"
                                                        rows="4"
                                                        placeholder="Notes on communication, presentation, and logic..."
                                                    ></textarea>
                                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                                        <span class="small text-muted fw-medium">Presentation Rating:</span>
                                                        <div class="d-flex align-items-center bg-light rounded-3 p-1 border">
                                                            <input 
                                                                type="range" 
                                                                class="form-range mx-2" 
                                                                min="1" max="10" step="0.5" 
                                                                wire:model.live="demoScores.{{ $index }}.score"
                                                                style="width: 150px;"
                                                            >
                                                            <span class="badge bg-success text-white fs-6 py-2 px-3 rounded-3" style="min-width: 45px;">
                                                                {{ $demoScores[$index]['score'] ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-5 text-muted">No demo scenarios available.</div>
                                            @endforelse
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Overall Notes --}}
                            <div class="col-12">
                                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                                    <div class="card-header bg-white border-bottom py-3 px-4">
                                        <h6 class="mb-0 fw-bold d-flex align-items-center">
                                            <i class="bi bi-journal-text me-2 text-primary"></i>
                                            Final Assessment Verdict & Notes
                                        </h6>
                                    </div>
                                    <div class="card-body p-4">
                                        <textarea 
                                            class="form-control SaaS-input" 
                                            wire:model="overallNotes"
                                            rows="4"
                                            placeholder="Summarize the candidate's strengths, weaknesses, and your overall recommendation for this stage..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white container-fluid px-5 py-3 border-top border-2">
                    <div class="container-max mx-auto d-flex justify-content-between w-100 align-items-center">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none fw-semibold px-0" wire:click="closeInterviewModal">
                            <i class="bi bi-x-circle me-1"></i> Discard & Close
                        </button>
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-outline-secondary px-4 fw-semibold rounded-3" wire:click="closeInterviewModal">Cancel</button>
                            <button type="button" class="btn btn-primary btn-lg px-5 fw-bold rounded-3 shadow-primary" wire:click="submitInterview" wire:loading.attr="disabled">
                                <span><i class="bi bi-check2-circle me-1"></i> Complete This Stage</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
