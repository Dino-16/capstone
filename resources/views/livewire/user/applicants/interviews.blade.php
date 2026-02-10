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
                    Status: {{ $statusFilter ? ($statusFilter === 'interview_ready' ? 'Ready' : ucfirst($statusFilter)) : 'All' }}
                </button>

                <ul @class('dropdown-menu')>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">All Status</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'interview_ready')">Ready</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'interviewed')">Interviewed</a>
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

    {{-- INTERVIEW STAGES GUIDE --}}
    <div class="row g-3 mb-4">
        @foreach($interviewStages as $stageKey => $stage)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-{{ $stage['color'] }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-circle bg-{{ $stage['color'] }} bg-opacity-10 p-3 me-3">
                                <i class="bi {{ $stage['icon'] }} text-{{ $stage['color'] }} fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $stage['label'] }}</h6>
                                <small class="text-muted text-uppercase">Stage {{ $loop->iteration }}</small>
                            </div>
                        </div>
                        <p class="mb-0 text-muted small">
                            <i class="bi bi-quote me-1"></i>{{ $stage['description'] }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
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
                    <th @class('text-secondary')>Email</th>
                    <th @class('text-secondary')>Department</th>
                    <th @class('text-secondary')>Position</th>
                    <th @class('text-secondary')>Interview Stage</th>
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
                                $currentStage = $candidate->interview_stage ?? 'initial';
                                $stageInfo = $interviewStages[$currentStage] ?? $interviewStages['initial'];
                            @endphp
                            <div class="dropdown">
                                <button 
                                    class="btn btn-sm btn-{{ $stageInfo['color'] }} dropdown-toggle d-flex align-items-center gap-1" 
                                    type="button" 
                                    data-bs-toggle="dropdown"
                                >
                                    <i class="bi {{ $stageInfo['icon'] }}"></i>
                                    {{ $stageInfo['label'] }}
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach($interviewStages as $stageKey => $stage)
                                        <li>
                                            <a 
                                                class="dropdown-item {{ $currentStage === $stageKey ? 'active' : '' }}" 
                                                href="#"
                                                wire:click.prevent="updateInterviewStage({{ $candidate->id }}, '{{ $stageKey }}')"
                                            >
                                                <i class="bi {{ $stage['icon'] }} me-2 text-{{ $stage['color'] }}"></i>
                                                {{ $stage['label'] }}
                                                <br>
                                                <small class="text-muted">{{ $stage['description'] }}</small>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
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
                            <div class="d-flex gap-2">
                                <button
                                    type="button"
                                    @class('btn btn-sm btn-primary')
                                    wire:click="openInterviewModal({{ $candidate->id }})"
                                    title="Start Interview Assessment"
                                >
                                    <i @class('bi bi-play-circle me-1')></i>
                                    {{ $candidate->status === 'interviewed' ? 'Review' : 'Start' }}
                                </button>
                                
                                <button
                                    type="button"
                                    @class('btn btn-sm btn-outline-primary')
                                    wire:click="openMessageModal({{ $candidate->id }})"
                                    title="Message Candidate"
                                >
                                    <i @class('bi bi-envelope')></i>
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
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" @class('text-center text-muted py-5')>
                            @if($search)
                                <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates found matching "{{ $search }}".</div>
                            @elseif($statusFilter || $departmentFilter || $positionFilter)
                                <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                <div class="mt-3">No candidates found matching the selected filters.</div>
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
    @include('livewire.user.applicants.includes.interview-assessment-modal')

    {{-- PASS/FAIL RESULT MODAL --}}
    @include('livewire.user.applicants.includes.interview-result-modal')

    {{-- MESSAGE MODAL --}}
    @include('livewire.user.applicants.includes.interview-message-modal')

    <style>
        .container-max { max-width: 1000px; }
        .cursor-pointer { cursor: pointer; }
        .cursor-pointer:hover { background-color: #f8fafc; }
        
        .SaaS-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background-color: #ffffff;
        }
        .SaaS-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .shadow-xs { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .shadow-primary { box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3), 0 4px 6px -2px rgba(59, 130, 246, 0.1); }
        .shadow-success { box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3), 0 4px 6px -2px rgba(16, 185, 129, 0.1); }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        
        .transition-all { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .transition-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-color: #bfdbfe !important; }
        .scale-110 { transform: scale(1.1); }
        .fw-black { font-weight: 900; }
        .tracking-wider { letter-spacing: 0.05em; }
        .tracking-widest { letter-spacing: 0.1em; }
        
        .form-range::-webkit-slider-thumb { background: #3b82f6; }
        .form-range::-moz-range-thumb { background: #3b82f6; }
        .btn-xl { font-size: 1.1rem; }
    </style>
</div>


