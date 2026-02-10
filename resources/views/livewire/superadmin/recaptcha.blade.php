@section('page-title', 'RECAPTCHA')
@section('page-subtitle', 'Manage bot protection and verification settings')
@section('breadcrumbs', 'RECAPTCHA')

<div class="container-fluid p-4">
    <div class="row mb-4">
        {{-- Header is handled by layout now --}}
    </div>

    {{-- Alert Messages --}}
    {{-- Toast --}}
    <x-toast />

    {{-- Settings Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title fw-bold mb-1">Enable Verification</h5>
                <p class="text-muted mb-0 small">Toggle to enable or disable reCAPTCHA on the application form.</p>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="recaptchaToggle" 
                       wire:click="toggleRecaptcha" {{ $isEnabled ? 'checked' : '' }} 
                       style="width: 3em; height: 1.5em;">
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
                            <p class="text-uppercase text-muted small fw-bold mb-1">Successful Verifications</p>
                            <h2 class="fw-bold text-success mb-0">{{ $stats['total_success'] }}</h2>
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
                            <p class="text-uppercase text-muted small fw-bold mb-1">Failed Attempts</p>
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
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-uppercase text-muted small fw-bold mb-1">Recent Activity (7 Days)</p>
                            <h2 class="fw-bold text-primary mb-0">{{ $stats['recent_attempts'] }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="bi bi-activity text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Verification Logs</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Status</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Time</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4">
                                    @if($log->status === 'success')
                                        <span class="badge bg-success rounded-pill px-3">Success</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3">Failed</span>
                                    @endif
                                </td>
                                <td class="font-monospace small">{{ $log->ip_address }}</td>
                                <td class="small text-muted" style="max-width: 300px;">
                                    <div class="text-truncate" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </div>
                                </td>
                                <td class="small">{{ $log->created_at->format('M d, Y h:i A') }}</td>
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
                                    <i class="bi bi-clipboard-data display-6 d-block mb-3"></i>
                                    No logs found yet.
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


