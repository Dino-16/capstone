{{-- Sidebar --}}
<aside id="sidebar" @class('bg-white border-end p-3 shadow-sm')>

    {{-- Profile Section --}}
    <div @class('profile-section text-center')>
        @php
            $user = session('user');
            $firstName = $user['name'] ?? 'Guest'; 
            $userName = $user['name'] ?? 'Guest';
            $userPosition = $user['position'] ?? 'Employee'; 
            $avatarInitial = $userName !== 'Guest' ? urlencode(substr($userName, 0, 1)) : 'G';
        @endphp
        <img src="https://ui-avatars.com/api/?name={{ $avatarInitial }}&size=150&background=0d6efd&color=fff"
            alt="Admin Profile" class="profile-img mb-2">
        <h6 @class('fw-semibold mb-1')>{{ $userName }}</h6>
        <small @class('text-muted')>{{ $userPosition }}</small>
    </div>
   
    {{-- Navigation Menu --}}
    <ul @class('nav flex-column')>
        <li @class("nav-item")>
            @php
                $position = session('user.position');
                $dashboardRoute = 'dashboard';
                $isSuperAdmin = ($position === 'Super Admin');
                
                if ($isSuperAdmin) $dashboardRoute = 'superadmin.dashboard';
                elseif ($position === 'HR Manager') $dashboardRoute = 'admin.dashboard';
            @endphp
            <a href="{{ route($dashboardRoute) }}"
                @class('nav-link text-dark ' . (request()->is('dashboard') || request()->is('admin/dashboard') || request()->is('admin/superadmin/dashboard') ? 'active' : ''))>
                <i @class('bi bi-grid me-2')></i> Dashboard
            </a>
        </li>

        <li @class("nav-item")>
            <a href="#recruitmentMenu"
            role="button"
            aria-expanded="{{ (request()->is('recruitment-requests') || request()->is('job-postings')) ? 'true' : 'false' }}"
            aria-controls="recruitmentMenu"
            data-bs-toggle="collapse"
            @class("nav-link text-dark d-flex justify-content-between align-items-center")>
                <span><i @class('bi bi-briefcase-fill me-2')></i> Recruitment</span>
                <i @class('bi bi-chevron-down small')></i>
            </a>
            <div id="recruitmentMenu" @class('collapse ps-4 ' . ((request()->is('recruitment-requests') || request()->is('job-postings')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class("nav-item")>
                        <a href="{{ route('recruitment-requests') }}"
                            @class('nav-link text-dark' . (request()->is('recruitment-requests') ? 'active' : ''))>
                            <i @class('bi bi-file-earmark-plus me-2')></i> Recruitment Requests
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('job-postings') }}"
                            @class('nav-link text-dark' . (request()->is('job-postings') ? 'active' : ''))>
                            <i @class('bi bi-megaphone me-2')></i> Job Posting
                        </a>
                    </li>
                </ul>
            </div>
        </li>

          

        <li @class("nav-item")>
            <a href="#applicantsMenu"
            role="button"
            aria-expanded="{{ (request()->is('applications') || request()->is('candidates') || request()->is('interviews') || request()->is('offers')) ? 'true' : 'false' }}"
            aria-controls="applicantsMenu"
            data-bs-toggle="collapse"
            @class("nav-link text-dark d-flex justify-content-between align-items-center")>
                <span><i @class('bi bi-people-fill me-2')></i> Applicants</span>
                <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="applicantsMenu" @class('collapse ps-4 ' . ((request()->is('applications') || request()->is('candidates') || request()->is('interviews') || request()->is('offers')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class("nav-item")>
                        <a href="{{ route('applications') }}"
                            @class('nav-link text-dark' . (request()->is('applications') ? 'active' : ''))>
                            <i @class('bi bi-journal-text me-2')></i> Applications
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('candidates') }}"
                            @class('nav-link text-dark' . (request()->is('candidates') ? 'active' : ''))>
                            <i @class('bi bi-person-check me-2')></i> Candidates
                        </a>
                    </li>

                    
        <li @class("nav-item")>
            <a href="{{ route('facility-request') }}" @class('nav-link text-dark'. (request()->is('facility-request') ? 'active' : ''))>
                <i @class('bi bi-building me-2')></i> Facility Request
            </a>
        </li>

                    <li @class("nav-item")>
                        <a href="{{ route('interviews') }}"
                        @class('nav-link text-dark' . (request()->is('interviews') ? 'active' : ''))>
                            <i @class('bi bi-calendar-event me-2')></i> Interviews
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('offers') }}"
                        @class('nav-link text-dark' . (request()->is('offers') ? 'active' : ''))>
                            <i @class('bi bi-file-earmark-check me-2')></i> Offers
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li @class("nav-item")>
            <a href="#onboardingMenu"
            role="button"
            aria-expanded="{{ (request()->is('employees') || request()->is('document-checklist') || request()->is('orientation-plan')) ? 'true' : 'false' }}"
            aria-controls="onboardingMenu"
            data-bs-toggle="collapse"
            @class("nav-link text-dark d-flex justify-content-between align-items-center")>
                <span><i @class('bi bi-person-badge-fill me-2')></i> Onboarding</span>
                <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="onboardingMenu" @class('collapse ps-4 ' . ((request()->is('employees') || request()->is('document-checklist') || request()->is('orientation-plan')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class("nav-item")>
                        <a href="{{ route('document-checklists') }}"
                            @class('nav-link text-dark' . (request()->is('document-checklists') ? 'active' : ''))>
                            <i @class('bi bi-check2-square me-2')></i> Document Checklist
                        </a>
                    </li>

                    <li @class("nav-item")>
                        <a href="{{ route('employees') }}"
                            @class('nav-link text-dark' . (request()->is('employees') ? 'active' : ''))>
                            <i @class('bi bi-person-plus-fill me-2')></i> Employees
                        </a>
                    </li>

                    <li @class("nav-item")>
                        <a href="{{ route('orientation-schedule') }}"
                            @class('nav-link text-dark'. (request()->is('orientation-schedule') ? 'active' : ''))>
                            <i @class('bi bi-calendar-week me-2')></i> Orientation Schedule
                        </a>
                    </li>
                </ul>
            </div>
        </li>

       
  
        <li @class("nav-item")>
            <a href="#performanceMenu"
            role="button"
            aria-expanded="{{ (request()->is('new-hire-reviews') || request()->is('evaluations') || request()->is('evaluations')) ? 'true' : 'false' }}"
            aria-controls="performanceMenu"
            data-bs-toggle="collapse"
            @class("nav-link text-dark d-flex justify-content-between align-items-center")>
                <span><i @class('bi bi-bar-chart-fill me-2')></i> Performance</span>
                <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="performanceMenu" @class('collapse ps-4 ' . ((request()->is('new-hire-reviews') || request()->is('evaluations') || request()->is('evaluation')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class("nav-item")>
                        <a href="{{ route('evaluations') }}"
                            @class('nav-link text-dark' . (request()->is('evaluations') ? 'active' : ''))>
                            <i @class('bi bi-clipboard-data-fill me-2')></i> Evaluations
                        </a>
                    </li>
                    
                    <li @class("nav-item")>
                        <a href="{{ route('tracker') }}"
                            @class('nav-link text-dark' . (request()->is('tracker') ? 'active' : ''))>
                            <i @class('bi bi-graph-up me-2')></i> Performance Tracker
                        </a>
                    </li>
            
                    <li @class("nav-item")>
                        <a href="{{ route('evaluation-records') }}"
                            @class('nav-link text-dark' . (request()->is('evaluation-records') ? 'active' : ''))>
                            <i @class('bi bi-file-earmark-bar-graph me-2')></i> Evaluation Records
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li @class("nav-item")>
            <a href="#rewardsMenu"
            role="button"
            aria-expanded="{{ (request()->is('rewards') || request()->is('reward-giving')) ? 'true' : 'false' }}"
            aria-controls="rewardsMenu"
            data-bs-toggle="collapse"
            @class("nav-link text-dark d-flex justify-content-between align-items-center")>
                <span><i @class('bi bi-gift me-2')></i> Recognition</span>
                <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="rewardsMenu" @class('collapse ps-4 ' . ((request()->is('rewards') || request()->is('reward-giving')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class("nav-item")>
                        <a href="{{ route('rewards') }}"
                        @class('nav-link text-dark'. (request()->is('rewards') ? 'active' : ''))>
                            <i @class('bi bi-gift me-2')></i> Rewards
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('reward-giving') }}" @class('nav-link text-dark'. (request()->is('reward-giving') ? 'active' : ''))>
                            <i @class('bi bi-heart-fill me-2')></i> Give Rewards
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li @class("nav-item")>
            <a href="{{ route('reports') }}" @class('nav-link text-dark'. (request()->is('reports') ? 'active' : ''))>
                <i @class('bi bi-file-earmark-text me-2')></i> Reports Data
            </a>
        </li>

        {{-- Security Menu (Super Admin only) --}}
        @if(session('user.position') === 'Super Admin')
        <li @class("nav-item")>
            <a href="{{ route('superadmin.tickets.index') }}" @class('nav-link text-dark' . (request()->is('admin/superadmin/tickets') ? 'active' : ''))>
                <i class="bi bi-ticket-perforated me-2"></i> Ticket Requests
            </a>
        </li>
        <li @class("nav-item")>
            <a href="#securityMenu"
            role="button"
            aria-expanded="{{ (request()->is('admin/superadmin/recaptcha') || request()->is('admin/superadmin/mfa') || request()->is('admin/superadmin/honeypots')) ? 'true' : 'false' }}"
            aria-controls="securityMenu"
            data-bs-toggle="collapse"
            @class("nav-link text-dark d-flex justify-content-between align-items-center")>
                <span><i @class('bi bi-shield-lock me-2')></i> Security Settings</span>
                <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="securityMenu" @class('collapse ps-4 ' . ((request()->is('admin/superadmin/recaptcha') || request()->is('admin/superadmin/mfa') || request()->is('admin/superadmin/honeypots')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class("nav-item")>
                        <a href="{{ route('superadmin.recaptcha') }}"
                            @class('nav-link text-dark' . (request()->is('admin/superadmin/recaptcha') ? 'active' : ''))>
                            <i @class('bi bi-robot me-2')></i> Recaptcha
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('superadmin.mfa') }}"
                            @class('nav-link text-dark' . (request()->is('admin/superadmin/mfa') ? 'active' : ''))>
                            <i @class('bi bi-key-fill me-2')></i> MFA Settings
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('superadmin.honeypots') }}"
                            @class('nav-link text-dark' . (request()->is('admin/superadmin/honeypots') ? 'active' : ''))>
                            <i @class('bi bi-bug-fill me-2')></i> Honeypots
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        {{-- HR Manager Support Ticket Link --}}
        @if(session('user.position') === 'HR Manager')
        <li class="nav-item">
            <a href="{{ route('admin.tickets.index') }}" @class('nav-link text-dark' . (request()->is('admin/support/*') ? 'active' : ''))>
                <i class="bi bi-life-preserver me-2"></i> Support Tickets
            </a>
        </li>
        @endif

        <hr>

        <livewire:auth.logout />
    </ul>

</aside>
