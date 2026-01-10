@section('page-title', 'Reports')
@section('page-subtitle', 'Manage system reports')
@section('breadcrumbs', 'Reports')

<div @class('pt-2')>

    {{-- SUCCESS TOAST --}}
    <x-toast />

    {{-- STATUS CARDS --}}
    @include('livewire.user.includes.report-cards')

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center mb-4')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-text-input
                    type="search"
                    wire:model.live.debounce.3s="search"
                    placeholder="Search reports..."
                />
            </div>

            {{-- FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="filterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: {{ $typeFilter }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="filterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('typeFilter', 'All')">
                            All
                        </a>
                    </li>
                    @foreach($reportTypes as $value => $label)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('typeFilter', '{{ $value }}')">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                
                <button
                    @class('btn btn-primary')
                    data-bs-toggle="modal"
                    data-bs-target="#createReportModal"
                >
                    <i @class('bi bi-plus-lg me-2')></i>Create Report
                </button>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
        <h3>All Reports</h3>
        <p @class('text-secondary mb-0')>
            Overview of generated reports
        </p>
    </div>
    <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
        <table @class('table')>
            <thead>
                <tr @class('bg-dark')>
                    <th @class('text-secondary')>Report Name</th>
                    <th @class('text-secondary')>Report Type</th>
                    <th @class('text-secondary')>File Type</th>
                    <th @class('text-secondary')>Status</th>
                    <th @class('text-secondary')>Created At</th>
                    <th @class('text-secondary')>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->filteredReports as $report)
                    <tr wire:key="{{ $report->id }}">
                        <td>{{ $report->report_name }}</td>
                        <td>
                            <span @class('badge bg-primary')>
                                {{ ucfirst(str_replace('_', ' ', $report->report_type)) }}
                            </span>
                        </td>
                        <td>
                            <span @class('badge bg-info')>
                                {{ strtoupper($report->report_file) }}
                            </span>
                        </td>
                        <td>
                            <span @class('badge bg-success')>
                                {{ ucfirst($report->status) }}
                            </span>
                        </td>
                        <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                        <td @class('gap-3')>
                            @if($report->report_type == 'requisition')
                                <button
                                    wire:click="exportRequisition"
                                    @class('btn btn-success btn-sm')
                                    title="Export Requisition"
                                >
                                    <i @class('bi bi-download')></i>
                                </button>
                            @elseif($report->report_type == 'jobpost')
                                <button
                                    wire:click="exportJobPost"
                                    @class('btn btn-success btn-sm')
                                    title="Export Job Post"
                                >
                                    <i @class('bi bi-download')></i>
                                </button>
                            @elseif($report->report_type == 'employee')
                                <button
                                    wire:click="exportEmployee"
                                    @class('btn btn-success btn-sm')
                                    title="Export Employee"
                                >
                                    <i @class('bi bi-download')></i>
                                </button>
                            @elseif($report->report_type == 'documentchecklist')
                                <button
                                    wire:click="exportDocumentChecklist"
                                    @class('btn btn-success btn-sm')
                                    title="Export Document Checklist"
                                >
                                    <i @class('bi bi-download')></i>
                                </button>
                            @elseif($report->report_type == 'orientationschedule')
                                <button
                                    wire:click="exportOrientationSchedule"
                                    @class('btn btn-success btn-sm')
                                    title="Export Orientation Schedule"
                                >
                                    <i @class('bi bi-download')></i>
                                </button>
                            @elseif($report->report_type == 'evaluationrecords')
                                <button
                                    wire:click="exportEvaluationRecords"
                                    @class('btn btn-success btn-sm')
                                    title="Export Evaluation Records"
                                >
                                    <i @class('bi bi-download')></i>
                                </button>
                            @elseif($report->report_type == 'rewards')
                                <button
                                    wire:click="exportRewards"
                                    @class('btn btn-success btn-sm')
                                    title="Export Rewards"
                                >
                                    <i @class('bi bi-download')></i>
                                </button>
                            @elseif($report->report_type == 'giverewards')
                                <button
                                    wire:click="exportGiveRewards"
                                    @class('btn btn-success btn-sm')
                                    title="Export Give Rewards"
                                >
                                    <i @class('bi bi-download')></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" @class('text-center text-muted')>
                            No reports found. Create your first report.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- CREATE REPORT MODAL --}}
    <div @class('modal fade') id="createReportModal" tabindex="-1" aria-labelledby="createReportModalLabel" aria-hidden="true">
        <div @class('modal-dialog')>
            <div @class('modal-content')>
                <div @class('modal-header')>
                    <h5 @class('modal-title') id="createReportModalLabel">Create New Report</h5>
                    <button type="button" @class('btn-close') data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit="saveReport">
                    <div @class('modal-body')>
                        <div @class('mb-3')>
                            <label for="reportName" @class('form-label')>Report Name</label>
                            <input 
                                type="text" 
                                @class('form-control') 
                                id="reportName" 
                                wire:model="reportName" 
                                placeholder="Enter report name"
                                required
                            >
                            @error('reportName')
                                <div @class('text-danger small mt-1')>{{ $message }}</div>
                            @enderror
                        </div>
                        <div @class('mb-3')>
                            <label for="reportType" @class('form-label')>Report Type</label>
                            <select 
                                @class('form-select') 
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
                                <div @class('text-danger small mt-1')>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div @class('modal-footer')>
                        <button type="button" @class('btn btn-secondary') data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" @class('btn btn-primary')>
                            <i @class('bi bi-save me-2')></i>Save Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
