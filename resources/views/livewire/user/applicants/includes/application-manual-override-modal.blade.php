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
