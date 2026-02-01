<div class="pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="w-25">
            <x-text-input type="search" wire:model.live.debounce.500ms="search" placeholder="Search by name or position..." />
        </div>
        
        <div>
            <button
                class="btn btn-success"
                wire:click="export"
                wire:target="employees"
            >
                Export to Excel
            </button>
        </div>
    </div>

    <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
        <div>
            <h3>All Employees</h3>
            <p @class('text-secondary mb-0')>
                Overview of pending, in progress, completed requirements of employees
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
        <table @class('table')>
            <thead>
                <tr @class('bg-dark')>
                    <th @class('text-secondary fw-normal') scope="col">Name</th>
                    <th @class('text-secondary fw-normal') scope="col">Position</th>
                    <th @class('text-secondary fw-normal') scope="col">Department</th>
                    <th @class('text-secondary fw-normal') scope="col">HR Documents</th>
                    <th @class('text-secondary fw-normal') scope="col">Employement Status</th>
                    <th @class('text-secondary fw-normal') scope="col">Date Hired</th>
                    <th @class('text-secondary fw-normal') scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $index => $employee)
                    <tr>
                        <td class="text-nowrap"><strong>{{ $employee['first_name'] . ' ' . $employee['last_name'] }}</strong></td>
                        <td class="text-truncate">{{ $employee['position'] ?? '—' }}</td>
                        <td class="text-capitalize">{{ $employee['department']['name'] ?? 'N/A' }}</td>
                        <td>
                            @if($employee['has_document_checklist'] ?? false)
                                @if(($employee['document_status'] ?? '') === 'Complete')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>Complete
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="bi bi-clock-fill me-1"></i>In Progress
                                    </span>
                                @endif
                                <br>
                                <small class="text-muted">{{ number_format($employee['document_completion'] ?? 0, 2) }}% Complete</small>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock-fill me-1"></i>Pending
                                </span>
                            @endif
                        </td>
                        <td class="text-capitalize">{{ $employee['employement_status'] ?? '---' }}</td>
                        <td class="text-capitalize">{{ $employee['date_hired'] ? \Carbon\Carbon::parse($employee['date_hired'])->format('M d, Y') : '---' }}</td>
                        <td>
                            <button
                                class="btn btn-primary btn-sm"
                                wire:click="viewEmployee({{ $index }})"
                                title="View Employee Details"
                            >
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" @class('text-center text-muted')>
                            Not integrated
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pb-4">
            {{ $employees->links() }}
        </div>
    </div>

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
                                        <strong>{{ ($selectedEmployee['first_name'] ?? '') . ' ' . ($selectedEmployee['last_name'] ?? '') }}</strong>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Email</small>
                                        <strong>{{ $selectedEmployee['email'] ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="mb-0">
                                        <small class="text-muted d-block">Contact Number</small>
                                        <strong>{{ $selectedEmployee['phone'] ?? $selectedEmployee['contact_number'] ?? 'N/A' }}</strong>
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
                                        <strong>{{ $selectedEmployee['department']['name'] ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Employment Status</small>
                                        <span class="badge bg-{{ ($selectedEmployee['employement_status'] ?? '') == 'Active' ? 'success' : 'secondary' }}">
                                            {{ $selectedEmployee['employement_status'] ?? 'N/A' }}
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

</div>
