@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview and quick insights')
@section('breadcrumbs', 'Dashboard')

<div class="pt-2">
    <!-- Clickable Stats Card Styles -->
    <style>
        .stat-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
        }
        .stat-card-link .card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .stat-card-link:hover .card {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        .stat-card-link:hover .card .text-primary { color: #0056b3 !important; }
        .stat-card-link:hover .card .text-success { color: #1e7e34 !important; }
        .stat-card-link:hover .card .text-info { color: #0c5460 !important; }
        .stat-card-link:hover .card .text-warning { color: #c69500 !important; }
        .stat-card-link:hover .card .text-secondary { color: #545b62 !important; }
        .stat-card-link:hover .card .text-dark { color: #1d2124 !important; }
    </style>

    <!-- Main Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="{{ route('recruitment-requests') }}" class="stat-card-link">
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
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('job-postings') }}" class="stat-card-link">
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
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('applications') }}" class="stat-card-link">
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
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('employees') }}" class="stat-card-link">
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
            </a>
        </div>
    </div>

    <!-- Department Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="{{ route('document-checklists') }}" class="stat-card-link">
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
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('orientation-schedule') }}" class="stat-card-link">
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
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('evaluations') }}" class="stat-card-link">
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
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('reward-giving') }}" class="stat-card-link">
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
            </a>
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
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Applications</h5>
                </div>
                <div class="card-body">
                    @forelse($recentActivities['recent_applications'] as $application)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $application->first_name }} {{ $application->last_name }}</strong>
                                <br><small class="text-muted">{{ $application->applied_position ?? 'N/A' }}</small>
                            </div>
                            <small class="text-muted">{{ $application->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-muted text-center">No recent applications</p>
                    @endforelse
                </div>
            </div>
        </div>
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
    // Monthly Trends Chart (Percentages)
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyData['months']),
            datasets: [{
                label: 'Applications ({{ $monthlyData["totalApplications"] }} total)',
                data: @json($monthlyData['applicationsPercent']),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Evaluations ({{ $monthlyData["totalEvaluations"] }} total)',
                data: @json($monthlyData['evaluationsPercent']),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1,
                fill: true
            }, {
                label: 'Rewards ({{ $monthlyData["totalRewards"] }} total)',
                data: @json($monthlyData['rewardsPercent']),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const datasetLabel = context.dataset.label.split(' (')[0];
                            const percentage = context.parsed.y;
                            const monthIndex = context.dataIndex;
                            
                            // Get the raw counts
                            const applicationsCounts = @json($monthlyData['applications']);
                            const evaluationsCounts = @json($monthlyData['evaluations']);
                            const rewardsCounts = @json($monthlyData['rewards']);
                            
                            let count = 0;
                            let total = 0;
                            if (datasetLabel === 'Applications') {
                                count = applicationsCounts[monthIndex];
                                total = {{ $monthlyData['totalApplications'] }};
                            } else if (datasetLabel === 'Evaluations') {
                                count = evaluationsCounts[monthIndex];
                                total = {{ $monthlyData['totalEvaluations'] }};
                            } else if (datasetLabel === 'Rewards') {
                                count = rewardsCounts[monthIndex];
                                total = {{ $monthlyData['totalRewards'] }};
                            }
                            
                            return datasetLabel + ': ' + percentage.toFixed(1) + '% (' + count + ' of ' + total + ')';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Percentage of Total'
                    }
                }
            }
        }
    });

    // Department Distribution Chart (Percentages)
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    new Chart(departmentCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($departmentData['percentages'])),
            datasets: [{
                data: @json(array_values($departmentData['percentages'])),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#E7E9ED',
                    '#8B5CF6',
                    '#10B981',
                    '#F59E0B'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems.length > 0 ? tooltipItems[0].label : '';
                        },
                        label: function(context) {
                            const value = context.parsed || 0;
                            const counts = @json($departmentData['counts']);
                            const label = context.label || '';
                            const count = counts[label] || 0;
                            return value.toFixed(1) + '% (' + count + ' employees)';
                        }
                    }
                }
            }
        }
    });

</script>
