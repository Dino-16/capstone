@section('page-title', 'Applications')
@section('page-subtitle', 'AI Review & Shortlist Hub')
@section('breadcrumbs', 'Applications')




<div @class('pt-2')>

    {{-- PASSWORD GATE --}}
    @include('components.password-gate')

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-search-input
                    wire:model.live="search" 
                    placeholder="Search..."
                />
            </div>

            {{-- QUALIFICATION STATUS FILTER --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="qualificationFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-bar-chart-line-fill me-2')></i>
                    Rating: {{ $qualificationFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="qualificationFilterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('qualificationFilter', '')">
                            All Status
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('qualificationFilter', 'Exceptional')">
                            <i class="bi bi-patch-check-fill text-success me-2"></i>Exceptional (90-100)
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('qualificationFilter', 'Highly Qualified')">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>Highly Qualified (80-89)
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('qualificationFilter', 'Qualified')">
                            <i class="bi bi-check-circle-fill text-warning me-2"></i>Qualified (70-79)
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('qualificationFilter', 'Moderately Qualified')">
                            <i class="bi bi-info-circle-fill text-warning me-2"></i>Moderately Qualified (60-69)
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('qualificationFilter', 'Marginally Qualified')">
                            <i class="bi bi-exclamation-circle-fill text-danger me-2"></i>Marginally Qualified (50-59)
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('qualificationFilter', 'Not Qualified')">
                            <i class="bi bi-x-circle-fill text-danger me-2"></i>Not Qualified (0-49)
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('qualificationFilter', 'Pending Review')">
                            <i class="bi bi-clock-fill text-secondary me-2"></i>Pending Review
                        </a>
                    </li>
                </ul>
            </div>
            
            {{-- POSITION FILTER --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="positionFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-briefcase-fill me-2')></i>
                    Position: {{ $positionFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="positionFilterDropdown" style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('positionFilter', '')">
                            All Positions
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($positions as $pos)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('positionFilter', '{{ $pos }}')">
                                {{ $pos }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- DEPARTMENT FILTER --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="departmentFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-building-fill me-2')></i>
                    Department: {{ $departmentFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="departmentFilterDropdown" style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('departmentFilter', '')">
                            All Departments
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach($departments as $dept)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('departmentFilter', '{{ $dept }}')">
                                {{ $dept }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3 d-flex gap-2')>
            <button
                @class('btn btn-success')
                wire:click="exportData"
            >
                <i @class('bi bi-download me-2')></i>Export
            </button>

            @if(!$showShortlisted)
                <button
                    @class('btn btn-warning')
                    wire:click="openShortlisted"
                >
                    <i class="bi bi-star-fill me-2"></i>View Shortlisted
                </button>
            @else
                <button
                    @class('btn btn-secondary') {{-- Corrected from btn-default which might not exist in Bootstrap --}}
                    wire:click="closeShortlisted"
                >
                    <i class="bi bi-arrow-left-circle-fill me-1"></i>Back to All
                </button>
            @endif
        </div>
    </div>

    {{-- AI RATING LEGEND --}}
    <div @class('card mb-4 border-0 shadow-sm')>
        <div @class('card-body py-3')>
            <div @class('d-flex align-items-center gap-3 flex-wrap')>
                <span @class('fw-semibold text-muted')><i class="bi bi-robot me-2"></i>AI Rating Scale:</span>
                <span @class('badge bg-success')>90-100: Exceptional</span>
                <span @class('badge bg-success')>80-89: Highly Qualified</span>
                <span @class('badge bg-warning text-dark')>70-79: Qualified</span>
                <span @class('badge bg-warning text-dark')>60-69: Moderately Qualified</span>
                <span @class('badge bg-danger')>50-59: Marginally Qualified</span>
                <span @class('badge bg-danger')>0-49: Not Qualified</span>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    @if($applications)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
                    @if($showShortlisted)
                <h3><i class="bi bi-star-fill me-2 text-warning"></i>Shortlisted Applications</h3>
                <p @class('text-secondary mb-0')>Applications marked for further consideration</p>
            @else
                <h3><i class="bi bi-file-earmark-person me-2"></i>All Applications</h3>
                <p @class('text-secondary mb-0')>AI-reviewed applications with rating scores and qualification status</p>
            @endif
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table table-hover')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Name</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>Department</th>
                        <th @class('text-secondary')>AI Rating</th>
                        <th @class('text-secondary')>Resume</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr wire:key="{{ $app->id }}">
                            <td>
                                <strong>{{ ucwords($app->first_name) }} {{ ucwords($app->last_name) }}</strong>
                                @if($app->status === 'shortlisted')
                                    <span class="badge bg-warning text-dark ms-1"><i class="bi bi-star-fill me-1"></i>Shortlisted</span>
                                @endif
                            </td>
                            <td>{{ $app->email }}</td>
                            <td>
                                <div class="fw-medium">{{ $app->applied_position }}</div>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $app->department }}</div>
                            </td>
                            <td>
                                @if($app->rating_score !== null)
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-{{ $app->rating_badge_color }} mb-1" style="width: fit-content;">
                                            Score: {{ number_format($app->rating_score, 1) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Pending Review</span>
                                @endif
                            </td>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button
                                        type="button"
                                        @class('btn btn-sm btn-outline-primary')
                                        wire:click="viewFilteredResume({{ $app->id }})"
                                        title="View AI Analysis"
                                    >
                                        <i @class('bi bi-eye')></i>
                                    </button>
                                    <button
                                        type="button"
                                        @class('btn btn-sm btn-outline-warning')
                                        wire:click="openEditFilteredResume({{ $app->id }})"
                                        title="Edit Resume Data"
                                    >
                                        <i @class('bi bi-pencil')></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                     {{-- Status Toggle --}}
                                     @if($app->status === 'shortlisted')
                                         <button
                                             type="button"
                                             @class('btn btn-sm btn-outline-secondary')
                                             wire:click="restore({{ $app->id }})"
                                             title="Restore to Active"
                                         >
                                             <i @class('bi bi-bootstrap-reboot')></i> 
                                         </button>

                                         {{-- Pivot Tool - Available for shortlisted candidates --}}
                                         <button
                                             type="button"
                                             @class('btn btn-sm btn-outline-info')
                                             wire:click="openShortlistModal({{ $app->id }})"
                                             title="Pivot to Alternative Role"
                                         >
                                             <i @class('bi bi-arrow-repeat')></i>
                                         </button>

                                         {{-- Delete button (HR Manager and Super Admin) --}}
                                         @if(in_array(session('user.position'), ['Super Admin', 'HR Manager']))
                                             <button
                                                 type="button"
                                                 @class('btn btn-sm btn-outline-danger')
                                                 wire:click="delete({{ $app->id }})"
                                                 wire:confirm="Are you sure you want to permanently delete this application?"
                                                 wire:loading.attr="disabled"
                                                 title="Delete Application"
                                             >
                                                 <i @class('bi bi-trash')></i> 
                                             </button>
                                         @endif
                                     @else
                                         <button
                                             type="button"
                                             @class('btn btn-sm btn-outline-warning')
                                             wire:click="shortlist({{ $app->id }})"
                                             title="Add to Shortlist"
                                         >
                                             <i @class('bi bi-star')></i> 
                                         </button>
                                     @endif
                                     {{-- Schedule Interview --}}
                                     @if($app->status !== 'drafted')
                                         <button
                                             type="button"
                                             @class('btn btn-sm btn-success')
                                             wire:click="openScheduleModal({{ $app->id }})"
                                             title="Promote to Candidate & Schedule"
                                         >
                                             <i @class('bi bi-calendar-check')></i>
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
                                    <div class="mt-3">No applications found matching "{{ $search }}".</div>
                                @elseif($qualificationFilter)
                                    <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No applications found with qualification "{{ $qualificationFilter }}".</div>
                                @else
                                    <i @class('bi bi-inbox d-block mx-auto fs-1')></i>
                                    <div class="mt-3">
                                        @if($showShortlisted)
                                            No shortlisted applications found.
                                        @else
                                            No applications found.
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $applications->links() }}
        </div>
    @endif

    {{-- View Filtered Resume Modal (AI Analysis) --}}
    @include('livewire.user.applicants.includes.application-ai-analysis-modal')

    {{-- Edit Filtered Resume Modal (Manual Override) --}}
    @include('livewire.user.applicants.includes.application-manual-override-modal')

    {{-- Shortlist Tool Modal - Pivot to Alternative Role --}}
    @include('livewire.user.applicants.includes.application-pivot-modal')

    {{-- Schedule Interview Modal --}}
    @include('livewire.user.applicants.includes.application-promote-modal')

<style>
    .scrollable {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

 </div>
