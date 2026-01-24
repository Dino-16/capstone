<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title h2 mb-3">Performance Tracker</h2>
                        <p class="card-text text-muted">Employee performance tracking and monthly evaluation schedule</p>
                        
                        @if(session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session()->get('error') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Loading State -->
                @if(isset($loading) && $loading)
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading employee data...</p>
                    </div>
                @endif

                <!-- Debug Info -->
                @if(!$loading && !empty($employees))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Debug Info</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Employees Loaded:</strong> {{ count($employees) }}</p>
                            @if(!empty($employees))
                                <p><strong>First Employee:</strong> {{ $employees[0]['name'] ?? 'No name' }}</p>
                                <p><strong>Sample Data:</strong> {{ json_encode(array_slice($employees, 0, 1)) }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Selected Employee Details -->
                @if($selectedEmployee)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Employee Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Name</label>
                                        <input type="text" class="form-control" wire:model="email" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Position</label>
                                        <input type="text" class="form-control" wire:model="position" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Department</label>
                                        <input type="text" class="form-control" wire:model="department" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Hire Date</label>
                                        <input type="text" class="form-control" wire:model="hireDate" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Monthly Evaluations -->
                @if($selectedEmployee && !empty($monthlyEvaluations))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                Monthly Evaluation Schedule 
                                <span class="badge bg-primary ms-2">{{ count($monthlyEvaluations) }} Months</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($monthlyEvaluations as $evaluation)
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-{{ 
                                            $evaluation['status'] == 'completed' ? 'success' : 
                                            ($evaluation['status'] == 'current' ? 'warning' : 
                                            ($evaluation['status'] == 'pending' ? 'danger' : 'secondary')) 
                                        }}">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">{{ $evaluation['month'] }}</h6>
                                                <div class="mb-2">
                                                    <small class="text-muted">{{ $evaluation['evaluation_date'] }}</small>
                                                </div>
                                                <div>
                                                    <span class="badge bg-{{ 
                                                        $evaluation['status'] == 'completed' ? 'success' : 
                                                        ($evaluation['status'] == 'current' ? 'warning' : 
                                                        ($evaluation['status'] == 'pending' ? 'danger' : 'info')) 
                                                    }} mb-2">
                                                        {{ ucfirst($evaluation['status']) }}
                                                    </span>
                                                </div>
                                                @if($evaluation['status'] == 'current' || $evaluation['status'] == 'pending')
                                                    <div class="mt-2">
                                                        <a href="{{ route('evaluations') }}?employee={{ $selectedEmployee['id'] }}&month={{ $evaluation['evaluation_date'] }}" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-clipboard-check me-1"></i>
                                                            Start Evaluation
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                @endforeach
                            </div>

                            <!-- Evaluation Summary -->
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Completed</h6>
                                            <h3 class="card-text">{{ collect($monthlyEvaluations)->where('status', 'completed')->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Pending</h6>
                                            <h3 class="card-text">
                                                {{ collect($monthlyEvaluations)->where('status', 'pending')->count() }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-dark">
                                        <div class="card-body">
                                            <h6 class="card-title">Current</h6>
                                            <h3 class="card-text">
                                                {{ collect($monthlyEvaluations)->where('status', 'current')->count() }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h6 class="card-title">Upcoming</h6>
                                            <h3 class="card-text">
                                                {{ collect($monthlyEvaluations)->where('status', 'upcoming')->count() }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Employees List -->
                @if(!$selectedEmployee)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                All Employees 
                                <span class="badge bg-primary ms-2">{{ count($employees) }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Position</th>
                                            <th>Department</th>
                                            <th>Hire Date</th>
                                            <th>Pending</th>
                                            <th>Completed</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employees as $employee)
                                            <tr>
                                                <td>
                                                    <strong>{{ $employee['name'] }}</strong>
                                                </td>
                                                <td>{{ $employee['email'] }}</td>
                                                <td>{{ $employee['position'] }}</td>
                                                <td>{{ $employee['department'] }}</td>
                                                <td>{{ $employee['hire_date'] }}</td>
                                                <td>
                                                    <span class="badge bg-danger">
                                                        {{ collect($employee['monthly_evaluations'])->where('status', 'pending')->count() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        {{ collect($employee['monthly_evaluations'])->where('status', 'completed')->count() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(collect($employee['monthly_evaluations'])->where('status', 'pending')->count() > 0)
                                                        <a href="{{ route('evaluations') }}?employee={{ $employee['id'] }}" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-clipboard-check me-1"></i>
                                                            Evaluate
                                                        </a>
                                                    @else
                                                        <span class="badge bg-success">Up to date</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    No employees found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
