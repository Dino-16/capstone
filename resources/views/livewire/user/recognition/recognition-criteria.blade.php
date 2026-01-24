<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title h2 mb-2">Recognition Criteria</h2>
                        <p class="card-text text-muted">Employee recognition based on attendance performance and other criteria</p>
                    </div>
                </div>

                <!-- Attendance Section -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title h5 mb-0">Attendance-Based Recognition</h3>
                        <button 
                            wire:click="loadAttendanceData" 
                            wire:loading.attr="disabled"
                            class="btn btn-primary"
                        >
                            <span wire:loading.remove>
                                <i class="bi bi-arrow-clockwise me-2"></i>Refresh Data
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Refreshing...
                            </span>
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Loading State -->
                        @if ($loading)
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-3 text-muted">Loading attendance data...</p>
                            </div>
                        @endif

                        <!-- Error State -->
                        @if ($error)
                            <div class="alert alert-danger" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>
                                        <strong>Error:</strong> {{ $error }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Attendance Data Table -->
                        @if (!$loading && !$error && !empty($attendanceData))
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                            <th>Attendance Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse (collect($attendanceData)->filter(function ($item) { return is_array($item); })->groupBy('employee.id') as $employeeId => $employeeRecords)
                                            @php
                                                // Debug: Let's see what we're working with
                                                $firstRecord = $employeeRecords->first();
                                                $employeeName = 'N/A';
                                                $employeePosition = 'N/A';
                                                $employeeDepartment = 'N/A';
                                                
                                                if ($firstRecord && is_array($firstRecord)) {
                                                    // Debug output
                                                    if ($employeeId == 6) { // Only debug for the first employee
                                                        dd('First record structure:', $firstRecord);
                                                    }
                                                    
                                                    // Try different ways to access employee data
                                                    if (isset($firstRecord['employee']) && is_array($firstRecord['employee'])) {
                                                        $employeeName = ($firstRecord['employee']['first_name'] ?? '') . ' ' . ($firstRecord['employee']['last_name'] ?? '');
                                                        $employeePosition = $firstRecord['employee']['position'] ?? 'N/A';
                                                        $employeeDepartment = $firstRecord['employee']['department'] ?? 'N/A';
                                                    } else {
                                                        // If employee is not nested, try direct access
                                                        $employeeName = ($firstRecord['first_name'] ?? '') . ' ' . ($firstRecord['last_name'] ?? '');
                                                        $employeePosition = $firstRecord['position'] ?? 'N/A';
                                                        $employeeDepartment = $firstRecord['department'] ?? 'N/A';
                                                    }
                                                }
                                                
                                                $attendanceCount = $employeeRecords->filter(fn($record) => is_array($record))->count();
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $employeeName }}</strong></td>
                                                <td>{{ $employeePosition }}</td>
                                                <td>{{ $employeeDepartment }}</td>
                                                <td>{{ $attendanceCount }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    No attendance data available
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary Statistics -->
                            <div class="row mt-4">
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Records</h6>
                                            <h3 class="card-text">{{ collect($attendanceData)->filter(function ($item) { return is_array($item); })->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Clocked In</h6>
                                            <h3 class="card-text">
                                                {{ collect($attendanceData)->filter(function ($item) { return is_array($item); })->where('status', 'clocked_in')->count() }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Total Hours</h6>
                                            <h3 class="card-text">
                                                {{ number_format(collect($attendanceData)->filter(function ($item) { return is_array($item); })->sum('total_hours'), 1) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card bg-warning text-dark">
                                        <div class="card-body">
                                            <h6 class="card-title">Unique Employees</h6>
                                            <h3 class="card-text">
                                                {{ collect($attendanceData)->filter(function ($item) { return is_array($item); })->filter(function($record) {
                                                    return isset($record['employee']) && isset($record['employee']['id']);
                                                })->pluck('employee.id')->unique()->count() }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Empty State -->
                        @if (!$loading && !$error && empty($attendanceData))
                            <div class="text-center py-5">
                                <i class="bi bi-clipboard-data display-1 text-muted"></i>
                                <h5 class="mt-3 text-muted">No attendance data</h5>
                                <p class="text-muted">No attendance records are available at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Additional sections can be added here -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title h5 mb-3">Other Recognition Criteria</h3>
                        <p class="card-text text-muted">Additional recognition criteria will be added here in the future.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
