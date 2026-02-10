{{-- View Employee Modal --}}
@if($showEmployeeModal && $selectedEmployee)
<div @class('modal fade show d-block') tabindex="-1" style="background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-white border-bottom">
                <h5 class="modal-title">
                    <i class="bi bi-person-vcard me-2"></i>Employee Information
                </h5>
                <button type="button" class="btn-close" wire:click="closeModal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Employee Info Cards --}}
                <div class="row g-4">
                    {{-- Personal Details --}}
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Personal Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Full Name</small>
                                    <strong>{{ $selectedEmployee['full_name'] ?? (($selectedEmployee['first_name'] ?? '') . ' ' . ($selectedEmployee['last_name'] ?? '')) }}</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Email</small>
                                    <strong>{{ $selectedEmployee['email'] ?? 'N/A' }}</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Contact Number</small>
                                    <strong>{{ $selectedEmployee['phone'] ?? $selectedEmployee['contact_number'] ?? 'N/A' }}</strong>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted d-block">Civil Status</small>
                                    <strong class="text-capitalize">{{ $selectedEmployee['civil_status'] ?? 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Employment Details --}}
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-briefcase me-2"></i>Employment Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Position</small>
                                    <strong>{{ $selectedEmployee['position'] ?? 'N/A' }}</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Department</small>
                                    <strong>{{ $selectedEmployee['department']['name'] ?? $selectedEmployee['department'] ?? 'N/A' }}</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Employment Status</small>
                                    @php
                                        $modalStatus = str_replace('_', ' ', $selectedEmployee['employment_status'] ?? 'N/A');
                                        $modalStatusClass = match(strtolower($selectedEmployee['employment_status'] ?? '')) {
                                            'regular' => 'bg-success',
                                            'new_hire' => 'bg-info text-white',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $modalStatusClass }} text-capitalize">
                                        {{ $modalStatus }}
                                    </span>
                                </div>
                                <div class="mb-0">
                                    <small class="text-muted d-block">Date Hired</small>
                                    <strong>{{ isset($selectedEmployee['date_hired']) ? \Carbon\Carbon::parse($selectedEmployee['date_hired'])->format('F d, Y') : 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- HR Documents Section --}}
                <div class="card mt-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>HR Documents</h6>
                        @if($selectedEmployee['has_document_checklist'] ?? false)
                            @if(($selectedEmployee['document_status'] ?? '') === 'Complete')
                                <span class="badge bg-success">Complete</span>
                            @else
                                <span class="badge bg-warning text-dark">In Progress</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">No Documents</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($selectedEmployee['has_document_checklist'] ?? false)
                            {{-- Progress Bar --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Completion Progress</small>
                                    <small class="fw-bold">{{ number_format($selectedEmployee['document_completion'] ?? 0, 2) }}%</small>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-{{ ($selectedEmployee['document_completion'] ?? 0) == 100 ? 'success' : 'primary' }}" 
                                         role="progressbar" 
                                         style="width: {{ $selectedEmployee['document_completion'] ?? 0 }}%">
                                    </div>
                                </div>
                            </div>

                            {{-- Documents List --}}
                            @if(!empty($employeeDocuments))
                                <div class="row g-2">
                                    @foreach($employeeDocuments as $docType => $status)
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center justify-content-between p-2 border rounded {{ $status === 'complete' ? 'border-success bg-success bg-opacity-10' : '' }}">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-file-earmark-{{ $status === 'complete' ? 'check text-success' : 'text' }} me-2"></i>
                                                    <span>{{ ucwords(str_replace('_', ' ', $docType)) }}</span>
                                                </div>
                                                @if($status === 'complete')
                                                    <span class="badge bg-success">Submitted</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">Document details not available.</p>
                            @endif
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                <p class="mb-0">No document checklist has been created for this employee.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
            </div>
        </div>
    </div>
</div>
@endif
