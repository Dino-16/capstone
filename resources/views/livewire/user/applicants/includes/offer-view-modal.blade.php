    {{-- VIEW CANDIDATE MODAL --}}
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
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Employment Details</h6>
                            <p><strong>Position:</strong> {{ $selectedCandidate->applied_position ?? 'N/A' }}</p>
                            <p><strong>Department:</strong> {{ $selectedCandidate->department ?? 'N/A' }}</p>
                            <p>
                                <strong>Interview Score:</strong> 
                                @if($selectedCandidate->interview_total_score !== null)
                                    <span class="badge bg-success">{{ number_format($selectedCandidate->interview_total_score, 1) }}%</span>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Contract Status</h6>
                            <p>
                                <strong>Status:</strong>
                                <span class="badge bg-{{ 
                                    $selectedCandidate->contract_status === 'approved' ? 'success' : 
                                    ($selectedCandidate->contract_status === 'sent' ? 'info' : 
                                    ($selectedCandidate->contract_status === 'declined' ? 'danger' : 'warning'))
                                }}">
                                    {{ $selectedCandidate->contract_status === 'approved' ? 'Approved' : ucfirst($selectedCandidate->contract_status ?? 'pending') }}
                                </span>
                            </p>
                            @if($selectedCandidate->contract_sent_at)
                                <p><strong>Sent At:</strong> {{ $selectedCandidate->contract_sent_at->format('M d, Y h:i A') }}</p>
                            @endif
                            @if($selectedCandidate->contract_approved_at)
                                <p><strong>Approved At:</strong> {{ $selectedCandidate->contract_approved_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Documents Email</h6>
                            <p>
                                <strong>Status:</strong>
                                @if($selectedCandidate->documents_email_sent)
                                    <span class="badge bg-success">Sent</span>
                                @else
                                    <span class="badge bg-secondary">Not Sent</span>
                                @endif
                            </p>
                            @if($selectedCandidate->documents_email_sent_at)
                                <p><strong>Sent At:</strong> {{ $selectedCandidate->documents_email_sent_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>

                    @if($selectedCandidate->interview_scores && isset($selectedCandidate->interview_scores['notes']))
                        <hr>
                        <h6 class="fw-bold text-primary mb-3">Interview Notes</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $selectedCandidate->interview_scores['notes'] ?? 'No notes recorded.' }}
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeViewModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
