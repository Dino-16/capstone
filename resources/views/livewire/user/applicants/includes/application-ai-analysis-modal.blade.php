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
