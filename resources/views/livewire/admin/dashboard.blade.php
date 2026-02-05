@section('page-title', 'Management Dashboard')
@section('page-subtitle', 'Strategic HR Oversight')
@section('breadcrumbs', 'Management Dashboard')

<div class="pt-2">
    <!-- Management Banner -->
    <div class="card mb-4 border-0 bg-primary text-white overflow-hidden shadow">
        <div class="card-body p-4 position-relative">
            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                <i class="bi bi-briefcase-fill style='font-size: 8rem'"></i>
            </div>
            <h3 class="fw-bold">Welcome, {{ session('user.name') }}</h3>
            <p class="mb-0 opacity-75">You have {{ $statusCounts['requisitions'] }} pending requisitions requiring your attention today.</p>
        </div>
    </div>

    <!-- Management High-Level Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-blue-100 p-3 me-3">
                            <i class="bi bi-graph-up text-blue-600 fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $performanceStats['average_score'] }}</h5>
                            <small class="text-muted">Avg Performance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-green-100 p-3 me-3">
                            <i class="bi bi-people text-green-600 fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $statusCounts['employees'] }}</h5>
                            <small class="text-muted">Force Strength</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-orange-100 p-3 me-3">
                            <i class="bi bi-person-plus text-orange-600 fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $statusCounts['applications'] }}</h5>
                            <small class="text-muted">Talent Pipeline</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-purple-100 p-3 me-3">
                            <i class="bi bi-award text-purple-600 fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $recognitionStats['rewards_given'] }}</h5>
                            <small class="text-muted">Recognitions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Organizational Growth Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="400" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Department Strength</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" width="400" height="300"></canvas>
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
            datasets: [{
                label: 'Talent Acquisition',
                data: @json($monthlyData['applications']),
                borderColor: '#3182ce',
                backgroundColor: 'rgba(49, 130, 206, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
        }
    });

    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    new Chart(departmentCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($departmentData)),
            datasets: [{
                data: @json(array_values($departmentData)),
                backgroundColor: '#805ad5',
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
        }
    });
</script>
