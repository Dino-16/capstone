@section('page-title', 'Reports')
@section('page-subtitle', 'Generate and manage HR analytics reports')
@section('breadcrumbs', 'Reports')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- REPORT TYPE CARDS --}}
    <div class="row g-3 mb-4">
        @php
            $reportInfo = [
                'requisition' => [
                    'icon' => 'bi-clipboard-data',
                    'color' => 'primary',
                    'title' => 'Requisition Reports',
                    'description' => 'Track job requisition requests, approval status, and hiring timelines.',
                ],
                'jobpost' => [
                    'icon' => 'bi-briefcase-fill',
                    'color' => 'success',
                    'title' => 'Job Posting Reports',
                    'description' => 'Analyze active job postings, application rates, and position fill rates.',
                ],
                'employee' => [
                    'icon' => 'bi-people-fill',
                    'color' => 'info',
                    'title' => 'Employee Reports',
                    'description' => 'Overview of employee data, onboarding status, and HR document compliance.',
                ],
                'documentchecklist' => [
                    'icon' => 'bi-file-earmark-check-fill',
                    'color' => 'warning',
                    'title' => 'Document Checklist Reports',
                    'description' => 'Monitor employee document submission and compliance tracking.',
                ],
                'orientationschedule' => [
                    'icon' => 'bi-calendar-check-fill',
                    'color' => 'danger',
                    'title' => 'Orientation Schedule Reports',
                    'description' => 'Track new hire orientation schedules, attendance, and completion status.',
                ],
                'evaluationrecords' => [
                    'icon' => 'bi-star-fill',
                    'color' => 'secondary',
                    'title' => 'Performance Evaluation Reports',
                    'description' => 'Comprehensive performance review data across all evaluation metrics.',
                ],
                'rewards' => [
                    'icon' => 'bi-trophy-fill',
                    'color' => 'success',
                    'title' => 'Rewards Catalog Reports',
                    'description' => 'List of available rewards, types, and point requirements.',
                ],
                'giverewards' => [
                    'icon' => 'bi-gift-fill',
                    'color' => 'primary',
                    'title' => 'Awarded Rewards Reports',
                    'description' => 'History of rewards given to employees and recognition trends.',
                ],
            ];
        @endphp

        @foreach($reportInfo as $type => $info)
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm hover-lift" style="cursor: pointer;" wire:click="quickGenerate('{{ $type }}')">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-{{ $info['color'] }} bg-opacity-10 p-3 me-3">
                                <i class="bi {{ $info['icon'] }} text-{{ $info['color'] }} fs-4"></i>
                            </div>
                            <h6 class="mb-0 fw-semibold">{{ $info['title'] }}</h6>
                        </div>
                        <p class="text-muted small mb-3">{{ $info['description'] }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $info['color'] }} bg-opacity-10 text-{{ $info['color'] }}">
                                <i class="bi bi-file-earmark-excel me-1"></i>Excel
                            </span>
                            <button class="btn btn-sm btn-outline-{{ $info['color'] }}" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="quickGenerate('{{ $type }}')">
                                    <i class="bi bi-download me-1"></i>Export
                                </span>
                                <span wire:loading wire:target="quickGenerate('{{ $type }}')">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- CUSTOM REPORT GENERATOR --}}
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center">
                <i class="bi bi-plus-circle-fill text-primary me-2"></i>
                <h5 class="mb-0">Save Custom Report</h5>
            </div>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                Save a report configuration for quick access later. Named reports appear in your report history below.
            </p>
            <form wire:submit="saveReport">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="reportName" class="form-label fw-semibold">Report Name</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="reportName" 
                            wire:model="reportName" 
                            placeholder="e.g., Q1 2026 Hiring Report"
                            required
                        >
                        @error('reportName')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <label for="reportType" class="form-label fw-semibold">Report Type</label>
                        <select 
                            class="form-select" 
                            id="reportType" 
                            wire:model="reportType"
                            required
                        >
                            <option value="">Select Report Type</option>
                            @foreach($reportTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reportType')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save me-1"></i>Save Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- HEADER ACTIONS --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <x-search-input
                wire:model.live.debounce.3s="search"
                placeholder="Search saved reports..."
            />
        </div>

        <div class="dropdown">
            <button
                type="button"
                id="filterDropdown"
                data-bs-toggle="dropdown"
                @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
            >
                <i @class('bi bi-funnel-fill me-2')></i>
                Filter: {{ $typeFilter === 'All' ? 'All Types' : ($reportTypes[$typeFilter] ?? $typeFilter) }}
            </button>

            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                <li>
                    <a class="dropdown-item {{ $typeFilter === 'All' ? 'active' : '' }}" wire:click="$set('typeFilter', 'All')">
                        <i class="bi bi-collection me-2"></i>All Types
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                @foreach($reportTypes as $value => $label)
                    <li>
                        <a class="dropdown-item {{ $typeFilter === $value ? 'active' : '' }}" wire:click="$set('typeFilter', '{{ $value }}')">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- SAVED REPORTS TABLE --}}
    <div class="p-4 bg-white rounded border rounded-bottom-0 border-bottom-0">
        <div class="d-flex align-items-center">
            <i class="bi bi-clock-history text-primary me-2 fs-4"></i>
            <div>
                <h5 class="mb-0">Saved Reports</h5>
                <p class="text-muted small mb-0">Your saved report configurations for quick access</p>
            </div>
        </div>
    </div>
    <div class="table-responsive border rounded bg-white px-4 rounded-top-0 border-top-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-secondary fw-normal">Report Name</th>
                    <th class="text-secondary fw-normal">Type</th>
                    <th class="text-secondary fw-normal">Status</th>
                    <th class="text-secondary fw-normal">Created</th>
                    <th class="text-secondary fw-normal text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->filteredReports as $report)
                    <tr wire:key="{{ $report->id }}">
                        <td>
                            <div class="fw-semibold">{{ $report->report_name }}</div>
                            <small class="text-muted">{{ $report->report_file }}</small>
                        </td>
                        <td>
                            @php
                                $typeColors = [
                                    'requisition' => 'primary',
                                    'jobpost' => 'success',
                                    'employee' => 'info',
                                    'documentchecklist' => 'warning',
                                    'orientationschedule' => 'danger',
                                    'evaluationrecords' => 'secondary',
                                    'rewards' => 'success',
                                    'giverewards' => 'primary',
                                ];
                            @endphp
                            <span class="badge bg-{{ $typeColors[$report->report_type] ?? 'secondary' }}">
                                {{ $reportTypes[$report->report_type] ?? ucfirst($report->report_type) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-check-circle me-1"></i>{{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $report->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $report->created_at->format('h:i A') }}</small>
                        </td>
                         <td class="text-center">
                             <div class="d-flex justify-content-center gap-2">
                                 <button
                                     wire:click="quickGenerate('{{ $report->report_type }}')"
                                     class="btn btn-sm btn-outline-success"
                                     title="Download Report"
                                     wire:loading.attr="disabled"
                                 >
                                     <span wire:loading.remove wire:target="quickGenerate('{{ $report->report_type }}')">
                                         <i class="bi bi-download"></i>
                                     </span>
                                     <span wire:loading wire:target="quickGenerate('{{ $report->report_type }}')">
                                         <span class="spinner-border spinner-border-sm"></span>
                                     </span>
                                 </button>
                                 @if(session('user.position') === 'HR Manager')
                                 <button 
                                     class="btn btn-sm btn-danger" 
                                     title="Delete Report"
                                     wire:click="deleteReport({{ $report->id }})"
                                     wire:confirm="Are you sure you want to delete this report?"
                                 >
                                     <i class="bi bi-trash"></i>
                                 </button>
                                 @endif
                             </div>
                         </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-file-earmark-x d-block mx-auto fs-1 text-muted mb-3"></i>
                            <p class="text-muted mb-1">No saved reports yet</p>
                            <small class="text-muted">Use the cards above for quick exports or save a custom report for later.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- REPORT GUIDE --}}
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header bg-light">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle text-primary me-2"></i>
                <h6 class="mb-0">Report Guide</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-semibold text-primary mb-2">
                        <i class="bi bi-building me-1"></i>Recruitment Reports
                    </h6>
                    <ul class="list-unstyled text-muted small mb-3">
                        <li class="mb-1"><i class="bi bi-dot"></i><strong>Requisition:</strong> Job opening requests and approval workflows</li>
                        <li class="mb-1"><i class="bi bi-dot"></i><strong>Job Posting:</strong> Active positions, locations, and application counts</li>
                    </ul>

                    <h6 class="fw-semibold text-info mb-2">
                        <i class="bi bi-person-badge me-1"></i>Onboarding Reports
                    </h6>
                    <ul class="list-unstyled text-muted small mb-3">
                        <li class="mb-1"><i class="bi bi-dot"></i><strong>Employee:</strong> New hire data and onboarding progress</li>
                        <li class="mb-1"><i class="bi bi-dot"></i><strong>Document Checklist:</strong> Required document submission status</li>
                        <li class="mb-1"><i class="bi bi-dot"></i><strong>Orientation:</strong> Scheduled orientations and attendance</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-semibold text-warning mb-2">
                        <i class="bi bi-graph-up me-1"></i>Performance Reports
                    </h6>
                    <ul class="list-unstyled text-muted small mb-3">
                        <li class="mb-1"><i class="bi bi-dot"></i><strong>Evaluation Records:</strong> Employee performance scores and feedback</li>
                    </ul>

                    <h6 class="fw-semibold text-success mb-2">
                        <i class="bi bi-award me-1"></i>Recognition Reports
                    </h6>
                    <ul class="list-unstyled text-muted small mb-3">
                        <li class="mb-1"><i class="bi bi-dot"></i><strong>Rewards Catalog:</strong> Available rewards and point values</li>
                        <li class="mb-1"><i class="bi bi-dot"></i><strong>Awarded Rewards:</strong> Recognition history and trends</li>
                    </ul>

                    <div class="alert alert-light border mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <small>All reports export to <strong>Excel (.xlsx)</strong> format for easy analysis and sharing.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>

</div>
