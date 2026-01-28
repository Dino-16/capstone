<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="h3 text-gray-800">Multi-Factor Authentication (MFA) Management</h2>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        {{-- Global Setting --}}
        <!-- Global Setting Card -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold mb-2">Global MFA Switch</h5>
                        <p class="text-muted small">Enable or disable MFA for the entire system.</p>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleGlobal" {{ $isGlobalEnabled ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                        <label class="form-check-label ms-2 fw-bold">{{ $isGlobalEnabled ? 'Enabled' : 'Disabled' }}</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- HR Staff Setting Card -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold mb-2 text-warning">HR Staff Access</h5>
                        <p class="text-muted small">Require MFA for HR Staff logins.</p>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleHrStaff" {{ $isHrStaffEnabled ? 'checked' : '' }} {{ !$isGlobalEnabled ? 'disabled' : '' }} style="width: 3em; height: 1.5em;">
                        <label class="form-check-label ms-2 fw-bold">{{ $isHrStaffEnabled ? 'Required' : 'Optional' }}</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- HR Manager Setting Card -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title fw-bold mb-2 text-primary">HR Manager Access</h5>
                         <p class="text-muted small">Require MFA for HR Manager logins.</p>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleHrManager" {{ $isHrManagerEnabled ? 'checked' : '' }} {{ !$isGlobalEnabled ? 'disabled' : '' }} style="width: 3em; height: 1.5em;">
                         <label class="form-check-label ms-2 fw-bold">{{ $isHrManagerEnabled ? 'Required' : 'Optional' }}</label>
                    </div>
                </div>
            </div>
        </div>
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
                            <p class="text-uppercase text-muted small fw-bold mb-1">Failed MFA Attempts</p>
                            <h2 class="fw-bold text-danger mb-0">{{ $stats['total_failed'] }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-shield-x text-danger fs-3"></i>
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
                            <p class="text-uppercase text-muted small fw-bold mb-1">Total Login Attempts</p>
                            <h2 class="fw-bold text-info mb-0">{{ $stats['login_attempts'] }}</h2>
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
        <div class="card-header bg-white py-3">
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
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
