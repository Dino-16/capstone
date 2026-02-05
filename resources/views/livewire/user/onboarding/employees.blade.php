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
        
        <div>
            <button
                class="btn btn-success"
                wire:click="export"
                wire:target="employees"
            >
                Export
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
                                $status = str_replace('_', ' ', $employee['employment_status'] ?? '---');
                                $statusClass = match(strtolower($employee['employment_status'] ?? '')) {
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

</div>
