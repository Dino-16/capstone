<div @class('login-container')>
    <div @class('row g-0')>
        <!-- Left Side - Welcome -->
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

        <!-- Right Side - Login Form -->
        <div @class('col-lg-6 login-right')>
            <h3 @class('text-center mb-4') style="color: var(--jetlouge-primary); font-weight: 700;">
                Sign In to Your Account
            </h3>

            <x-input-error @class('text-center') :field="('email')" />
            
            {{-- Session Expired Notification --}}
            @if(session()->has('session_expired'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-clock-history me-2"></i>
                    <strong>Session Expired!</strong> {{ session('session_expired') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session()->has('test'))
                <div class="alert alert-info">
                    {{ session('test') }}
                </div>
            @endif
            
            <div>

                <form wire:submit.prevent="login" id="loginForm" >
                    <x-honeypot />
                    <div @class('mb-3')>
                        <label for="email-name" @class('form-label fw-semibold')>Email</label>
                        <div @class('input-group')>
                            <span @class('input-group-text')>
                                <i @class('bi bi-envelope')></i>
                            </span>
                            <input wire:model="email" type="email" @class('form-control') id="email-name" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div @class('mb-3')>
                        <label for="password" @class('form-label fw-semibold')>Password</label>
                        <div @class('input-group')>
                            <span @class('input-group-text')>
                                <i @class('bi bi-lock')></i>
                            </span>
                            <input wire:model="password" type="password" @class('form-control') id="password" placeholder="Enter your password" required>
                            <button @class('btn btn-outline-secondary') type="button" id="togglePassword">
                                <i @class('bi bi-eye') id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div @class('mb-3 form-check d-flex justify-content-between')>
                        <span>
                            <input type="checkbox" @class('form-check-input') id="rememberMe">
                            <label @class('form-check-label') for="rememberMe">
                                Remember me
                            </label>
                        </span>
                        <a href="#">Register</a>
                    </div>

                    <button type="submit" @class('btn btn-login mb-3')>
                        <i @class('bi bi-box-arrow-in-right me-2')></i>
                        Sign In
                    </button>

                    <hr @class('my-4')>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const passwordInput = document.getElementById('password');
                        const togglePassword = document.getElementById('togglePassword');
                        const eyeIcon = document.getElementById('eyeIcon');

                        togglePassword.addEventListener('click', function () {
                            const type = passwordInput.type === 'password' ? 'text' : 'password';
                            passwordInput.type = type;
                            eyeIcon.classList.toggle('bi-eye');
                            eyeIcon.classList.toggle('bi-eye-slash');
                        });
                    });
                </script>
            </div>

        </div>
    </div>
</div>