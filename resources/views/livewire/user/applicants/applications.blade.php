@section('page-title', 'Applications')
@section('page-subtitle', 'AI Review & Draft Hub')
@section('breadcrumbs', 'Applications')




<div @class('pt-2')>


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
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: {{ $qualificationFilter ?: 'All' }}
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
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3 d-flex gap-2')>
            <button
                @class('btn btn-success')
                wire:click="exportData"
            >
                <i @class('bi bi-download me-2')></i>Export
            </button>

            @if(!$showDrafts)
                <button
                    @class('btn btn-danger')
                    wire:click="openDrafts"
                >
                    Open Drafts
                </button>
            @else
                <button
                    @class('btn btn-secondary') {{-- Corrected from btn-default which might not exist in Bootstrap --}}
                    wire:click="closeDrafts"
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
            @if($showDrafts)
                <h3><i class="bi bi-file-earmark-person me-2"></i>Draft Applications</h3>
                <p @class('text-secondary mb-0')>Only drafted applications</p>
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
                        <th @class('text-secondary')>AI Rating</th>
                        <th @class('text-secondary')>Qualification Status</th>
                        <th @class('text-secondary')>Resume</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr wire:key="{{ $app->id }}">
                            <td>
                                <strong>{{ ucwords($app->first_name) }} {{ ucwords($app->last_name) }}</strong>
                                @if($app->status === 'drafted')
                                    <span class="badge bg-secondary ms-1">Draft</span>
                                @endif
                            </td>
                            <td>{{ $app->email }}</td>
                            <td>
                                <div class="fw-medium">{{ $app->applied_position }}</div>
                            </td>
                            <td>
                                @if($app->rating_score !== null)
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-{{ $app->rating_badge_color }} mb-1" style="width: fit-content;">
                                            Score: {{ number_format($app->rating_score, 1) }}
                                        </span>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $app->rating_description }}</small>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Pending Review</span>
                                @endif
                            </td>
                            <td>
                                @if($app->qualification_status)
                                    @php
                                        $statusColor = 'secondary';
                                        if (in_array($app->qualification_status, ['Exceptional', 'Highly Qualified'])) {
                                            $statusColor = 'success';
                                        } elseif ($app->qualification_status == 'Qualified') {
                                            $statusColor = 'warning'; // Matches 'Qualified' in legend (70-79) which is yellow/warning in badge logic usually? Wait, legend says 70-79 is yellow.
                                            // Actually let's map strictly to legend colors:
                                            // 90-100 (Exceptional): Success
                                            // 80-89 (Highly): Success
                                            // 70-79 (Qualified): Warning
                                            // 60-69 (Moderately): Warning
                                            // 50-59 (Marginally): Danger
                                            // 0-49 (Not): Danger
                                        } elseif (in_array($app->qualification_status, ['Moderately Qualified', 'Qualified'])) {
                                            $statusColor = 'warning'; 
                                        } else {
                                            $statusColor = 'danger';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ $app->qualification_status }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
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
                                     @if($app->status === 'drafted')
                                         <button
                                             type="button"
                                             @class('btn btn-sm btn-outline-warning')
                                             wire:click="restore({{ $app->id }})"
                                             title="Restore to Active"
                                         >
                                             <i @class('bi bi-bootstrap-reboot')></i> 
                                         </button>

                                         {{-- Delete button (Super Admin only) --}}
                                         @if(strcasecmp(session('user.position'), 'Super Admin') === 0)
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
                                             @class('btn btn-sm btn-outline-danger')
                                             wire:click="draft({{ $app->id }})"
                                             title="Move to Drafts"
                                         >
                                             <i @class('bi bi-journal-text')></i> 
                                         </button>
                                     @endif

                                     {{-- Draft Tool - Pivot to new role --}}
                                     @if($app->status !== 'drafted' && ($app->qualification_status == 'Not Qualified' || ($app->rating_score && $app->rating_score < 60)))
                                         <button
                                             type="button"
                                             @class('btn btn-sm btn-outline-secondary')
                                             wire:click="openDraftModal({{ $app->id }})"
                                             title="Pivot to Alternative Role"
                                         >
                                             <i @class('bi bi-arrow-repeat')></i>
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
                                        @if($showDrafts)
                                            No draft applications found.
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
    @if($showFilteredResumeModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-robot me-2"></i>AI Analysis - {{ $applicantName }}</h5>
                    <button type="button" class="btn-close" wire:click="closeFilteredResumeModal"></button>
                </div>
                <div class="modal-body">
                    @if($filteredResume)
                        {{-- Rating Score Card --}}
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body text-center py-4">
                                <div class="display-4 fw-bold text-{{ $filteredResume['rating_badge_color'] ?? 'secondary' }}">
                                    {{ $filteredResume['rating_score'] ?? 'N/A' }}
                                </div>
                                <div class="text-muted">AI Rating Score</div>
                                @if(isset($filteredResume['rating_description']))
                                    <div class="mt-2">
                                        <span class="badge bg-{{ $filteredResume['rating_badge_color'] ?? 'secondary' }} fs-6">
                                            {{ $filteredResume['rating_description'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Qualification Status:</strong> 
                                @if($filteredResume['qualification_status'] ?? null)
                                    <span class="badge bg-{{ ($filteredResume['qualification_status'] ?? null) == 'Qualified' ? 'success' : 'danger' }}">
                                        {{ $filteredResume['qualification_status'] }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong><i class="bi bi-tools me-2"></i>Skills:</strong>
                            @if(!empty($filteredResume['skills']) && is_array($filteredResume['skills']))
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    @foreach($filteredResume['skills'] as $skill)
                                        <span class="badge bg-light text-dark border">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No skills data available</p>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong><i class="bi bi-briefcase me-2"></i>Experience:</strong>
                            @if(!empty($filteredResume['experience']) && is_array($filteredResume['experience']))
                                <div class="mt-2">
                                    @foreach($filteredResume['experience'] as $exp)
                                        <div class="mb-2 p-2 border rounded bg-light">
                                            @if(is_array($exp))
                                                <strong>{{ $exp['title'] ?? 'N/A' }}</strong> at {{ $exp['company'] ?? 'N/A' }}
                                                <br><small class="text-muted">{{ $exp['period'] ?? 'N/A' }}</small>
                                                @if(isset($exp['description']))
                                                    <p class="mb-0 mt-1">{{ $exp['description'] }}</p>
                                                @endif
                                            @else
                                                {{ $exp }}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No experience data available</p>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong><i class="bi bi-mortarboard me-2"></i>Education:</strong>
                            @if(!empty($filteredResume['education']) && is_array($filteredResume['education']))
                                <div class="mt-2">
                                    @foreach($filteredResume['education'] as $edu)
                                        <div class="mb-2 p-2 border rounded bg-light">
                                            @if(is_array($edu))
                                                <strong>{{ $edu['degree'] ?? 'N/A' }}</strong> in {{ $edu['field'] ?? 'N/A' }}
                                                <br><small class="text-muted">{{ $edu['institution'] ?? 'N/A' }} ({{ $edu['year'] ?? 'N/A' }})</small>
                                            @else
                                                {{ $edu }}
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No education data available</p>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            No AI analysis data found for this applicant.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeFilteredResumeModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Edit Filtered Resume Modal (Manual Override) --}}
    @if($showEditFilteredResumeModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Manual Override - {{ $applicantName }}</h5>
                    <button type="button" class="btn-close" wire:click="closeEditFilteredResumeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            @if($resumeUrl)
                                @php
                                    $ext = strtolower(pathinfo($resumeUrl, PATHINFO_EXTENSION));
                                @endphp
                                @if($ext === 'pdf')
                                    <iframe src="{{ $resumeUrl }}" style="width: 100%; height: 500px;" class="border rounded"></iframe>
                                @elseif(in_array($ext, ['png','jpg','jpeg','gif','webp']))
                                    <img src="{{ $resumeUrl }}" alt="Resume" class="img-fluid border rounded" />
                                @else
                                    <a class="btn btn-outline-primary" href="{{ $resumeUrl }}" target="_blank" rel="noopener">
                                        Open Resume
                                    </a>
                                @endif
                            @else
                                <div class="alert alert-warning mb-0">
                                    No resume file found.
                                </div>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Rating Score (0-100)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" wire:model.defer="edit_rating_score" placeholder="Enter score">
                                    @error('edit_rating_score') <div class="text-danger small">{{ $message }}</div> @enderror
                                    <small class="text-muted">Score will auto-determine qualification status</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Qualification Status</label>
                                    <select class="form-select" wire:model.defer="edit_qualification_status">
                                        <option value="">Auto (based on score)</option>
                                        <option value="Qualified">Qualified</option>
                                        <option value="Not Qualified">Not Qualified</option>
                                    </select>
                                    @error('edit_qualification_status') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Skills</label>
                                <div class="scrollable">
                                    @foreach($edit_skills as $i => $skill)
                                        <div class="d-flex gap-2 mb-2">
                                            <input type="text" class="form-control" wire:model.defer="edit_skills.{{ $i }}" placeholder="e.g. Communication">
                                            <button type="button" class="btn btn-outline-danger" wire:click.prevent="removeSkill({{ $i }})">Remove</button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" wire:click.prevent="addSkill">Add Skill</button>
                                @error('edit_skills') <div class="text-danger small">{{ $message }}</div> @enderror
                                @error('edit_skills.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Experience</label>
                                <div class="scrollable">
                                    @foreach($edit_experience as $i => $exp)
                                        <div class="d-flex gap-2 mb-2">
                                            <input type="text" class="form-control" wire:model.defer="edit_experience.{{ $i }}" placeholder="e.g. Cashier - Company A (2022)">
                                            <button type="button" class="btn btn-outline-danger" wire:click.prevent="removeExperience({{ $i }})">Remove</button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" wire:click.prevent="addExperience">Add Experience</button>
                                @error('edit_experience') <div class="text-danger small">{{ $message }}</div> @enderror
                                @error('edit_experience.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Education</label>
                                <div class="scrollable">
                                    @foreach($edit_education as $i => $edu)
                                        <div class="d-flex gap-2 mb-2">
                                            <input type="text" class="form-control" wire:model.defer="edit_education.{{ $i }}" placeholder="e.g. BSIT - University X (2021)">
                                            <button type="button" class="btn btn-outline-danger" wire:click.prevent="removeEducation({{ $i }})">Remove</button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" wire:click.prevent="addEducation">Add Education</button>
                                @error('edit_education') <div class="text-danger small">{{ $message }}</div> @enderror
                                @error('edit_education.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditFilteredResumeModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="updateFilteredResume" wire:loading.attr="disabled">
                        <span wire:loading.remove>Save Changes</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Draft Tool Modal - Pivot to Alternative Role --}}
    @if($showDraftModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i>Pivot to Alternative Role</h5>
                    <button type="button" class="btn-close" wire:click="closeDraftModal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>{{ $draftApplicantName }}</strong> may be a better fit for a different role. 
                        Use this tool to suggest an alternative position.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Position</label>
                        <input type="text" class="form-control" value="{{ $currentPosition }}" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Suggested Alternative Position</label>
                        <select class="form-select" wire:model="suggestedPosition">
                            <option value="">Select a position...</option>
                            @foreach($availablePositions as $position)
                                <option value="{{ $position }}">{{ $position }}</option>
                            @endforeach
                        </select>
                        @error('suggestedPosition') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Pivot (Optional)</label>
                        <textarea class="form-control" wire:model="draftReason" rows="3" placeholder="Why is this applicant better suited for the alternative role?"></textarea>
                        @error('draftReason') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDraftModal">Cancel</button>
                    <button type="button" class="btn btn-primary" wire:click="pivotToNewRole" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-check2 me-1"></i>Confirm Pivot</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Schedule Interview Modal --}}
    @if($showScheduleModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-calendar-check me-2"></i>Promote to Candidate - {{ $applicantData['name'] ?? '' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeScheduleModal"></button>
                </div>
                <div class="modal-body">
                    @if(!empty($applicantData))
                        {{-- Rating Summary --}}
                        @if(isset($applicantData['rating_score']))
                        <div class="card mb-4 border-0 bg-light">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="display-6 fw-bold text-{{ $applicantData['rating_badge_color'] ?? 'secondary' }}">
                                    {{ number_format($applicantData['rating_score'], 1) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">AI Rating Score</div>
                                    <small class="text-muted">{{ $applicantData['rating_description'] ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Email:</strong> {{ $applicantData['email'] }}
                            </div>
                            <div class="col-md-6">
                                <strong>Phone:</strong> {{ $applicantData['phone'] }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Position:</strong> {{ $applicantData['position'] }}
                            </div>
                            <div class="col-md-6">
                                <strong>Department:</strong> {{ $applicantData['department'] }}
                            </div>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Interview Location / Facility <span class="text-danger">*</span></label>
                        <select class="form-select @error('selectedFacility') is-invalid @enderror" 
                                wire:model.live="selectedFacility"
                                {{ count($approvedFacilities) == 0 ? 'disabled' : '' }}>
                            <option value="">
                                {{ count($approvedFacilities) == 0 ? 'No Approved Facility' : '-- Select Approved Facility --' }}
                            </option>
                            @foreach($approvedFacilities as $facility)
                                <option value="{{ $facility['id'] }}">{{ $facility['details'] }}</option>
                            @endforeach
                        </select>
                        @error('selectedFacility') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Select an approved facility reservation to auto-fill Scheduled Date & Time.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Interview Date</label>
                            <input type="date" class="form-control" wire:model="interview_date">
                            @error('interview_date') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Interview Time</label>
                            <input type="time" class="form-control" wire:model="interview_time">
                            @error('interview_time') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="bi bi-arrow-right-circle-fill me-2"></i>
                        <strong>Note:</strong> This will promote the application to <strong>Candidate</strong> status and schedule the interview. 
                        The candidate will receive a self-scheduling link via email to confirm or reschedule.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeScheduleModal">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="scheduleInterview" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-check2 me-1"></i>Promote & Schedule</span>
                        <span wire:loading>Scheduling...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
    .scrollable {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

 </div>
