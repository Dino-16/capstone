@section('page-title', 'Management Dashboard')
@section('page-subtitle', 'Strategic HR Oversight')
@section('breadcrumbs', 'Management Dashboard')

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
    </style>

    <!-- Management High-Level Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('evaluations') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-blue-100 p-3 me-3">
                                <i class="bi bi-graph-up text-blue-600 fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $performanceStats['average_score'] }}</h5>
                                <small class="text-muted">Performance Score</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('employees') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-green-100 p-3 me-3">
                                <i class="bi bi-people text-green-600 fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $statusCounts['employees'] }}</h5>
                                <small class="text-muted">Total Employees</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('applications') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-orange-100 p-3 me-3">
                                <i class="bi bi-person-plus text-orange-600 fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $statusCounts['applications'] }}</h5>
                                <small class="text-muted">Total Applications</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('reward-giving') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-purple-100 p-3 me-3">
                                <i class="bi bi-award text-purple-600 fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $recognitionStats['rewards_given'] }}</h5>
                                <small class="text-muted">Rewards Given</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('document-checklists') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                <i class="bi bi-clipboard-check text-info fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $onboardingStats['document_checklists'] }}</h5>
                                <small class="text-muted">Onboarding Checklists</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 col-lg-2">
            <a href="{{ route('orientation-schedule') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="bi bi-calendar-event text-warning fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $onboardingStats['orientations'] }}</h5>
                                <small class="text-muted">Scheduled Orientations</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Organizational Growth Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="400" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Department Strength</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>





    <!-- AI Analytics Section -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-robot me-2"></i>AI Recruitment Analytics</h5>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="card-title fw-bold text-secondary mb-0">Average Candidate Score</h6>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2">
                            <i class="bi bi-star-fill text-success"></i>
                        </div>
                    </div>
                    <h2 class="display-4 fw-bold text-success mb-0">{{ $aiAnalytics['average_score'] }}<span class="fs-4 text-muted">/100</span></h2>
                    <small class="text-muted">Based on {{ $aiAnalytics['total_candidates'] }} analyzed profiles</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">Qualification Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="qualificationChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">Top Extracted Skills</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($aiAnalytics['top_skills'] as $skill => $count)
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-4 py-3">
                                <span class="fw-medium">{{ ucwords($skill) }}</span>
                                <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">No skills data available</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-blue-100 { background-color: #ebf8ff; }
        .text-blue-600 { color: #3182ce; }
        .bg-green-100 { background-color: #f0fff4; }
        .text-green-600 { color: #38a169; }
        .bg-orange-100 { background-color: #fffaf0; }
        .text-orange-600 { color: #dd6b20; }
        .bg-purple-100 { background-color: #faf5ff; }
        .text-purple-600 { color: #805ad5; }
    </style>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyData['months']),
            datasets: [
                {
                    label: 'Talent Acquisition',
                    data: @json($monthlyData['applicationsPercent']),
                    borderColor: '#3182ce', // Blue
                    backgroundColor: 'rgba(49, 130, 206, 0.1)',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'Performance Evaluations',
                    data: @json($monthlyData['evaluationsPercent']),
                    borderColor: '#38a169', // Green
                    backgroundColor: 'rgba(56, 161, 105, 0.1)',
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'Employee Recognition',
                    data: @json($monthlyData['rewardsPercent']),
                    borderColor: '#805ad5', // Purple
                    backgroundColor: 'rgba(128, 90, 213, 0.1)',
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: { 
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const datasetIndex = context.datasetIndex;
                            const percentage = context.parsed.y;
                            const monthIndex = context.dataIndex;
                            
                            const countsMap = {
                                0: @json($monthlyData['applications']),
                                1: @json($monthlyData['evaluations']),
                                2: @json($monthlyData['rewards'])
                            };
                            
                            const totalsMap = {
                                0: {{ $monthlyData['totalApplications'] }},
                                1: {{ $monthlyData['totalEvaluations'] }},
                                2: {{ $monthlyData['totalRewards'] }}
                            };
                            
                            const count = countsMap[datasetIndex][monthIndex];
                            const total = totalsMap[datasetIndex];
                            
                            return context.dataset.label + ': ' + percentage.toFixed(1) + '% (' + count + ' of ' + total + ')';
                        }
                    }
                }
            },
            scales: { 
                y: { 
                    beginAtZero: true, 
                    max: 100,
                    grid: { display: false },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Percentage of Total'
                    }
                }, 
                x: { grid: { display: false } } 
            }
        }
    });

    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    new Chart(departmentCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($departmentData['percentages'])),
            datasets: [{
                data: @json(array_values($departmentData['percentages'])),
                backgroundColor: '#805ad5',
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems.length > 0 ? tooltipItems[0].label : '';
                        },
                        label: function(context) {
                            const value = context.parsed.x || 0;
                            const counts = @json($departmentData['counts']);
                            const label = context.label || '';
                            const count = counts[label] || 0;
                            return value.toFixed(1) + '% (' + count + ' employees)';
                        }
                    }
                }
            },
            scales: { 
                x: { 
                    grid: { display: false },
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }, 
                y: { grid: { display: false } } 
            }
        }
    });

    // AI Qualification Chart
    const qualificationCtx = document.getElementById('qualificationChart').getContext('2d');
    new Chart(qualificationCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($aiAnalytics['distribution'])),
            datasets: [{
                data: @json(array_values($aiAnalytics['distribution'])),
                backgroundColor: [
                    '#198754', // Exceptional - Success
                    '#20c997', // Highly Qualified - Teal
                    '#0dcaf0', // Qualified - Info
                    '#ffc107', // Moderately - Warning
                    '#dc3545'  // Not Qualified - Danger
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 11 }
                    }
                }
            },
            cutout: '70%'
        }
    });
</script>
