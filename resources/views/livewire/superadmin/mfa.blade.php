@section('page-title', 'Multi-Factor Authentication')
@section('page-subtitle', 'Configure and monitor MFA security settings')
@section('breadcrumbs', 'MFA')

<div class="container-fluid p-4">
    <div class="row mb-4">
        {{-- Header is handled by layout now --}}
    </div>

    {{-- Alert Messages --}}
    {{-- Toast --}}
    <x-toast />

    @php
        $isSuperAdminUser = strtolower(trim(session('user.position', ''))) === 'super admin';
        $colClass = $isSuperAdminUser ? 'col-lg-3' : 'col-lg-4';
    @endphp

    <div class="row g-4 mb-4">
        {{-- Global Setting --}}
        <!-- Global Setting Card -->
        <div class="col-md-6 {{ $colClass }}">
            <div class="card shadow-sm h-100 border-0 border-top border-4 border-secondary">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <h5 class="card-title fw-bold mb-2 text-secondary">Global MFA</h5>
                        <p class="text-muted small mb-0">Enable/disable system-wide MFA.</p>
                    </div>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="fw-bold {{ $isGlobalEnabled ? 'text-success' : 'text-secondary' }}">{{ $isGlobalEnabled ? 'Enabled' : 'Disabled' }}</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleGlobal" {{ $isGlobalEnabled ? 'checked' : '' }} style="cursor: pointer; transform: scale(1.2);">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HR Staff Setting Card -->
        <div class="col-md-6 {{ $colClass }}">
            <div class="card shadow-sm h-100 border-0 border-top border-4 border-warning">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <h5 class="card-title fw-bold mb-2 text-warning">HR Staff Access</h5>
                        <p class="text-muted small mb-0">Require MFA for HR Staff.</p>
                    </div>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="fw-bold {{ $isHrStaffEnabled ? 'text-success' : 'text-secondary' }}">{{ $isHrStaffEnabled ? 'Required' : 'Optional' }}</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleHrStaff" {{ $isHrStaffEnabled ? 'checked' : '' }} {{ !$isGlobalEnabled ? 'disabled' : '' }} style="cursor: pointer; transform: scale(1.2);">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HR Manager Setting Card -->
        <div class="col-md-6 {{ $colClass }}">
            <div class="card shadow-sm h-100 border-0 border-top border-4 border-primary">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <h5 class="card-title fw-bold mb-2 text-primary">HR Manager Access</h5>
                         <p class="text-muted small mb-0">Require MFA for HR Managers.</p>
                    </div>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                         <span class="fw-bold {{ $isHrManagerEnabled ? 'text-success' : 'text-secondary' }}">{{ $isHrManagerEnabled ? 'Required' : 'Optional' }}</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleHrManager" {{ $isHrManagerEnabled ? 'checked' : '' }} {{ !$isGlobalEnabled ? 'disabled' : '' }} style="cursor: pointer; transform: scale(1.2);">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Super Admin Setting Card (Only visible to Super Admins) -->
        @if($isSuperAdminUser)
        <div class="col-md-6 {{ $colClass }}">
            <div class="card shadow-sm h-100 border-0 border-top border-4 border-danger">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <h5 class="card-title fw-bold mb-2 text-danger">Super Admin Access</h5>
                         <p class="text-muted small mb-0">Require MFA for Super Admins.</p>
                    </div>
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                         <span class="fw-bold {{ $isSuperAdminEnabled ? 'text-success' : 'text-secondary' }}">{{ $isSuperAdminEnabled ? 'Required' : 'Optional' }}</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleSuperAdmin" {{ $isSuperAdminEnabled ? 'checked' : '' }} {{ !$isGlobalEnabled ? 'disabled' : '' }} style="cursor: pointer; transform: scale(1.2);">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>


    {{-- Analytics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted small fw-bold mb-1">Authenticated Logins</p>
                            <h2 class="fw-bold text-success mb-0">{{ $stats['total_verified'] }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-shield-check text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted small fw-bold mb-1">Total Failed Actions</p>
                            <h2 class="fw-bold text-danger mb-0">{{ $stats['total_failed'] }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-info border-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted small fw-bold mb-1">Total Activity Logs</p>
                            <h2 class="fw-bold text-info mb-0">{{ $stats['total_logs'] }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-person-lines-fill text-info fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Authentication Logs</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Action & Status</th>
                            <th>User (Email / Role)</th>
                            <th>IP Address</th>
                            <th>Time</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ ucwords(str_replace('_', ' ', $log->action)) }}</span>
                                        @if($log->status === 'success')
                                            <span class="badge bg-success rounded-pill align-self-start">Success</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill align-self-start">Failed</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $log->email ?? 'Unknown' }}</span>
                                        <small class="text-muted">{{ $log->role ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-monospace small">{{ $log->ip_address }}</div>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </small>
                                </td>
                                <td class="small">{{ $log->created_at->format('M d, Y h:i:s A') }}</td>
                                <td class="text-end pe-4">
                                    <button wire:click="confirmDeleteLog({{ $log->id }})" 
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-lock display-6 d-block mb-3"></i>
                                    No authentication logs found yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end py-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>

{{-- Delete Single Log Confirmation Modal --}}
@if($showDeleteModal)
<div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5); z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this log entry?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-danger" wire:click="deleteLog">Delete</button>
            </div>
        </div>
    </div>
</div>
@endif


