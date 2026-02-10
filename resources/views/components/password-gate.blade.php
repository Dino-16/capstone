{{-- Password Verification Gate --}}
@if(!$isPasswordVerified)
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
         style="background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 9999;">
        <div class="card shadow-lg border-0" style="max-width: 450px; width: 100%;">
            <div class="card-header bg-primary text-white text-center py-4">
                <i class="bi bi-shield-lock-fill fs-1 mb-2 d-block"></i>
                <h4 class="mb-1 fw-bold">Data Privacy Protection</h4>
                <small class="opacity-75">This page contains sensitive information</small>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                        <div>
                            <strong>Security Verification Required</strong>
                            <p class="mb-0 small">Please enter your password to verify your identity and access this protected content.</p>
                        </div>
                    </div>
                </div>

                @if($verificationError)
                    <div class="alert alert-danger border-0 d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ $verificationError }}
                    </div>
                @endif

                <form wire:submit="verifyPassword">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-person-fill me-1"></i>
                            Logged in as
                        </label>
                        <input type="text" class="form-control bg-light" value="{{ session('user.email') }}" disabled readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-key-fill me-1"></i>
                            Enter Your Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input 
                                type="password" 
                                class="form-control form-control-lg" 
                                wire:model="verificationPassword"
                                placeholder="Enter your password"
                                autofocus
                                required
                            >
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="verifyPassword">
                                <i class="bi bi-unlock-fill me-2"></i>Verify & Continue
                            </span>
                            <span wire:loading wire:target="verifyPassword">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Verifying...
                            </span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="cancelVerification">
                            <i class="bi bi-arrow-left me-2"></i>Go Back to Dashboard
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light text-center py-3">
                <small class="text-muted">
                    <i class="bi bi-shield-check me-1"></i>
                    Your session will remain verified for 3 minutes
                </small>
            </div>
        </div>
    </div>
@endif
