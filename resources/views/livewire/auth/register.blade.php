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

            <div>
                <form wire:submit.prevent="register" id="registerForm">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Name</label>
                        <input wire:model="name" type="text" class="form-control" id="name" placeholder="Enter your name" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input wire:model="email" type="email" class="form-control" id="email" placeholder="Enter your email" required>
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input wire:model="password" type="password" class="form-control" id="password" placeholder="Create a password" required>
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                        <input wire:model="password_confirmation" type="password" class="form-control" id="password_confirmation" placeholder="Confirm your password" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Role</label>
                        <select wire:model="role" class="form-select" id="role" required>
                            <option>Select a role</option>
                            <option value="admin">Admin</option>
                            <option value="employee">Employee</option>
                            <option value="manager">Manager</option>
                        </select>
                        @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    
                    <div @class('mb-3 form-check d-flex justify-content-between')>
                        <span>
                            <input type="checkbox" @class('form-check-input') id="rememberMe">
                            <label @class('form-check-label') for="rememberMe">
                                Remember me
                            </label>
                        </span>
                        <a href="{{ route('login') }}">Login</a>
                    </div>

                    <button type="submit" @class('btn btn-login mb-3')>
                        <i @class('bi bi-box-arrow-in-right me-2')></i>
                        Sign Up
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
