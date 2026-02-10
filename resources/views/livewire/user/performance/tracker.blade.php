@section('page-title', 'Performance Tracker')
@section('page-subtitle', 'Manage performance evaluations')
@section('breadcrumbs', 'Performance Tracker')

<div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                {{-- HEADER ACTIONS --}}
                <div @class('d-flex justify-content-between align-items-center mb-3')>

                    {{-- LEFT SIDE --}}
                    <div @class('d-flex align-items-center gap-2')>
                        
                        {{-- SEARCH BAR --}}
                        <div>
                            <x-search-input
                                wire:model.live="search" 
                                placeholder="Search employees..."
                            />
                        </div>
                        
                        {{-- DEPARTMENT FILTER --}}
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

                        {{-- POSITION FILTER --}}
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

                        {{-- NEXT EVALUATION FILTER --}}
                        <div @class('dropdown')>
                <button
                    type="button"
                    id="nextEvalFilterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: 
                    @if($nextEvaluationFilter === '')
                        All Status
                    @elseif($nextEvaluationFilter === 'current')
                        Due This Month
                    @elseif($nextEvaluationFilter === 'pending')
                        Pending/Overdue
                    @elseif($nextEvaluationFilter === 'upcoming')
                        Upcoming
                    @elseif($nextEvaluationFilter === 'caught_up')
                        All Caught Up
                    @endif
                </button>

                            <ul @class('dropdown-menu') aria-labelledby="nextEvalFilterDropdown">
                                <li>
                                    <a @class('dropdown-item') wire:click="$set('nextEvaluationFilter', '')">
                                        All Status
                                    </a>
                                </li>
                                <li>
                                    <a @class('dropdown-item') wire:click="$set('nextEvaluationFilter', 'current')">
                                        <i class="bi bi-exclamation-circle-fill text-warning me-2"></i>Due This Month
                                    </a>
                                </li>
                                <li>
                                    <a @class('dropdown-item') wire:click="$set('nextEvaluationFilter', 'pending')">
                                        <i class="bi bi-clock-fill text-danger me-2"></i>Pending/Overdue
                                    </a>
                                </li>
                                <li>
                                    <a @class('dropdown-item') wire:click="$set('nextEvaluationFilter', 'upcoming')">
                                        <i class="bi bi-calendar-fill text-info me-2"></i>Upcoming
                                    </a>
                                </li>
                                <li>
                                    <a @class('dropdown-item') wire:click="$set('nextEvaluationFilter', 'caught_up')">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>All Caught Up
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- RIGHT SIDE --}}
                    <div @class('d-flex gap-2')>
                        <button
                            @class('btn btn-success')
                            wire:click="exportData"
                        >
                            <i @class('bi bi-download me-2')></i>Export
                        </button>
                    </div>
                </div>

                <!-- Monthly Performance Evaluation Table -->
                <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
                    <h3>Monthly Evaluation Schedule</h3>
                    <p @class('text-secondary mb-0')>
                        Employee evaluation overview
                        @if($search || $nextEvaluationFilter)
                            <span class="badge bg-primary ms-2">{{ count($this->filteredEmployees) }} results</span>
                        @endif
                    </p>
                </div>
                <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
                    <table @class('table')>
                        <thead>
                            <tr @class('bg-dark')>
                                <th @class('text-secondary')>Employee</th>
                                <th @class('text-secondary')>Position</th>
                                <th @class('text-secondary')>Department</th>
                                <th @class('text-secondary')>Hire Date</th>
                                <th @class('text-secondary')>Evaluation Status</th>
                                <th @class('text-secondary')>Next Evaluation</th>
                                <th @class('text-secondary')>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->filteredEmployees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                {{ substr($employee['name'], 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $employee['name'] }}</div>
                                                <div class="small text-muted">{{ $employee['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee['position'] }}</td>
                                    <td>{{ $employee['department'] }}</td>
                                    <td>{{ $employee['hire_date'] }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-success" title="Completed">
                                                {{ $employee['completed_evaluations'] }} Completed
                                            </span>
                                            <span class="badge bg-danger" title="Pending">
                                                {{ $employee['pending_evaluations'] }} Pending
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $nextEval = collect($employee['monthly_evaluations'])
                                                ->where('status', '!=', 'completed')
                                                ->first();
                                        @endphp
                                        @if($nextEval)
                                            <span class="badge bg-{{ $nextEval['status'] == 'current' ? 'warning' : 'info' }}">
                                                {{ $nextEval['month'] }}
                                            </span>
                                        @else
                                            <span class="text-success small">All Caught Up</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($nextEval)
                                            <div class="d-flex gap-2">
                                                <button 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    wire:click="openScheduleModal({{ $employee['id'] }})"
                                                    title="View Evaluation Schedule"
                                                >
                                                    <i class="bi bi-calendar3"></i>
                                                </button>
                                                <button 
                                                    class="btn btn-sm btn-primary" 
                                                    wire:click="goToEvaluate({{ $employee['id'] }})"
                                                    title="Evaluate Employee"
                                                >
                                                    <i class="bi bi-clipboard-check"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-muted">---</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" @class('text-center text-muted py-5')>
                                        @if($search)
                                            <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                            <div class="mt-3">No employees found matching "{{ $search }}".</div>
                                        @elseif($nextEvaluationFilter)
                                            <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                            <div class="mt-3">
                                                No employees found with status: 
                                                @if($nextEvaluationFilter === 'current') Due This Month
                                                @elseif($nextEvaluationFilter === 'pending') Pending/Overdue
                                                @elseif($nextEvaluationFilter === 'upcoming') Upcoming
                                                @elseif($nextEvaluationFilter === 'caught_up') All Caught Up
                                                @endif
                                            </div>
                                        @else
                                            <i @class('bi bi-people d-block mx-auto fs-1')></i>
                                            <div class="mt-3">No employees found.</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Attendance Tracker -->
                <div class="mt-4">
                    {{-- ATTENDANCE HEADER ACTIONS --}}
                    <div @class('d-flex justify-content-between align-items-center mb-3')>

                        {{-- LEFT SIDE --}}
                        <div @class('d-flex align-items-center gap-2')>
                            
                            {{-- SEARCH BAR --}}
                            <div>
                                <x-text-input
                                    type="search"
                                    wire:model.live="attendanceSearch" 
                                    placeholder="Search attendance..."
                                />
                            </div>

                            {{-- STATUS FILTER --}}
                            <div @class('dropdown')>
                                <button
                                    type="button"
                                    id="attendanceStatusFilterDropdown"
                                    data-bs-toggle="dropdown"
                                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                                >
                                    <i @class('bi bi-funnel-fill me-2')></i>
                                    @if($attendanceStatusFilter === '')
                                        All Status
                                    @elseif($attendanceStatusFilter === 'clocked_out')
                                        Clocked Out
                                    @elseif($attendanceStatusFilter === 'active')
                                        Active
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $attendanceStatusFilter)) }}
                                    @endif
                                </button>

                                <ul @class('dropdown-menu') aria-labelledby="attendanceStatusFilterDropdown">
                                    <li>
                                        <a @class('dropdown-item') wire:click="$set('attendanceStatusFilter', '')">
                                            All Status
                                        </a>
                                    </li>
                                    <li>
                                        <a @class('dropdown-item') wire:click="$set('attendanceStatusFilter', 'clocked_out')">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>Clocked Out
                                        </a>
                                    </li>
                                    <li>
                                        <a @class('dropdown-item') wire:click="$set('attendanceStatusFilter', 'active')">
                                            <i class="bi bi-clock-fill text-primary me-2"></i>Active
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- RIGHT SIDE --}}
                        <div @class('d-flex gap-2')>
                            <button
                                @class('btn btn-success')
                                wire:click="exportAttendanceData"
                            >
                                <i @class('bi bi-download me-2')></i>Export
                            </button>
                        </div>
                    </div>
                </div>

                <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
                    <h3>Daily Attendance Tracker</h3>
                    <p @class('text-secondary mb-0')>
                        Attendance records overview
                        @if($attendanceSearch || $attendanceStatusFilter)
                            <span class="badge bg-primary ms-2">{{ count($this->filteredAttendance) }} results</span>
                        @endif
                    </p>
                </div>
                <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
                    <table @class('table')>
                        <thead>
                            <tr @class('bg-dark')>
                                <th @class('text-secondary')>Employee</th>
                                <th @class('text-secondary')>Date</th>
                                <th @class('text-secondary')>Time In</th>
                                <th @class('text-secondary')>Time Out</th>
                                <th @class('text-secondary')>Total Hours</th>
                                <th @class('text-secondary')>Status</th>
                                <th @class('text-secondary')>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->filteredAttendance as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if(isset($record['employee']['profile_picture']))
                                                <img src="{{ $record['employee']['profile_picture'] }}" 
                                                     class="rounded-circle me-2" 
                                                     width="32" height="32"
                                                     alt="Avatar">
                                            @else
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px;">
                                                    {{ substr($record['employee']['first_name'] ?? 'U', 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $record['employee']['first_name'] ?? '' }} {{ $record['employee']['last_name'] ?? '' }}</div>
                                                <div class="small text-muted">{{ $record['employee']['position'] ?? 'Employee' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ isset($record['date']) ? \Carbon\Carbon::parse($record['date'])->format('M d, Y') : '-' }}</td>
                                    <td>
                                        @if(isset($record['clock_in_time']))
                                            <span class="text-success">
                                                <i class="bi bi-clock"></i> 
                                                {{ \Carbon\Carbon::parse($record['clock_in_time'])->format('h:i A') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($record['clock_out_time']))
                                            <span class="text-muted">
                                                <i class="bi bi-clock-history"></i> 
                                                {{ \Carbon\Carbon::parse($record['clock_out_time'])->format('h:i A') }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold {{ ($record['total_hours'] ?? 0) > 8 ? 'text-success' : '' }}">
                                            {{ $record['total_hours'] ?? '0.00' }} hrs
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            ($record['status'] ?? '') === 'clocked_out' ? 'success' : 
                                            (($record['status'] ?? '') === 'active' ? 'primary' : 'secondary') 
                                        }}">
                                            {{ ucfirst(str_replace('_', ' ', $record['status'] ?? 'unknown')) }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="bi bi-geo-alt text-muted small"></i>
                                        {{ $record['location'] ?? 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-x display-4 d-block mb-2"></i>
                                        No attendance records found for today.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- EVALUATION SCHEDULE MODAL --}}
    @if($showScheduleModal && $scheduleEmployee)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title">
                            <i class="bi bi-calendar3 me-2"></i>Evaluation Schedule
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeScheduleModal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Employee Info Card --}}
                        <div class="card mb-4 border-0 bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                        {{ substr($scheduleEmployee['name'], 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">{{ $scheduleEmployee['name'] }}</h5>
                                        <p class="mb-1 text-muted">
                                            <i class="bi bi-briefcase me-1"></i>{{ $scheduleEmployee['position'] }}
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="bi bi-building me-1"></i>{{ $scheduleEmployee['department'] }}
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-calendar me-1"></i>Hired: {{ $scheduleEmployee['hire_date'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-body text-center py-2">
                                        <h3 class="mb-0 text-success">{{ $scheduleEmployee['db_evaluations'] ?? 0 }}</h3>
                                        <small class="text-muted">Total Evaluations</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-primary">
                                    <div class="card-body text-center py-2">
                                        <h3 class="mb-0 text-primary">{{ $scheduleEmployee['completed_db_evaluations'] ?? 0 }}</h3>
                                        <small class="text-muted">Completed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning">
                                    <div class="card-body text-center py-2">
                                        <h3 class="mb-0 text-warning">{{ $scheduleEmployee['pending_evaluations'] ?? 0 }}</h3>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Monthly Schedule --}}
                        <h6 class="mb-3"><i class="bi bi-list-ul me-2"></i>Monthly Evaluation Schedule</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th>Evaluation Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($scheduleEmployee['monthly_evaluations'] ?? [] as $month => $eval)
                                        <tr class="{{ $eval['is_current'] ? 'table-warning' : '' }}">
                                            <td>
                                                <i class="bi bi-calendar-event me-1 {{ $eval['is_current'] ? 'text-warning' : 'text-muted' }}"></i>
                                                {{ $month }}
                                                @if($eval['is_current'])
                                                    <span class="badge bg-warning text-dark ms-1">Current</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($eval['evaluation_date'])->format('M d, Y') }}</td>
                                            <td>
                                                @if($eval['status'] == 'completed')
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span>
                                                @elseif($eval['status'] == 'pending')
                                                    <span class="badge bg-danger"><i class="bi bi-clock me-1"></i>Pending</span>
                                                @elseif($eval['status'] == 'current')
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-arrow-right-circle me-1"></i>Due</span>
                                                @else
                                                    <span class="badge bg-info"><i class="bi bi-calendar me-1"></i>Upcoming</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No evaluation schedule available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeScheduleModal">
                            <i class="bi bi-x-lg me-1"></i>Close
                        </button>
                        <button type="button" class="btn btn-success" wire:click="goToEvaluate({{ $scheduleEmployee['id'] }})">
                            <i class="bi bi-clipboard-check me-1"></i>Evaluate Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
