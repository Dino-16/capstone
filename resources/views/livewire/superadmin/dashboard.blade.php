@section('page-title', 'System Administration')
@section('page-subtitle', 'System Health & Security Overview')
@section('breadcrumbs', 'Super Admin Dashboard')

<div class="pt-2">
    <!-- System Health Alert -->
    <div @class('alert alert-info border-0 shadow-sm d-flex align-items-center mb-4') role="alert">
        <i @class('bi bi-shield-check fs-3 me-3')></i>
        <div>
            <h6 @class('mb-0 fw-bold')>System Integrity Mode: Active</h6>
            <small>All security plugins (MFA, Honeypots, Recaptcha) are monitoring traffic.</small>
        </div>
    </div>

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

    <!-- Security Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('superadmin.mfa') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="fw-bold mb-0 text-primary">Secure</h3>
                                <small class="text-muted">MFA Status</small>
                            </div>
                            <i class="bi bi-key fs-1 text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('superadmin.honeypots') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="fw-bold mb-0 text-success">Active</h3>
                                <small class="text-muted">Honeypot Traps</small>
                            </div>
                            <i class="bi bi-bug fs-1 text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('superadmin.recaptcha') }}" class="stat-card-link">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="fw-bold mb-0 text-info">Verified</h3>
                                <small class="text-muted">Recaptcha API</small>
                            </div>
                            <i class="bi bi-robot fs-1 text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Analytics Charts Area -->
    <div class="row g-3 mb-4">
        <!-- User Accounts Chart (Left, Main) -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">User Accounts Overview</h5>
                    <span class="badge bg-primary rounded-pill">Total: {{ $userStats['total'] ?? 0 }}</span>
                </div>
                <div class="card-body">
                    <canvas id="userAccountsChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Activity Audit Chart (Right, Side) -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">User Activity Load</h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="width: 100%; max-width: 250px;">
                        <canvas id="auditChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="card-title fw-bold text-secondary mb-0">Candidate Quality</h6>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2">
                            <i class="bi bi-star-fill text-success"></i>
                        </div>
                    </div>
                    <h2 class="display-4 fw-bold text-success mb-0">{{ $aiAnalytics['average_score'] }}<span class="fs-4 text-muted">/100</span></h2>
                    <small class="text-muted">Global Average Score</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">Talent Pool Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="qualificationChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">Trending Skills</h6>
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
    
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Recent System Logs</h5>
                </div>
                <div class="card-body">
                   <div class="list-group list-group-flush">
                       <div class="list-group-item px-0 border-0 mb-2">
                           <div class="d-flex w-100 justify-content-between">
                               <h6 class="mb-1 text-dark fw-semibold">MFA Challenge Issued</h6>
                               <small class="text-muted">2 mins ago</small>
                           </div>
                           <p class="mb-1 small text-muted">Admin login attempt from 192.168.1.1</p>
                       </div>
                       <div class="list-group-item px-0 border-0 mb-2">
                           <div class="d-flex w-100 justify-content-between">
                               <h6 class="mb-1 text-dark fw-semibold">Honeypot Triggered</h6>
                               <small class="text-muted">15 mins ago</small>
                           </div>
                           <p class="mb-1 small text-muted">Bot detected on application form.</p>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const auditCtx = document.getElementById('auditChart').getContext('2d');
    new Chart(auditCtx, {
        type: 'doughnut',
        data: {
            labels: ['Operational', 'Security Logs', 'System Audits'],
            datasets: [{
                data: [65, 25, 10],
                backgroundColor: ['#0d6efd', '#198754', '#ffc107'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: { 
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems.length > 0 ? tooltipItems[0].label : '';
                        },
                        label: function(context) {
                            return context.parsed.toFixed(1) + '%';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    const userCtx = document.getElementById('userAccountsChart').getContext('2d');
    @php
        $defaultUserStats = ['system' => 0, 'ess' => 0, 'systemPercent' => 0, 'essPercent' => 0];
        $mergedStats = array_merge($defaultUserStats, $userStats ?? []);
    @endphp
    const userStats = @json($mergedStats);
    
    new Chart(userCtx, {
        type: 'bar',
        data: {
            labels: ['System Accounts (Admin/HR)', 'ESS Accounts (Employees)'],
            datasets: [{
                label: 'Percentage of Total Accounts',
                data: [userStats.systemPercent, userStats.essPercent],
                backgroundColor: ['#6610f2', '#0dcaf0'],
                borderRadius: 5,
                barPercentage: 0.5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { borderDash: [2, 2] },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    title: {
                        display: true,
                        text: 'Percentage of Total Accounts'
                    }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return tooltipItems.length > 0 ? tooltipItems[0].label : '';
                        },
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed.y || 0;
                            const count = label.includes('System') ? userStats.system : userStats.ess;
                            return value.toFixed(1) + '% (' + count + ' accounts)';
                        }
                    },
                    backgroundColor: '#fff',
                    titleColor: '#000',
                    bodyColor: '#000',
                    borderColor: '#ddd',
                    borderWidth: 1
                }
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
                    '#198754', // Exceptional
                    '#20c997', // Highly Qualified
                    '#0dcaf0', // Qualified
                    '#ffc107', // Moderately
                    '#dc3545'  // Not Qualified
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            },
            cutout: '60%'
        }
    });
</script>
