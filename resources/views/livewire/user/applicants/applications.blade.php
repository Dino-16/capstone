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
                <x-text-input
                    type="search"
                    wire:model.live="search" 
                    placeholder="Search..."
                />
            </div>
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
            <h3><i class="bi bi-file-earmark-person me-2"></i>All Applications</h3>
            <p @class('text-secondary mb-0')>
                AI-reviewed applications with rating scores and qualification status
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table table-hover')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Name</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>AI Rating</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Resume</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr wire:key="{{ $app->id }}">
                            <td>
                                <div class="fw-semibold">{{ $app->first_name }} {{ $app->last_name }}</div>
                                <small class="text-muted">{{ $app->phone }}</small>
                            </td>
                            <td>{{ $app->email }}</td>
                            <td>
                                <div class="fw-medium">{{ $app->applied_position }}</div>
                                <small class="text-muted">{{ $app->department }}</small>
                            </td>
                            <td>
                                @if($app->rating_score)
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
                                    <span class="badge bg-{{ $app->qualification_status == 'Qualified' ? 'success' : ($app->qualification_status == 'Not Qualified' ? 'danger' : 'warning') }}">
                                        {{ $app->qualification_status }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button
                                        type="button"
                                        @class('btn btn-outline-info')
                                        wire:click="viewFilteredResume({{ $app->id }})"
                                        title="View AI Analysis"
                                    >
                                        <i @class('bi bi-eye-fill')></i>
                                    </button>
                                    <button
                                        type="button"
                                        @class('btn btn-outline-warning')
                                        wire:click="openEditFilteredResume({{ $app->id }})"
                                        title="Edit Resume Data"
                                    >
                                        <i @class('bi bi-pencil-square')></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    {{-- Draft Tool - Pivot to new role --}}
                                    @if($app->qualification_status == 'Not Qualified' || ($app->rating_score && $app->rating_score < 60))
                                        <button
                                            type="button"
                                            @class('btn btn-outline-secondary')
                                            wire:click="openDraftModal({{ $app->id }})"
                                            title="Pivot to Alternative Role"
                                        >
                                            <i @class('bi bi-arrow-repeat')></i> Pivot
                                        </button>
                                    @endif
                                    {{-- Schedule Interview --}}
                                    <button
                                        type="button"
                                        @class('btn btn-success')
                                        wire:click="openScheduleModal({{ $app->id }})"
                                        title="Promote to Candidate & Schedule"
                                    >
                                        <i @class('bi bi-calendar-check')></i> Schedule
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted py-5')>
                                <i @class('bi bi-inbox d-block mx-auto fs-1')></i>
                                <div class="mt-3">No applications found.</div>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-robot me-2"></i>AI Analysis - {{ $applicantName }}</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeFilteredResumeModal"></button>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i>Pivot to Alternative Role</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeDraftModal"></button>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-calendar-check me-2"></i>Promote to Candidate - {{ $applicantData['name'] ?? '' }}</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeScheduleModal"></button>
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
