@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview and quick insights')
@section('breadcrumbs', 'Dashboard')

<div class="pt-2">
    <!-- Main Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm h-100 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $statusCounts['requisitions'] }}</h4>
                            <small class="text-muted">Pending Requisitions</small>
                        </div>
                        <div class="text-primary opacity-75">
                            <i class="bi bi-hourglass-split fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm h-100 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $statusCounts['jobs'] }}</h4>
                            <small class="text-muted">Active Jobs</small>
                        </div>
                        <div class="text-success opacity-75">
                            <i class="bi bi-briefcase-fill fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- 
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm h-100 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $statusCounts['applications'] }}</h4>
                            <small class="text-muted">Total Applications</small>
                        </div>
                        <div class="text-info opacity-75">
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        --}}
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm h-100 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $statusCounts['employees'] }}</h4>
                            <small class="text-muted">Total Employees</small>
                        </div>
                        <div class="text-warning opacity-75">
                            <i class="bi bi-person-badge-fill fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm h-100 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $onboardingStats['document_checklists'] }}</h4>
                            <small class="text-muted">Document Checklists</small>
                        </div>
                        <div class="text-secondary opacity-75">
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm h-100 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $onboardingStats['orientations'] }}</h4>
                            <small class="text-muted">Orientations</small>
                        </div>
                        <div class="text-dark opacity-75">
                            <i class="bi bi-mortarboard fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm h-100 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $performanceStats['evaluations'] }}</h4>
                            <small class="text-muted">Evaluations</small>
                        </div>
                        <div class="text-primary opacity-75">
                            <i class="bi bi-clipboard-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-white text-dark shadow-sm h-100 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $recognitionStats['rewards_given'] }}</h4>
                            <small class="text-muted">Rewards Given</small>
                        </div>
                        <div class="text-success opacity-75">
                            <i class="bi bi-award fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Monthly Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Department Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-3">
        {{--
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Applications</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivities['recent_applications'] as $application)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $application->name }}</strong>
                                <br><small class="text-muted">{{ $application->position ?? 'N/A' }}</small>
                            </div>
                            <small class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted text-center">No recent applications</p>
                    @endforelse
                </div>
            </div>
        </div>
        --}}
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Evaluations</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivities['recent_evaluations'] as $evaluation)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $evaluation->employee_name }}</strong>
                                <br><small class="text-muted">Score: {{ $evaluation->overall_score }}/100</small>
                            </div>
                            <small class="text-muted">{{ $evaluation->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted text-center">No recent evaluations</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Rewards</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivities['recent_rewards'] as $reward)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $reward->employee_name }}</strong>
                                <br><small class="text-muted">{{ $reward->reward->name ?? 'N/A' }}</small>
                            </div>
                            <small class="text-muted">{{ $reward->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted text-center">No recent rewards</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyData['months']),
            datasets: [{
                label: 'Evaluations',
                data: @json($monthlyData['evaluations']),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }, {
                label: 'Rewards',
                data: @json($monthlyData['rewards']),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Department Distribution Chart
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    new Chart(departmentCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($departmentData)),
            datasets: [{
                data: @json(array_values($departmentData)),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
