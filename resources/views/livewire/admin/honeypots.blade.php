<div class="container-fluid p-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 text-gray-800">Honeypot Security Management</h2>
            <p class="text-muted">A honeypot is a security mechanism that creates a "trap" for automated bots. It adds a hidden field to your forms that human users won't see, but bots (which usually fill out every field) will incorrectly fill out, allowing the system to block them.</p>
        </div>
    </div>

    {{-- Alert Messages --}}
    {{-- Toast --}}
    <x-toast />

    {{-- Settings Card --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="card-title fw-bold mb-1">Global Enable</h5>
                            <p class="text-muted small mb-0">Toggle honeypot protection for all integrated forms.</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleHoneypot" {{ $isEnabled ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Trap Field Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-eye-slash"></i></span>
                            <input type="text" class="form-control" wire:model.blur="fieldName" placeholder="e.g. secondary_email">
                            <button class="btn btn-outline-primary" wire:click="updateFieldName">Update</button>
                        </div>
                        <small class="text-muted">Changes the `name` attribute of the hidden input. Use a realistic sounding name (e.g., `fax`, `website`, `secondary_email`) to trick bots.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="col-md-6">
            <div class="row g-3 h-100">
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-danger border-5">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <p class="text-uppercase text-muted small fw-bold mb-1">Bots Trapped (Total)</p>
                            <h2 class="fw-bold text-danger mb-0">{{ $stats['total_trapped'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm h-100 border-start border-warning border-5">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <p class="text-uppercase text-muted small fw-bold mb-1">Trapped (Last 7 Days)</p>
                            <h2 class="fw-bold text-warning mb-0">{{ $stats['recent_traps'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Logs Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">Trap Logs</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Form</th>
                            <th>IP Address</th>
                            <th>Payload (Trap Value)</th>
                            <th>User Agent</th>
                            <th>Time</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $log->form_name }}</td>
                                <td class="font-monospace small">{{ $log->ip_address }}</td>
                                <td class="small text-danger bg-light p-1 rounded">
                                    {{ json_decode($log->payload)->honeypot_value ?? 'N/A' }}
                                </td>
                                <td class="small text-muted" style="max-width: 250px;">
                                    <div class="text-truncate" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </div>
                                </td>
                                <td class="small">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                                <td class="text-end pe-4">
                                    <button wire:click="deleteLog({{ $log->id }})" 
                                            wire:confirm="Are you sure you want to delete this log?"
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-bug display-6 d-block mb-3"></i>
                                    No bots caught yet.
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
