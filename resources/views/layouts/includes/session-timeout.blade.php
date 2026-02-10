{{-- Session Timeout Handler --}}
{{-- Include this partial in layouts that need session timeout functionality --}}

@if(session()->has('user') && session('user.authenticated'))
<style>
    /* Session Timeout Modal Styles */
    .session-timeout-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(4px);
    }

    .session-timeout-overlay.show {
        display: flex;
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .session-timeout-modal {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 32px;
        max-width: 420px;
        width: 90%;
        text-align: center;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        transform: scale(0.9);
        animation: modalSlideIn 0.3s ease-out forwards;
    }

    @keyframes modalSlideIn {
        to { transform: scale(1); }
    }

    .session-timeout-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .session-timeout-icon svg {
        width: 40px;
        height: 40px;
        color: white;
    }

    .session-timeout-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3436;
        margin-bottom: 10px;
    }

    .session-timeout-message {
        color: #636e72;
        font-size: 0.95rem;
        margin-bottom: 24px;
        line-height: 1.6;
    }

    .session-timeout-timer {
        font-size: 3rem;
        font-weight: 700;
        color: #ee5253;
        margin-bottom: 24px;
        font-family: 'Roboto Mono', monospace;
    }

    .session-timeout-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .session-timeout-btn {
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }

    .session-timeout-btn-primary {
        background: linear-gradient(135deg, #0984e3 0%, #74b9ff 100%);
        color: white;
    }

    .session-timeout-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(9, 132, 227, 0.4);
    }

    .session-timeout-btn-secondary {
        background: #f1f2f6;
        color: #636e72;
    }

    .session-timeout-btn-secondary:hover {
        background: #dfe6e9;
    }

    .session-timeout-progress {
        width: 100%;
        height: 4px;
        background: #dfe6e9;
        border-radius: 4px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .session-timeout-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #ee5253 0%, #ff6b6b 100%);
        transition: width 1s linear;
    }
</style>

<!-- Session Timeout Warning Modal -->
<div id="sessionTimeoutOverlay" class="session-timeout-overlay">
    <div class="session-timeout-modal">
        <div class="session-timeout-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h3 class="session-timeout-title">Session Expiring Soon</h3>
        <p class="session-timeout-message">
            Your session is about to expire due to inactivity. Click "Stay Logged In" to continue working.
        </p>
        <div class="session-timeout-progress">
            <div id="sessionTimeoutProgressBar" class="session-timeout-progress-bar" style="width: 100%;"></div>
        </div>
        <div class="session-timeout-timer">
            <span id="sessionTimeoutCountdown">60</span>
        </div>
        <div class="session-timeout-buttons">
            <button type="button" class="session-timeout-btn session-timeout-btn-secondary" onclick="SessionTimeout.logout()">
                Log Out
            </button>
            <button type="button" class="session-timeout-btn session-timeout-btn-primary" onclick="SessionTimeout.stayLoggedIn()">
                Stay Logged In
            </button>
        </div>
    </div>
</div>

<script>
const SessionTimeout = {
    // Configuration - 3 minutes (180 seconds) total, warn 60 seconds before
    TIMEOUT_DURATION: 180, // 3 minutes in seconds
    WARNING_BEFORE: 60, // Show warning 60 seconds before timeout
    
    // State
    idleTime: 0,
    warningShown: false,
    countdownInterval: null,
    idleInterval: null,
    
    // Initialize the session timeout handler
    init: function() {
        // Reset idle time on any user activity
        const events = ['mousemove', 'mousedown', 'keypress', 'keydown', 'scroll', 'touchstart', 'click'];
        events.forEach(event => {
            document.addEventListener(event, () => this.resetIdleTime(), { passive: true });
        });
        
        // Check idle time every second
        this.idleInterval = setInterval(() => this.checkIdleTime(), 1000);
        
        console.log('[SessionTimeout] Initialized - Timeout: ' + this.TIMEOUT_DURATION + 's, Warning at: ' + (this.TIMEOUT_DURATION - this.WARNING_BEFORE) + 's');
    },
    
    // Reset idle time counter
    resetIdleTime: function() {
        if (!this.warningShown) {
            this.idleTime = 0;
        }
    },
    
    // Check idle time and show warning if needed
    checkIdleTime: function() {
        this.idleTime++;
        
        const timeUntilTimeout = this.TIMEOUT_DURATION - this.idleTime;
        
        // Show warning when approaching timeout
        if (timeUntilTimeout <= this.WARNING_BEFORE && !this.warningShown) {
            this.showWarning(timeUntilTimeout);
        }
        
        // Auto logout when timeout reached
        if (this.idleTime >= this.TIMEOUT_DURATION) {
            this.logout();
        }
    },
    
    // Show the warning modal
    showWarning: function(secondsRemaining) {
        this.warningShown = true;
        const overlay = document.getElementById('sessionTimeoutOverlay');
        const countdown = document.getElementById('sessionTimeoutCountdown');
        const progressBar = document.getElementById('sessionTimeoutProgressBar');
        
        overlay.classList.add('show');
        
        let remaining = secondsRemaining;
        const initialRemaining = secondsRemaining;
        
        countdown.textContent = remaining;
        
        // Start countdown
        this.countdownInterval = setInterval(() => {
            remaining--;
            countdown.textContent = remaining;
            
            // Update progress bar
            const progressPercent = (remaining / initialRemaining) * 100;
            progressBar.style.width = progressPercent + '%';
            
            if (remaining <= 0) {
                clearInterval(this.countdownInterval);
                this.logout();
            }
        }, 1000);
    },
    
    // Hide the warning modal
    hideWarning: function() {
        const overlay = document.getElementById('sessionTimeoutOverlay');
        overlay.classList.remove('show');
        
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
            this.countdownInterval = null;
        }
        
        this.warningShown = false;
    },
    
    // Stay logged in - refresh session
    stayLoggedIn: function() {
        this.hideWarning();
        this.idleTime = 0;
        
        // Make a request to refresh the session on the server
        fetch('{{ route("profile") }}', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            console.log('[SessionTimeout] Session refreshed');
        }).catch(error => {
            console.error('[SessionTimeout] Failed to refresh session:', error);
        });
    },
    
    // Log out the user
    logout: function() {
        this.hideWarning();
        
        if (this.idleInterval) {
            clearInterval(this.idleInterval);
        }
        
        // Submit logout form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const sessionExpired = document.createElement('input');
        sessionExpired.type = 'hidden';
        sessionExpired.name = 'session_expired';
        sessionExpired.value = '1';
        
        form.appendChild(csrfToken);
        form.appendChild(sessionExpired);
        document.body.appendChild(form);
        form.submit();
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    SessionTimeout.init();
});
</script>
@endif
