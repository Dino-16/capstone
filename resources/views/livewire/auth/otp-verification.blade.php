<div @class('login-container')>
    <div @class('row g-0')>
        <!-- Left Side - Welcome (Reused from Login) -->
        <div @class('col-lg-6 login-left')>
            <div @class('floating-shapes')>
                <div @class('shape')></div>
                <div @class('shape')></div>
                <div @class('shape')></div>
            </div>

            <div @class('logo-container')>
                <div @class('logo-box')>
                    <img src="{{ asset('images/logo.png') }}" alt="Jetlouge Travels">
                </div>
                <h1 @class('brand-text')>Jetlouge Travels</h1>
                <p @class('brand-subtitle')>Employee Portal</p>
            </div>

            <h2 @class('welcome-text')>Welcome Back!</h2>
            <p @class('welcome-subtitle')>
                Access your HR dashboard to manage employee records, 
                streamline recruitment, and support organizational growth.
            </p>

            <ul @class('feature-list')>
                <li>
                    <i @class('bi bi-check')></i>
                    <span>Manage employee profiles & roles</span>
                </li>
                <li>
                    <i @class('bi bi-check')></i>
                    <span>Track job applications & interview schedules</span>
                </li>
                <li>
                    <i @class('bi bi-check')></i>
                    <span>Monitor performance reviews & feedback</span>
                </li>
                <li>
                    <i @class('bi bi-check')></i>
                    <span>Secure access to HR tools & workflows</span>
                </li>
            </ul>
        </div>

        <!-- Right Side - OTP Form -->
        <div @class('col-lg-6 login-right')>
            <h3 @class('text-center mb-4') style="color: var(--jetlouge-primary); font-weight: 700;">
                Verify Your Identity
            </h3>

            @if (session('status'))
                <div class="alert alert-success text-center">
                    {{ session('status') }}
                </div>
            @endif

            @error('otp')
                <div class="alert alert-danger text-center">{{ $message }}</div>
            @enderror
            
            <div class="mb-4 text-center">
                <p class="text-secondary">
                    We have sent a verification code to <br>
                    <strong>{{ session('user_email') ?? 'your email' }}</strong>
                </p>
                <p class="small text-muted">Enter the 6-digit code below.</p>
            </div>

            <form wire:submit.prevent="verifyOtp">
                <div class="d-flex justify-content-center gap-2 mb-4">
                    @for ($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            maxlength="1"
                            wire:model.lazy="otpDigits.{{ $i }}"
                            class="form-control text-center fs-4 fw-bold border rounded shadow-sm"
                            style="width: 3rem; height: 3rem;"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                        />
                    @endfor
                </div>

                @error('otpDigits.*')
                    <div class="text-danger text-center mb-2">{{ $message }}</div>
                @enderror

                <button type="submit" @class('btn btn-login mb-3')>
                    <i class="bi bi-shield-check me-2"></i>
                    Verify Code
                </button>
            </form>

            <div class="text-center mt-3">
                <p class="mb-1 text-muted">Didn't receive a code?</p>
                <button wire:click="resendOtp" class="btn btn-link text-decoration-none fw-bold" style="color: var(--jetlouge-primary);">
                    Resend Code
                </button>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-muted text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('input[maxlength="1"]');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            // Optional: Handle backspace to move query backwards
             input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    });
</script>