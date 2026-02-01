@section('page-title', 'Evaluation Records')
@section('page-subtitle', 'Manage performance evaluations')
@section('breadcrumbs', 'Evaluation Records')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- STATUS CARDS --}}
    @include('livewire.user.performance.includes.record-card')

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-text-input
                    type="search"
                    wire:model.live.debounce.3s="search"
                    placeholder="Search..."
                />
            </div>

            {{-- FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="filterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: {{ $statusFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="filterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">
                            All Statuses
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Pending')">
                            Pending
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'In Progress')">
                            In Progress
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Completed')">
                            Completed
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Cancelled')">
                            Cancelled
                        </a>
                    </li>
                </ul>
            </div>

            {{-- SCORE FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="scoreFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-star-fill me-2')></i>
                    Score: {{ $scoreFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="scoreFilterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('scoreFilter', '')">
                            All Scores
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('scoreFilter', 'excellent')">
                            Excellent (90-100)
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('scoreFilter', 'good')">
                            Good (70-89)
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('scoreFilter', 'average')">
                            Average (50-69)
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('scoreFilter', 'poor')">
                            Poor (&lt;50)
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                
                <button
                    @class('btn btn-success')
                    wire:click="exportData"
                >
                    <i @class('bi bi-download me-2')></i>Export to CSV
                </button>

                @if(!$showDrafts)
                    <button
                        @class('btn btn-danger')
                        wire:click="openDraft"
                    >
                        Open Drafts
                    </button>
                @else
                    <button
                        @class('btn btn-danger')
                        wire:click="openDraft"
                        disabled
                    >
                        Open Drafts
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if($showDrafts)
        <div @class('mb-3')>
            <button @class('btn btn-default') wire:click="showAll"><i class="bi bi-arrow-left-circle-fill me-1"></i>Back to All</button>
        </div>
    @endif

    {{-- MAIN TABLE --}}
    @if(!$showDrafts)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>Evaluation Records</h3>
            <p @class('text-secondary mb-0')>
                Overview of performance evaluations
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Employee</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Evaluations</th>
                        <th @class('text-secondary')>Evaluation Date</th>
                        <th @class('text-secondary')>Evaluator</th>
                        <th @class('text-secondary')>Score</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Created</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $evaluation)
                        <tr wire:key="{{ $evaluation->id }}">
                            <td>
                                <strong>{{ $evaluation->employee_name }}</strong>
                            </td>
                            <td>
                                {{ $evaluation->email }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary" title="Total Evaluations">
                                        <i class="bi bi-clipboard-data me-1"></i>{{ $evaluation->employee_evaluation_count ?? 0 }}
                                    </span>
                                    <span class="badge bg-success" title="Completed">
                                        <i class="bi bi-check-circle me-1"></i>{{ $evaluation->employee_completed_count ?? 0 }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                {{ $evaluation->evaluation_date->format('M d, Y') }}
                            </td>
                            <td>
                                {{ $evaluation->evaluator_name }}                            
                            </td>
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <div @class('progress me-2') style="width: 60px; height: 8px;">
                                        <div @class("progress-bar {{ $evaluation->overall_score >= 80 ? 'bg-success' : ($evaluation->overall_score >= 60 ? 'bg-warning' : 'bg-danger') }}") 
                                             style="width: {{ $evaluation->overall_score }}%"></div>
                                    </div>
                                    <div @class('d-flex flex-column ms-2')>
                                        <span @class("badge {{ $evaluation->overall_score >= 80 ? 'bg-success' : ($evaluation->overall_score >= 60 ? 'bg-warning' : 'bg-danger') }}")>
                                            {{ $evaluation->overall_score }}%
                                        </span>
                                        <small @class('text-muted')>{{ $evaluation->overall_score }}/100</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $evaluation->status }}
                            </td>
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <i @class('bi bi-clock me-2 text-muted')></i>
                                    {{ $evaluation->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td @class('gap-3')>
                                <button
                                    @class('btn btn-info btn-sm')
                                    wire:click="viewEvaluation({{ $evaluation->id }})"
                                    title="View Evaluation"
                                >
                                    <i @class('bi bi-eye-fill')></i>
                                </button>

                                <button
                                    @class('btn btn-primary btn-sm')
                                    wire:click="editEvaluation({{ $evaluation->id }})"
                                    title="Edit Evaluation"
                                >
                                    <i @class('bi bi-pencil-fill')></i>
                                </button>

                                <button
                                    @class('btn btn-danger btn-sm')
                                    wire:click="draft({{ $evaluation->id }})"
                                    title="Draft Evaluation"
                                >
                                    <i @class('bi bi-journal-text')></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" @class('text-center text-muted py-5')>
                                <i @class('bi bi-file-text d-block mx-auto fs-1')></i>
                                <p @class('text-muted mb-0 mt-3')>No evaluation records found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- PAGINATION --}}
            <div>
                {{ $evaluations->links() }}
            </div>
        </div>
    @else
        {{-- DRAFT TABLE --}}
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>Draft Evaluations</h3>
            <p @class('text-secondary mb-0')>
                Only draft evaluations
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Employee</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Evaluation Date</th>
                        <th @class('text-secondary')>Evaluator</th>
                        <th @class('text-secondary')>Score</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Performance Areas</th>
                        <th @class('text-secondary')>Created</th>
                        <th @class('text-secondary')>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drafts as $draft)
                        <tr>
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <div @class('avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2')>
                                        <i @class('bi bi-person-fill text-white')></i>
                                    </div>
                                    <div>
                                        <strong>{{ $draft->employee_name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($draft->email)
                                    <a href="mailto:{{ $draft->email }}" @class('text-decoration-none')>
                                        {{ $draft->email }}
                                        <i @class('bi bi-envelope-fill text-primary')></i>
                                    </a>
                                @else
                                    <span @class('text-muted')>No email</span>
                                @endif
                            </td>
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <i @class('bi bi-calendar3 me-2 text-muted')></i>
                                    {{ $draft->evaluation_date->format('M d, Y') }}
                                </div>
                            </td>
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <div @class('avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center me-2')>
                                        <i @class('bi bi-person-badge-fill text-white')></i>
                                    </div>
                                    {{ $draft->evaluator_name }}
                                </div>
                            </td>
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <div @class('progress me-2') style="width: 60px; height: 8px;">
                                        <div @class("progress-bar {{ $draft->overall_score >= 80 ? 'bg-success' : ($draft->overall_score >= 60 ? 'bg-warning' : 'bg-danger') }}") 
                                             style="width: {{ $draft->overall_score }}%"></div>
                                    </div>
                                    <div @class('d-flex flex-column ms-2')>
                                        <span @class("badge {{ $draft->overall_score >= 80 ? 'bg-success' : ($draft->overall_score >= 60 ? 'bg-warning' : 'bg-danger') }}")>
                                            {{ $draft->overall_score }}%
                                        </span>
                                        <small @class('text-muted')>{{ $draft->overall_score }}/100</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $draft->status }}</td>
                            <td>
                                @if($draft->performance_areas)
                                    <div @class('d-flex flex-wrap gap-1')>
                                        @php
                                            $areas = explode(',', $draft->performance_areas);
                                            $areas = array_slice($areas, 0, 3);
                                        @endphp
                                        @foreach($areas as $area)
                                            <span @class('badge bg-light text-dark')>{{ trim($area) }}</span>
                                        @endforeach
                                        @if(count(explode(',', $draft->performance_areas)) > 3)
                                            <span @class('badge bg-secondary')>+{{ count(explode(',', $draft->performance_areas)) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span @class('text-muted')>Not specified</span>
                                @endif
                            </td>
                            <td>
                                <div @class('d-flex align-items-center')>
                                    <i @class('bi bi-clock me-2 text-muted')></i>
                                    {{ $draft->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td>
                                <button
                                    @class('btn btn-info btn-sm')
                                    wire:click="viewEvaluation({{ $draft->id }})"
                                    title="View Evaluation"
                                >
                                    <i @class('bi bi-eye-fill')></i>
                                </button>
                                
                                <button
                                    @class('btn btn-primary btn-sm')
                                    wire:click="restore({{ $draft->id }})"
                                    title="Restore Draft"
                                >
                                    <i @class('bi bi-bootstrap-reboot')></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" @class('text-center text-muted')>
                                <p @class('text-muted mb-0')>No drafts found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $drafts->links() }}
        </div>
    @endif
{{-- Edit Evaluation Modal --}}
    @include('livewire.user.performance.includes.evaluation-edit')
    
    {{-- View Evaluation Modal --}}
    @include('livewire.user.performance.includes.evaluation-view')

</div>
</div>
