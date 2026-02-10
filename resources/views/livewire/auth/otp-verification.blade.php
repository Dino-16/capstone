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
                    <strong>{{ session('otp_session.user_data.email') ?? 'your email' }}</strong>
                </p>
                <p class="small text-muted">Enter the 6-digit code below.</p>
            </div>

            {{-- OTP Timer Section --}}
            <div class="otp-timer-container mb-4">
                <div class="otp-timer-wrapper">
                    <div class="otp-timer-circle" id="otpTimerCircle">
                        <svg class="otp-timer-svg" viewBox="0 0 100 100">
                            <circle class="otp-timer-bg" cx="50" cy="50" r="45"></circle>
                            <circle class="otp-timer-progress" id="otpTimerProgress" cx="50" cy="50" r="45"></circle>
                        </svg>
                        <div class="otp-timer-content">
                            <span class="otp-timer-value" id="otpTimerMinutes">03</span>
                            <span class="otp-timer-separator">:</span>
                            <span class="otp-timer-value" id="otpTimerSeconds">00</span>
                        </div>
                    </div>
                    <div class="otp-timer-label" id="otpTimerLabel">
                        <i class="bi bi-clock-history me-1"></i>
                        Code expires in
                    </div>
                </div>
            </div>

            <style>
                .otp-timer-container {
                    display: flex;
                    justify-content: center;
                }

                .otp-timer-wrapper {
                    text-align: center;
                }

                .otp-timer-circle {
                    position: relative;
                    width: 100px;
                    height: 100px;
                    margin: 0 auto 10px;
                }

                .otp-timer-svg {
                    width: 100%;
                    height: 100%;
                    transform: rotate(-90deg);
                }

                .otp-timer-bg {
                    fill: none;
                    stroke: #e9ecef;
                    stroke-width: 8;
                }

                .otp-timer-progress {
                    fill: none;
                    stroke: url(#timerGradient);
                    stroke-width: 8;
                    stroke-linecap: round;
                    stroke-dasharray: 283;
                    stroke-dashoffset: 0;
                    transition: stroke-dashoffset 1s linear, stroke 0.3s ease;
                }

                .otp-timer-circle.warning .otp-timer-progress {
                    stroke: #f39c12;
                }

                .otp-timer-circle.danger .otp-timer-progress {
                    stroke: #e74c3c;
                    animation: pulse-danger 1s ease-in-out infinite;
                }

                @keyframes pulse-danger {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.6; }
                }

                .otp-timer-content {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    display: flex;
                    align-items: center;
                    font-family: 'Roboto Mono', monospace;
                }

                .otp-timer-value {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #2d3436;
                    min-width: 28px;
                    text-align: center;
                }

                .otp-timer-circle.danger .otp-timer-value {
                    color: #e74c3c;
                }

                .otp-timer-separator {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #636e72;
                    margin: 0 2px;
                }

                .otp-timer-label {
                    font-size: 0.85rem;
                    color: #636e72;
                    font-weight: 500;
                }

                .otp-timer-label.expired {
                    color: #e74c3c;
                    font-weight: 600;
                }

                .otp-timer-expired {
                    color: #e74c3c;
                    font-weight: 600;
                }

                /* Gradient Definition */
                .otp-timer-svg defs {
                    position: absolute;
                }
            </style>

            {{-- SVG Gradient Definition --}}
            <svg style="position: absolute; width: 0; height: 0;">
                <defs>
                    <linearGradient id="timerGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#0984e3" />
                        <stop offset="100%" style="stop-color:#74b9ff" />
                    </linearGradient>
                </defs>
            </svg>

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
        // OTP Input Auto-Focus Logic
        const inputs = document.querySelectorAll('input[maxlength="1"]');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            // Handle backspace to move query backwards
             input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        // OTP Timer Logic
        const OTPTimer = {
            // Server-provided expiration timestamp (Unix timestamp in seconds)
            expiresAt: {{ $otpExpiresAt ?? 0 }},
            totalDuration: 180, // 3 minutes in seconds
            timerInterval: null,

            init: function() {
                if (this.expiresAt === 0) {
                    console.warn('[OTPTimer] No expiration time provided');
                    return;
                }

                this.updateDisplay();
                this.timerInterval = setInterval(() => this.updateDisplay(), 1000);
            },

            getRemainingSeconds: function() {
                const now = Math.floor(Date.now() / 1000);
                const remaining = this.expiresAt - now;
                return Math.max(0, remaining);
            },

            updateDisplay: function() {
                const remaining = this.getRemainingSeconds();
                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;

                // Update time display
                document.getElementById('otpTimerMinutes').textContent = String(minutes).padStart(2, '0');
                document.getElementById('otpTimerSeconds').textContent = String(seconds).padStart(2, '0');

                // Update progress circle
                const circumference = 283; // 2 * PI * 45 (radius)
                const progress = remaining / this.totalDuration;
                const offset = circumference * (1 - progress);
                document.getElementById('otpTimerProgress').style.strokeDashoffset = offset;

                // Update visual states based on time remaining
                const timerCircle = document.getElementById('otpTimerCircle');
                const timerLabel = document.getElementById('otpTimerLabel');

                timerCircle.classList.remove('warning', 'danger');
                timerLabel.classList.remove('expired');

                if (remaining <= 0) {
                    // Timer expired
                    clearInterval(this.timerInterval);
                    timerCircle.classList.add('danger');
                    timerLabel.classList.add('expired');
                    timerLabel.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Code has expired!';
                    
                    // Show expired message and redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 2000);
                } else if (remaining <= 60) {
                    // Less than 1 minute - danger zone
                    timerCircle.classList.add('danger');
                } else if (remaining <= 180) {
                    // Less than 3 minutes - warning zone
                    timerCircle.classList.add('warning');
                }
            }
        };

        // Initialize the OTP timer
        OTPTimer.init();

        // Listen for Livewire events to reset timer on resend
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('message.processed', (message, component) => {
                // Check if the otp_expires was updated (after resend)
                const newExpiresAt = {{ $otpExpiresAt ?? 0 }};
                if (newExpiresAt !== OTPTimer.expiresAt) {
                    OTPTimer.expiresAt = newExpiresAt;
                    clearInterval(OTPTimer.timerInterval);
                    OTPTimer.timerInterval = setInterval(() => OTPTimer.updateDisplay(), 1000);
                    OTPTimer.updateDisplay();
                }
            });
        }
    });
</script>