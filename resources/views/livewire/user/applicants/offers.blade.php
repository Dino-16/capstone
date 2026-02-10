@section('page-title', 'Offers')
@section('page-subtitle', 'Contract & Document Hub')
@section('breadcrumbs', 'Offers')

<div @class('pt-2')>

    {{-- PASSWORD GATE --}}
    @include('components.password-gate')

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
                        {{ $stats['approved'] ?? 0 }}
                    </div>

                    {{-- Label --}}
                    <div class="text-muted small">
                        Contract Approved
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
                    Status: {{ $statusFilter ? ($statusFilter === 'approved' ? 'Approved' : ucfirst($statusFilter)) : 'All' }}
                </button>

                <ul @class('dropdown-menu')>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">All Status</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'pending')">Pending</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'sent')">Sent</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'approved')">Approved</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'declined')">Declined</a>
                    </li>
                </ul>
            </div>

            {{-- DEPARTMENT FILTER --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-building me-2')></i>
                    Department: {{ $departmentFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu')>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('departmentFilter', '')">All Departments</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($filters['departments'] as $dept)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('departmentFilter', '{{ $dept }}')">{{ $dept }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- POSITION FILTER --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-briefcase me-2')></i>
                    Position: {{ $positionFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu')>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('positionFilter', '')">All Positions</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($filters['positions'] as $pos)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('positionFilter', '{{ $pos }}')">{{ $pos }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="d-flex gap-2">
            <button 
                type="button" 
                @class('btn btn-outline-primary')
                wire:click="openApprovalModal"
            >
                <i @class('bi bi-file-earmark-check me-2')></i>Contract Approval
            </button>
            <button 
                type="button" 
                @class('btn btn-success')
                wire:click="exportData"
            >
                <i @class('bi bi-download me-2')></i>Export
            </button>
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
                    <th @class('text-secondary')>Email</th>
                    <th @class('text-secondary')>Department</th>
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
                        </td>
                        <td>
                            <div>{{ $candidate->candidate_email }}</div>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $candidate->department ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $candidate->applied_position ?? 'N/A' }}</div>
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
                                    'approved' => 'success',
                                    'declined' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-{{ $contractColors[$candidate->contract_status] ?? 'secondary' }}">
                                {{ $candidate->contract_status === 'approved' ? 'Approved' : ucfirst($candidate->contract_status ?? 'pending') }}
                            </span>
                            @if($candidate->contract_approved_at)
                                <br><small class="text-success">
                                    <i class="bi bi-check-circle"></i> 
                                    {{ $candidate->contract_approved_at->format('M d, Y') }}
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
                                <br><small class="text-muted">
                                    {{ $candidate->documents_email_sent_at ? $candidate->documents_email_sent_at->format('M d, Y h:i A') : '' }}
                                </small>
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
                                 
                                 @if(in_array(session('user.position'), ['Super Admin', 'HR Manager']))
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
 
                                 {{-- Request Contract API --}}
                                 @if($candidate->contract_status === 'pending')
                                     <button
                                         type="button"
                                         @class('btn btn-sm btn-outline-warning')
                                         wire:click="openRequestContractModal({{ $candidate->id }})"
                                         title="Request Contract from Legal"
                                     >
                                         <i @class('bi bi-file-earmark-plus-fill me-1')></i>Request
                                     </button>
                                 @endif

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

                                 {{-- Quick Approve (if contract sent) --}}
                                 @if($candidate->contract_status === 'sent')
                                     <button
                                         type="button"
                                         @class('btn btn-sm btn-success')
                                         wire:click="markContractApproved({{ $candidate->id }})"
                                         title="Approve Contract"
                                     >
                                         <i @class('bi bi-check2-square text-white me-1')></i>Approve
                                     </button>
                                 @endif
 
                                 {{-- Send Document Email (if contract approved) --}}
                                 @if($candidate->contract_status === 'approved' && !$candidate->documents_email_sent)
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
                                 @if($candidate->contract_status === 'approved' && $candidate->documents_email_sent && $candidate->status !== 'hired')
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
                        <td colspan="8" @class('text-center text-muted py-5')>
                            @if($search)
                                <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates found matching "{{ $search }}".</div>
                            @elseif($statusFilter || $departmentFilter || $positionFilter)
                                <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates matching the selected filters found.</div>
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
    @include('livewire.user.applicants.includes.offer-view-modal')

    {{-- CONTRACT STATUS MODAL --}}
    @include('livewire.user.applicants.includes.offer-contract-status-modal')

    {{-- DOCUMENT EMAIL MODAL --}}
    @include('livewire.user.applicants.includes.offer-document-email-modal')
 
    {{-- CONTRACT EMAIL MODAL --}}
    @include('livewire.user.applicants.includes.offer-contract-email-modal')

    {{-- REQUEST CONTRACT MODAL --}}
    @include('livewire.user.applicants.includes.offer-request-contract-modal')

    {{-- Contract Approval Modal --}}
    @include('livewire.user.applicants.includes.contract-approval-modal')

</div>
