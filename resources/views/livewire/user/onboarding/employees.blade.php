@section('page-title', 'Employee Onboarding')
@section('page-subtitle', 'Manage and track employee onboarding progress')
@section('breadcrumbs', 'Employee Onboarding')

<div class="pt-3">
    {{-- PASSWORD GATE --}}
    @include('components.password-gate')

    {{-- SUCCESS TOAST --}}
    <x-toast />

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
                    Status: {{ $statusFilter ? str_replace('_', ' ', $statusFilter) : 'All' }}
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

            {{-- Department Filter --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-building me-2')></i>
                    Department: {{ $departmentFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('departmentFilter', '')">All Departments</a>
                    </li>
                    @foreach($departments as $dept)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('departmentFilter', '{{ $dept }}')">{{ $dept }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Position Filter --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-person-badge me-2')></i>
                    Position: {{ $positionFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('positionFilter', '')">All Positions</a>
                    </li>
                    @foreach($positions as $pos)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('positionFilter', '{{ $pos }}')">{{ $pos }}</a>
                        </li>
                    @endforeach
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
                        <td class="text-nowrap"><strong>{{ $employee['full_name'] ?? ($employee['first_name'] . ' ' . $employee['last_name']) }}</strong></td>
                        <td class="text-truncate">{{ $employee['position'] ?? '—' }}</td>
                        <td class="text-capitalize">{{ $employee['department']['name'] ?? $employee['department'] ?? 'N/A' }}</td>
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
                                <button
                                    class="btn btn-sm btn-outline-primary"
                                    wire:click="viewEmployee({{ $index }})"
                                    title="View Details"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
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

    {{-- Modals --}}
    @include('livewire.user.onboarding.includes.employee-delete-modal')
    @include('livewire.user.onboarding.includes.employee-view-modal')
    @include('livewire.user.onboarding.includes.employee-edit-modal')
    @include('livewire.user.onboarding.includes.employee-request-contract-modal')
    @include('livewire.user.onboarding.includes.employee-contract-approval-modal')
</div>

