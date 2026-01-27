@section('page-title', 'Performance Tracker')
@section('page-subtitle', 'Manage performance evaluations')
@section('breadcrumbs', 'Performance Tracker')

<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Monthly Performance Evaluation Table -->
                <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
                    <h3>Monthly Evaluation Schedule</h3>
                    <p @class('text-secondary mb-0')>
                        Employee evaluation overview
                    </p>
                </div>
                <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
                    <table @class('table')>
                        <thead>
                            <tr @class('bg-dark')>
                                <th @class('text-secondary')>Employee</th>
                                <th @class('text-secondary')>Hire Date</th>
                                <th @class('text-secondary')>Evaluation Status</th>
                                <th @class('text-secondary')>Next Evaluation</th>
                                <th @class('text-secondary')>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                {{ substr($employee['name'], 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $employee['name'] }}</div>
                                                <div class="small text-muted">{{ $employee['position'] }}</div>
                                            </div>
                                        </div>
                                    </td>
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
                                        <a href="{{ route('evaluations') }}?employee={{ $employee['id'] }}" class="btn btn-sm btn-outline-primary">
                                            View Schedule
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No employees found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Attendance Tracker -->
                <div @class('mt-4 p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
                    <h3>Daily Attendance Tracker</h3>
                    <p @class('text-secondary mb-0')>
                        Attendance records overview
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
                            @forelse($attendanceRecords as $record)
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
</div>
