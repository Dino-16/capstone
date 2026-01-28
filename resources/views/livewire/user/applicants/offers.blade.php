@section('page-title', 'Offers')
@section('page-subtitle', 'Contract & Document Hub')
@section('breadcrumbs', 'Offers')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- STATS CARDS --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-warning">{{ $stats['pending'] ?? 0 }}</div>
                    <div class="text-muted small">Contract Pending</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-info">{{ $stats['sent'] ?? 0 }}</div>
                    <div class="text-muted small">Contract Sent</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-success">{{ $stats['signed'] ?? 0 }}</div>
                    <div class="text-muted small">Contract Signed</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-primary">{{ $stats['hired'] ?? 0 }}</div>
                    <div class="text-muted small">Hired</div>
                </div>
            </div>
        </div>
    </div>

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
            {{-- CONTRACT STATUS FILTER --}}
            <div>
                <select class="form-select" wire:model.live="statusFilter">
                    <option value="">All Contract Status</option>
                    <option value="pending">Pending</option>
                    <option value="sent">Sent</option>
                    <option value="signed">Signed</option>
                    <option value="declined">Declined</option>
                </select>
            </div>
        </div>
    </div>

    {{-- WORKFLOW INFO --}}
    <div @class('card mb-4 border-0 shadow-sm bg-light')>
        <div @class('card-body py-3')>
            <div @class('d-flex align-items-center gap-3')>
                <i class="bi bi-file-earmark-check-fill text-primary fs-4"></i>
                <div>
                    <strong>Step 4: Offering & Contract</strong>
                    <p class="mb-0 text-muted small">Track contract status and send document requirement emails. Once signed, complete onboarding to move to employees.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
        <h3><i class="bi bi-file-earmark-check me-2"></i>Candidates in Offering Stage</h3>
        <p @class('text-secondary mb-0')>
            Candidates who passed interview - awaiting contract signing
        </p>
    </div>
    <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
        <table @class('table table-hover')>
            <thead>
                <tr @class('bg-dark')>
                    <th @class('text-secondary')>Name</th>
                    <th @class('text-secondary')>Position</th>
                    <th @class('text-secondary')>Interview Score</th>
                    <th @class('text-secondary')>Contract Status</th>
                    <th @class('text-secondary')>Documents Email</th>
                    <th @class('text-secondary')>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidates as $candidate)
                    <tr wire:key="{{ $candidate->id }}" 
                        class="{{ $candidate->status === 'hired' ? 'table-success' : '' }}"
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
                            @if($candidate->interview_total_score !== null)
                                <span class="badge bg-{{ $candidate->interview_total_score >= 70 ? 'success' : ($candidate->interview_total_score >= 50 ? 'warning' : 'danger') }} fs-6">
                                    {{ number_format($candidate->interview_total_score, 1) }}%
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $contractColors = [
                                    'pending' => 'warning',
                                    'sent' => 'info',
                                    'signed' => 'success',
                                    'declined' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-{{ $contractColors[$candidate->contract_status] ?? 'secondary' }}">
                                {{ ucfirst($candidate->contract_status ?? 'pending') }}
                            </span>
                            @if($candidate->contract_signed_at)
                                <br><small class="text-success">
                                    <i class="bi bi-check-circle"></i> 
                                    {{ $candidate->contract_signed_at->format('M d, Y') }}
                                </small>
                            @elseif($candidate->contract_sent_at)
                                <br><small class="text-muted">
                                    Sent: {{ $candidate->contract_sent_at->format('M d, Y') }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($candidate->documents_email_sent)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Sent
                                </span>
                                @if($candidate->documents_email_sent_at)
                                    <br><small class="text-muted">
                                        {{ $candidate->documents_email_sent_at->format('M d, Y h:i A') }}
                                    </small>
                                @endif
                            @else
                                <span class="badge bg-secondary">Not Sent</span>
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

                                {{-- Update Contract Status --}}
                                <button
                                    type="button"
                                    @class('btn btn-outline-secondary btn-sm')
                                    wire:click="openContractModal({{ $candidate->id }})"
                                    title="Update Contract Status"
                                >
                                    <i @class('bi bi-file-earmark-text')></i>
                                </button>

                                {{-- Quick Sign (if contract sent) --}}
                                @if($candidate->contract_status === 'sent')
                                    <button
                                        type="button"
                                        @class('btn btn-success btn-sm')
                                        wire:click="markContractSigned({{ $candidate->id }})"
                                        title="Mark as Signed"
                                    >
                                        <i @class('bi bi-check2-square')></i>
                                    </button>
                                @endif

                                {{-- Send Document Email (if contract signed) --}}
                                @if($candidate->contract_status === 'signed' && !$candidate->documents_email_sent)
                                    <button
                                        type="button"
                                        @class('btn btn-primary btn-sm')
                                        wire:click="openEmailModal({{ $candidate->id }})"
                                        title="Send Document Requirements Email"
                                    >
                                        <i @class('bi bi-envelope-fill me-1')></i>Email
                                    </button>
                                @endif

                                {{-- Complete Onboarding --}}
                                @if($candidate->contract_status === 'signed' && $candidate->documents_email_sent && $candidate->status !== 'hired')
                                    <button
                                        type="button"
                                        @class('btn btn-warning btn-sm')
                                        wire:click="completeOnboarding({{ $candidate->id }})"
                                        title="Complete Onboarding"
                                    >
                                        <i @class('bi bi-person-check me-1')></i>Hire
                                    </button>
                                @endif

                                {{-- Hired Badge --}}
                                @if($candidate->status === 'hired')
                                    <span class="badge bg-success fs-6 ms-2">
                                        <i class="bi bi-trophy me-1"></i>HIRED
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" @class('text-center text-muted py-5')>
                            <i @class('bi bi-file-earmark-x d-block mx-auto fs-1')></i>
                            <div class="mt-3">No candidates in offering stage.</div>
                            <small>Candidates who pass interview will appear here.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $candidates->links() }}
    </div>

    {{-- VIEW CANDIDATE MODAL --}}
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
                                    $selectedCandidate->contract_status === 'signed' ? 'success' : 
                                    ($selectedCandidate->contract_status === 'sent' ? 'info' : 
                                    ($selectedCandidate->contract_status === 'declined' ? 'danger' : 'warning'))
                                }}">
                                    {{ ucfirst($selectedCandidate->contract_status ?? 'pending') }}
                                </span>
                            </p>
                            @if($selectedCandidate->contract_sent_at)
                                <p><strong>Sent At:</strong> {{ $selectedCandidate->contract_sent_at->format('M d, Y h:i A') }}</p>
                            @endif
                            @if($selectedCandidate->contract_signed_at)
                                <p><strong>Signed At:</strong> {{ $selectedCandidate->contract_signed_at->format('M d, Y h:i A') }}</p>
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

    {{-- CONTRACT STATUS MODAL --}}
    @if($showContractModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i>Update Contract Status</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeContractModal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Candidate:</strong> {{ $contractCandidateName }}</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contract Status</label>
                        <select class="form-select" wire:model="newContractStatus">
                            <option value="pending">Pending</option>
                            <option value="sent">Sent</option>
                            <option value="signed">Signed</option>
                            <option value="declined">Declined</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Changing to "Signed" will enable the Document Email button.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeContractModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="updateContractStatus" wire:loading.attr="disabled">
                        <span wire:loading.remove>Update Status</span>
                        <span wire:loading>Updating...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- DOCUMENT EMAIL MODAL --}}
    @if($showEmailModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-envelope-fill me-2"></i>Send Document Requirements Email</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeEmailModal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-person-check me-2"></i>
                        Sending to: <strong>{{ $emailCandidateName }}</strong> ({{ $emailCandidateEmail }})
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Subject</label>
                        <input type="text" class="form-control" wire:model="emailSubject">
                        @error('emailSubject') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Content (Document Explainer)</label>
                        <textarea 
                            class="form-control font-monospace" 
                            wire:model="emailContent" 
                            rows="20"
                            style="font-size: 0.85rem;"
                        ></textarea>
                        @error('emailContent') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Review the document list before sending. This email explains exactly what physical documents the candidate needs to prepare.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEmailModal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-lg" wire:click="sendDocumentEmail" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-send me-1"></i>Send Email</span>
                        <span wire:loading>Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
