@section('page-title', 'Applications')
@section('page-subtitle', 'Manage job applications')
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
    {{-- MAIN TABLE --}}
    @if($applications)
        <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>All Applications</h3>
            <p @class('text-secondary mb-0')>
                Overview of job applications
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Name</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Phone</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>Department</th>
                        <th @class('text-secondary')>Qualification Status</th>
                        <th @class('text-secondary')>Filtered Resumes</th>
                        <th @class('text-secondary')>Actions</th>
                        <th @class('text-secondary')>Schedule Interview</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                        <tr wire:key="{{ $app->id }}">
                            <td>{{ $app->first_name }} {{ $app->middle_name }} {{ $app->last_name }} {{ $app->suffix_name }}</td>
                            <td>{{ $app->email }}</td>
                            <td>{{ $app->phone }}</td>
                            <td>{{ $app->applied_position }}</td>
                            <td>{{ $app->department }}</td>
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
                                <button
                                    type="button"
                                    @class('btn btn-info btn-sm')
                                    wire:click="viewFilteredResume({{ $app->id }})"
                                    title="View Filtered Resume"
                                >
                                    <i @class('bi bi-eye-fill')></i> View
                                </button>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    @class('btn btn-warning btn-sm')
                                    wire:click="openEditFilteredResume({{ $app->id }})"
                                    title="Edit Filtered Resume"
                                >
                                    <i @class('bi bi-pencil-square')></i>
                                </button>
                            </td>
                            <td>
                                <button
                                    type="button"
                                    @class('btn btn-success btn-sm')
                                    wire:click="openScheduleModal({{ $app->id }})"
                                    title="Schedule Interview"
                                >
                                    <i @class('bi bi-calendar-check')></i> Schedule
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" @class('text-center text-muted py-5')>
                                <i @class('bi bi-inbox d-block mx-auto fs-1')></i>
                                <div class="mt-3">No applications found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $applications->links() }}
        </div>
    @elseif($showDrafts)
            <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
            <h3>Drafted Applications</h3>
            <p @class('text-secondary mb-0')>
                Only drafted applications
            </p>
        </div>
        <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Name</th>
                        <th @class('text-secondary')>Email</th>
                        <th @class('text-secondary')>Phone</th>
                        <th @class('text-secondary')>Position</th>
                        <th @class('text-secondary')>Department</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drafts as $draft)
                        @if($draft->status === 'Drafted')
                        <tr>
                            <td>{{ $draft->first_name }} {{ $draft->middle_name }} {{ $draft->last_name }} {{ $draft->suffix_name }}</td>
                            <td>{{ $draft->email }}</td>
                            <td>{{ $draft->phone }}</td>
                            <td>{{ $draft->applied_position }}</td>
                            <td>{{ $draft->department }}</td>
                            <td><span @class('badge bg-danger')>{{ $draft->status }}</span></td>
                            <td>
                                <button
                                    @class('btn btn-primary btn-sm')
                                    wire:click="restore({{ $draft->id }})"
                                    title="Restore"
                                >
                                    <i @class('bi bi-bootstrap-reboot')></i>
                                </button>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted')>
                                No drafts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $drafts->links() }}
        </div>
    @endif

    {{-- View Filtered Resume Modal --}}
    @if($showFilteredResumeModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtered Resume - {{ $applicantName }}</h5>
                    <button type="button" class="btn-close" wire:click="closeFilteredResumeModal"></button>
                </div>
                <div class="modal-body">
                    @if($filteredResume)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Rating Score:</strong> {{ $filteredResume['rating_score'] ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Qualification Status:</strong> 
                                @if($filteredResume['qualification_status'] ?? null)
                                    <span class="badge bg-{{ ($filteredResume['qualification_status'] ?? null) == 'Qualified' ? 'success' : 'warning' }}">
                                        {{ $filteredResume['qualification_status'] }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong>Skills:</strong>
                            @if(!empty($filteredResume['skills']) && is_array($filteredResume['skills']))
                                <ul class="mt-2">
                                    @foreach($filteredResume['skills'] as $skill)
                                        <li>{{ $skill }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No skills data available</p>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong>Experience:</strong>
                            @if(!empty($filteredResume['experience']) && is_array($filteredResume['experience']))
                                <div class="mt-2">
                                    @foreach($filteredResume['experience'] as $exp)
                                        <div class="mb-2 p-2 border rounded">
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
                            <strong>Education:</strong>
                            @if(!empty($filteredResume['education']) && is_array($filteredResume['education']))
                                <div class="mt-2">
                                    @foreach($filteredResume['education'] as $edu)
                                        <div class="mb-2 p-2 border rounded">
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
                            No filtered resume data found for this applicant.
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

    {{-- Edit Filtered Resume Modal --}}
    @if($showEditFilteredResumeModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Filtered Resume - {{ $applicantName }}</h5>
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
                                    <label class="form-label">Rating Score</label>
                                    <input type="number" step="0.01" class="form-control" wire:model.defer="edit_rating_score">
                                    @error('edit_rating_score') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Qualification Status</label>
                                    <input type="text" class="form-control" wire:model.defer="edit_qualification_status">
                                    @error('edit_qualification_status') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Skills</label>
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
                                <label class="form-label">Experience</label>
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
                                <label class="form-label">Education</label>
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
                        <span wire:loading.remove>Save</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Schedule Interview Modal --}}
    @if($showScheduleModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Interview - {{ $applicantData['name'] ?? '' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeScheduleModal"></button>
                </div>
                <div class="modal-body">
                    @if(!empty($applicantData))
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
                            <label class="form-label">Interview Date</label>
                            <input type="date" class="form-control" wire:model="interview_date">
                            @error('interview_date') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Interview Time</label>
                            <input type="time" class="form-control" wire:model="interview_time">
                            @error('interview_time') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill"></i>
                        <strong>Note:</strong> When you schedule this interview, the application will be converted to a candidate and moved to the candidates table. The original application record will be deleted.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeScheduleModal">Cancel</button>
                    <button type="button" class="btn btn-success" wire:click="scheduleInterview" wire:loading.attr="disabled">
                        <span wire:loading.remove>Schedule Interview</span>
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
