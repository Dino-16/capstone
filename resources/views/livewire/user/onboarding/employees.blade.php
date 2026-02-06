@section('page-title', 'Employee Onboarding')
@section('page-subtitle', 'Manage and track employee onboarding progress')
@section('breadcrumbs', 'Employee Onboarding')

<div class="pt-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <div>
                <x-search-input wire:model.live.debounce.500ms="search" placeholder="Search by name or position..." />
            </div>
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: {{ $statusFilter ? str_replace('_', ' ', $statusFilter) : 'All' }}
                </button>

                <ul @class('dropdown-menu')>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', '')">All Status</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'regular')">Regular</a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'new_hire')">New Hire</a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button
                class="btn btn-outline-primary"
                wire:click="openApprovalModal"
            >
                <i class="bi bi-file-earmark-check me-2"></i>Contract Approval
            </button>
            <button
                class="btn btn-success"
                wire:click="export"
                wire:target="employees"
            >
                <i class="bi bi-download me-2"></i>Export
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
                    <th @class('text-secondary fw-normal') scope="col">Employment Status</th>
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
                        <td>
                            @php
                                $status = $employee['employment_status'] ?? '---';
                                $statusClass = match(strtolower($status)) {
                                    'regular' => 'bg-success',
                                    'new_hire' => 'bg-info text-white',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} text-capitalize">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="text-capitalize">{{ $employee['date_hired'] ? \Carbon\Carbon::parse($employee['date_hired'])->format('M d, Y') : '---' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                            @if(session('user.position') === 'HR Manager')
                                <button
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="editEmployee({{ $index }})"
                                    title="Edit Details"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @endif
                            <button
                                class="btn btn-sm btn-outline-primary"
                                wire:click="viewEmployee({{ $index }})"
                                title="View Employee Details"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                            <button
                                class="btn btn-sm btn-outline-warning"
                                wire:click="openRequestContractModal({{ $index }})"
                                title="Request Contract"
                            >
                                <i class="bi bi-file-earmark-plus"></i>
                            </button>
                            @if(session('user.position') === 'Super Admin')
                                <button
                                    class="btn btn-sm btn-danger"
                                    wire:click="deleteEmployee({{ $index }})"
                                    wire:confirm="Are you sure you want to delete this employee?"
                                    title="Delete"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" @class('text-center text-muted py-5')>
                            @if($search)
                                <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                <div class="mt-3">No employees found matching "{{ $search }}".</div>
                            @elseif($statusFilter)
                                <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                <div class="mt-3">No {{ str_replace('_', ' ', $statusFilter) }} employees found.</div>
                            @else
                                <i @class('bi bi-people d-block mx-auto fs-1')></i>
                                <div class="mt-3">No employees found.</div>
                            @endif
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
                                        <strong>{{ $selectedEmployee['department']['name'] ?? 'N/A' }}</strong>
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

    {{-- Edit Employee Modal --}}
    @if($showEditModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Employee</h5>
                    <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateEmployee">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" wire:model="first_name">
                                @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" wire:model="last_name">
                                @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="email">
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" wire:model="phone">
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Position</label>
                                <input type="text" class="form-control" wire:model="position">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Employment Status</label>
                                <select class="form-select" wire:model="employment_status">
                                    <option value="regular">Regular</option>
                                    <option value="new_hire">New Hire</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                 <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="updateEmployee">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Request Contract Modal --}}
    @if($showRequestContractModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i>Request Contract</h5>
                    <button type="button" class="btn-close" wire:click="closeRequestContractModal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Requesting Department <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control border-2" wire:model="requestDepartment" placeholder="Enter department">
                                    @error('requestDepartment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Requestor Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control border-2" wire:model="requestorName" placeholder="Enter your name">
                                    @error('requestorName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Requestor Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control border-2" wire:model="requestorEmail" placeholder="Enter your email">
                                    @error('requestorEmail') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                 </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Contract Type Requested <span class="text-danger">*</span></label>
                                    <select class="form-select border-2" wire:model="requestContractType">
                                        <option value="">Select Contract Type</option>
                                        @foreach($contractTypes as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('requestContractType') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small text-uppercase">Purpose <span class="text-danger">*</span></label>
                                    <textarea 
                                        class="form-control border-2" 
                                        wire:model="requestPurpose" 
                                        rows="5" 
                                        placeholder="Describe the purpose of this contract request..."
                                        style="resize: none;"
                                    ></textarea>
                                    @error('requestPurpose') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-0">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <small>This request will be sent to the Legal Administration system for processing. You will be notified once the contract is ready.</small>
                    </div>
                </div>
                <div class="modal-footer border-top p-4 bg-white">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" wire:click="closeRequestContractModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4 py-2 fw-bold shadow-sm" wire:click="submitContractRequest" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="bi bi-send-fill me-2"></i>Send Request</span>
                        <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span>Submitting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Contract Approval Modal --}}
    @if($showApprovalModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title"><i class="bi bi-file-earmark-lock-fill me-2"></i>Submit Contract for Approval</h5>
                    <button type="button" class="btn-close" wire:click="closeApprovalModal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Submit a drafted contract to the Legal Department for review and final approval.
                    </div>

                    <div class="row g-3">
                        {{-- Contract Info --}}
                        <div class="col-12">
                             <label class="form-label fw-bold small text-uppercase text-secondary">Contract Title <span class="text-danger">*</span></label>
                             <input type="text" class="form-control" wire:model="approvalTitle" placeholder="e.g. Service Agreement 2024">
                             @error('approvalTitle') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Client/Requestor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="approvalClientName">
                            @error('approvalClientName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Client/Requestor Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" wire:model="approvalClientEmail">
                            @error('approvalClientEmail') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Contract Type <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="approvalType">
                                <option value="">Select Type</option>
                                <option value="service_agreement">Service Agreement</option>
                                <option value="employment_contract">Employment Contract</option>
                                <option value="partnership_agreement">Partnership Agreement</option>
                                <option value="vendor_contract">Vendor Contract</option>
                                <option value="non_disclosure_agreement">Non-Disclosure Agreement</option>
                                <option value="lease_agreement">Lease Agreement</option>
                                <option value="other">Other</option>
                            </select>
                            @error('approvalType') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Contract Value (PHP) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" wire:model="approvalValue" placeholder="0.00">
                            @error('approvalValue') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model="approvalStartDate">
                            @error('approvalStartDate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-secondary">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model="approvalEndDate">
                            @error('approvalEndDate') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Description</label>
                            <textarea class="form-control" rows="3" wire:model="approvalDescription" placeholder="Optional notes..."></textarea>
                            @error('approvalDescription') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{-- File Upload --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Attach Contract File <span class="text-danger">*</span></label>
                            <div class="border rounded p-3 bg-white">
                                <input type="file" class="form-control" wire:model="approvalFile" accept=".pdf,.doc,.docx">
                                <small class="text-muted mt-2 d-block">
                                    <i class="bi bi-file-earmark-arrow-up"></i> Accepted Formats: PDF, DOC, DOCX (Max 10MB)
                                </small>
                            </div>
                            @error('approvalFile') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div wire:loading wire:target="approvalFile" class="text-success small mt-2">
                                <span class="spinner-border spinner-border-sm me-2"></span>Uploading file...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary px-4" wire:click="closeApprovalModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold" wire:click="submitContractApproval" wire:loading.attr="disabled" wire:target="submitContractApproval, approvalFile">
                        <span wire:loading.remove wire:target="submitContractApproval"><i class="bi bi-send-fill me-2"></i>Submit for Approval</span>
                        <span wire:loading wire:target="submitContractApproval"><span class="spinner-border spinner-border-sm me-2"></span>Submitting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
</div>

