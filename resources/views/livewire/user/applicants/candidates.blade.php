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
                    Status: {{ $statusFilter ? ucfirst(str_replace('_', ' ', $statusFilter)) : 'All' }}
                </button>

                <ul @class('dropdown-menu')>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">All Status</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'scheduled')">Scheduled</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'interview_ready')">Interview Ready</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'failed')">Failed</a>
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
        <div>
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
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Department</th>
                        <th @class('text-secondary')>Position</th>
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
                                @php
                                    $statusColors = [
                                        'scheduled' => 'warning',
                                        'interview_ready' => 'success',
                                        'failed' => 'danger',
                                    ];
                                    $statusLabels = [
                                        'scheduled' => 'Scheduled',
                                        'interview_ready' => 'Interview Ready',
                                        'failed' => 'Failed',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$candidate->status] ?? 'secondary' }}">
                                    @if($candidate->status === 'failed')
                                        Failed ({{ ucfirst($candidate->interview_stage) }})
                                    @else
                                        {{ $statusLabels[$candidate->status] ?? ucfirst(str_replace('_', ' ', $candidate->status)) }}
                                    @endif
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
                                 <div class="d-flex gap-2 flex-wrap">
                                    {{-- Delete Candidate --}}
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

                                    {{-- Edit Candidate --}}
                                    @if(session('user.position') === 'HR Manager')
                                        <button
                                            type="button"
                                            @class('btn btn-sm btn-outline-primary')
                                            wire:click="editCandidate({{ $candidate->id }})"
                                            title="Edit Details"
                                        >
                                            <i @class('bi bi-pencil')></i>
                                        </button>
                                    @endif

                                     {{-- View Details --}}
                                     <button
                                         type="button"
                                         @class('btn btn-sm btn-outline-primary')
                                         wire:click="viewCandidate({{ $candidate->id }})"
                                         title="View Details"
                                     >
                                         <i @class('bi bi-eye')></i>
                                     </button>
                                     
                                     {{-- Send Scheduling Link --}}
                                     @if($candidate->status === 'scheduled' && !$candidate->self_scheduled)
                                         <button
                                             type="button"
                                             @class('btn btn-sm btn-outline-primary')
                                             wire:click="openSendLinkModal({{ $candidate->id }})"
                                             title="Send Self-Scheduling Link"
                                         >
                                             <i @class('bi bi-send')></i>
                                         </button>
                                     @endif
 
                                     {{-- Reschedule --}}
                                     <button
                                         type="button"
                                         @class('btn btn-sm btn-outline-warning')
                                         wire:click="openRescheduleModal({{ $candidate->id }})"
                                         title="Reschedule Interview"
                                     >
                                         <i @class('bi bi-calendar2-plus')></i>
                                     </button>
 
                                     {{-- Promote to Interview --}}
                                     @if($candidate->interview_schedule && $candidate->status === 'scheduled')
                                         <button
                                             type="button"
                                             @class('btn btn-sm btn-success')
                                             wire:click="promoteToInterview({{ $candidate->id }})"
                                             title="Move to Interviews"
                                         >
                                             <i @class('bi bi-arrow-right-circle')></i>
                                         </button>
                                     @endif
                                 </div>
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted py-5')>
                                @if($search)
                                    <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No candidates found matching "{{ $search }}".</div>
                                @elseif($statusFilter || $departmentFilter || $positionFilter)
                                    <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No candidates found matching the selected filters.</div>
                                @else
                                    <i @class('bi bi-person-x d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No candidates found.</div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $candidates->links() }}
        </div>
    @endif

    {{-- EDIT CANDIDATE MODAL --}}
    @include('livewire.user.applicants.includes.candidate-edit-modal')

    {{-- VIEW CANDIDATE MODAL --}}
    @include('livewire.user.applicants.includes.candidate-view-modal')

    {{-- RESCHEDULE CANDIDATE MODAL --}}
    @include('livewire.user.applicants.includes.candidate-reschedule-modal')

    {{-- SEND SCHEDULING LINK MODAL --}}
    @include('livewire.user.applicants.includes.candidate-link-modal')

</div>
