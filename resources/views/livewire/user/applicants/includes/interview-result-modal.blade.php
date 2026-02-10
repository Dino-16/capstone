    {{-- PASS/FAIL RESULT MODAL --}}
    @if($showResultModal && $resultCandidate)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(15, 23, 42, 0.9); backdrop-filter: blur(10px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-5 shadow-2xl overflow-hidden">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <button type="button" class="btn-close shadow-none" wire:click="closeResultModal"></button>
                </div>
                <div class="modal-body text-center px-5 pb-5">
                    <div class="mb-4">
                        <div class="bg-{{ $resultCandidate->interview_total_score >= 70 ? 'success' : ($resultCandidate->interview_total_score >= 50 ? 'warning' : 'danger') }} bg-opacity-10 d-inline-flex p-4 rounded-circle mb-4">
                            <i class="bi bi-award fs-1 text-{{ $resultCandidate->interview_total_score >= 70 ? 'success' : ($resultCandidate->interview_total_score >= 50 ? 'warning' : 'danger') }}"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">{{ $resultCandidate->candidate_name }}</h3>
                        <p class="text-secondary fw-medium">{{ $resultCandidate->applied_position }}</p>
                    </div>
                    
                    <div class="py-4 bg-light rounded-4 mb-4">
                        <div class="display-3 fw-black text-{{ $resultCandidate->interview_total_score >= 70 ? 'success' : ($resultCandidate->interview_total_score >= 50 ? 'warning' : 'danger') }}">
                            {{ number_format($resultCandidate->interview_total_score, 1) }}<small class="fs-4">%</small>
                        </div>
                        <div class="text-uppercase tracking-widest fw-bold text-secondary" style="font-size: 0.75rem;">
                            {{ ucfirst($resultCandidate->interview_stage) }} Stage Weighted Result
                        </div>
                    </div>
                    
                    <div class="text-start bg-light bg-opacity-50 p-3 rounded-3 mb-4 border">
                        <div class="d-flex align-items-start mb-2">
                            <i class="bi bi-check-circle-fill text-success mt-1 me-2"></i>
                            <span class="small"><strong>PASS:</strong> Moves candidate to <strong>{{ $nextStage ? ($nextStage === 'offer' ? 'Official Offer' : ucfirst($nextStage)) : 'Contract Generation' }}</strong>.</span>
                        </div>
                        <div class="d-flex align-items-start border-top pt-2 mt-2">
                            <i class="bi bi-x-circle-fill text-danger mt-1 me-2"></i>
                            <span class="small"><strong>FAIL:</strong> Immediately archive application as unsuitable.</span>
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-success btn-xl py-3 fw-bold rounded-4 shadow-success shadow-lg" wire:click="markAsPassed" wire:loading.attr="disabled">
                            <i class="bi bi-check-lg me-2"></i>PASS & PROMOTE
                        </button>
                        <button type="button" class="btn btn-outline-danger py-3 fw-bold rounded-4" wire:click="markAsFailed" wire:loading.attr="disabled">
                            <i class="bi bi-x-lg me-2"></i>MARK AS UNSUITABLE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
