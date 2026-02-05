@section('page-title', 'Offers')
@section('page-subtitle', 'Contract & Document Hub')
@section('breadcrumbs', 'Offers')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 h-100">
                {{-- Icon --}}
                <div class="mb-2">
                    <i class="bi bi-hourglass-split text-warning fs-3"></i>
                </div>

                <div class="ps-2">
                    {{-- Count --}}
                    <div class="fw-semibold fs-4">
                        {{ $stats['pending'] ?? 0 }}
                    </div>

                    {{-- Label --}}
                    <div class="text-muted small">
                        Contract Pending
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 h-100">
                {{-- Icon --}}
                <div class="mb-2">
                    <i class="bi bi-send-fill text-info fs-3"></i>
                </div>

                <div class="ps-2">
                    {{-- Count --}}
                    <div class="fw-semibold fs-4">
                        {{ $stats['sent'] ?? 0 }}
                    </div>

                    {{-- Label --}}
                    <div class="text-muted small">
                        Contract Sent
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 h-100">
                {{-- Icon --}}
                <div class="mb-2">
                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                </div>

                <div class="ps-2">
                    {{-- Count --}}
                    <div class="fw-semibold fs-4">
                        {{ $stats['signed'] ?? 0 }}
                    </div>

                    {{-- Label --}}
                    <div class="text-muted small">
                        Contract Signed
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0 h-100">
                {{-- Icon --}}
                <div class="mb-2">
                    <i class="bi bi-person-check-fill text-primary fs-3"></i>
                </div>

                <div class="ps-2">
                    {{-- Count --}}
                    <div class="fw-semibold fs-4">
                        {{ $stats['hired'] ?? 0 }}
                    </div>

                    {{-- Label --}}
                    <div class="text-muted small">
                        Hired
                    </div>
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
                <x-search-input
                    wire:model.live="search" 
                    placeholder="Search candidates..."
                />
            </div>
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: {{ $statusFilter ? ucfirst($statusFilter) : 'All' }}
                </button>

                <ul @class('dropdown-menu')>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">All Contract Status</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'pending')">Pending</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'sent')">Sent</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'signed')">Signed</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'declined')">Declined</a>
                    </li>
                </ul>
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
                             <div class="d-flex gap-2 flex-wrap">
                                 {{-- View Details --}}
                                 <button
                                     type="button"
                                     @class('btn btn-sm btn-outline-primary')
                                     wire:click="viewCandidate({{ $candidate->id }})"
                                     title="View Details"
                                 >
                                     <i @class('bi bi-eye')></i>
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
 
                                 {{-- Update Contract Status --}}
                                 <button
                                     type="button"
                                     @class('btn btn-sm btn-outline-secondary')
                                     wire:click="openContractModal({{ $candidate->id }})"
                                     title="Update Contract Status"
                                 >
                                     <i @class('bi bi-file-earmark-text')></i>
                                 </button>
 
                                 {{-- Send Contract Email --}}
                                 @if($candidate->contract_status === 'pending' || $candidate->contract_status === 'sent')
                                     <button
                                         type="button"
                                         @class('btn btn-sm btn-outline-info')
                                         wire:click="openContractEmailModal({{ $candidate->id }})"
                                         title="Send Contract Email"
                                     >
                                         <i @class('bi bi-send-plus-fill me-1')></i>Contract
                                     </button>
                                 @endif

                                 {{-- Quick Sign (if contract sent) --}}
                                 @if($candidate->contract_status === 'sent')
                                     <button
                                         type="button"
                                         @class('btn btn-sm btn-success')
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
                                         @class('btn btn-sm btn-outline-primary')
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
                                         @class('btn btn-sm btn-success')
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
                            @if($search)
                                <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates found matching "{{ $search }}".</div>
                            @elseif($statusFilter)
                                <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates with "{{ $statusFilter }}" contract status found.</div>
                            @else
                                <i @class('bi bi-file-earmark-x d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates in offering stage.</div>
                                <small>Candidates who pass interview will appear here.</small>
                            @endif
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i>Update Contract Status</h5>
                    <button type="button" class="btn-close" wire:click="closeContractModal"></button>
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
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-envelope-fill me-2"></i>Send Document Requirements Email</h5>
                    <button type="button" class="btn-close" wire:click="closeEmailModal"></button>
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
 
    {{-- CONTRACT EMAIL MODAL --}}
    @if($showContractEmailModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom shadow-sm">
                    <h5 class="modal-title text-primary"><i class="bi bi-file-earmark-medical me-2"></i>Send Employment Contract</h5>
                    <button type="button" class="btn-close" wire:click="closeContractEmailModal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Subject</label>
                        <input type="text" class="form-control" wire:model="contractEmailSubject">
                        @error('contractEmailSubject') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Contract Content</label>
                        <div class="bg-white border p-4 rounded shadow-sm">
                            <textarea 
                                class="form-control border-0 font-monospace" 
                                wire:model="contractEmailContent" 
                                rows="15"
                                style="font-size: 0.9rem; resize: none; background: transparent;"
                            ></textarea>
                        </div>
                        @error('contractEmailContent') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            Sending this email will automatically update the candidate's contract status to <strong>"Sent"</strong> and record the timestamp.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-outline-secondary" wire:click="closeContractEmailModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" wire:click="sendContractEmail" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-send-check-fill me-2"></i>Send Contract</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Sending...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
