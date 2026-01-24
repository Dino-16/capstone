{{-- Sidebar --}}
<aside id="sidebar" @class('bg-white border-end p-3 shadow-sm')>

    {{-- Profile Section --}}
    <div @class('profile-section text-center')>
        <img src="https://ui-avatars.com/api/?name={{ urlencode(substr(auth()->user()->name, 0, 1)) }}&size=150&background=0d6efd&color=fff"
            alt="Admin Profile" class="profile-img mb-2">
        <h6 @class('fw-semibold mb-1')>{{ auth()->user()->name }}</h6>
        <small @class('text-muted')>{{ auth()->user()->role }}</small>
    </div>
   
    {{-- Navigation Menu --}}
    <ul @class('nav flex-column')>
        <li @class("nav-item")>
            <a href="{{ route('dashboard') }}"
                @class('nav-link text-dark ' . (request()->is('dashboard') ? 'active' : ''))>
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
            aria-expanded="{{ (request()->is('applications') || request()->is('filtered-resumes') || request()->is('candidates') || request()->is('interviews') || request()->is('request-rooms') || request()->is('offer-acceptance')) ? 'true' : 'false' }}"
            aria-controls="applicantsMenu"
            data-bs-toggle="collapse"
            @class("nav-link text-dark d-flex justify-content-between align-items-center")>
                <span><i @class('bi bi-people-fill me-2')></i> Applicants</span>
                <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="applicantsMenu" @class('collapse ps-4 ' . ((request()->is('applications') || request()->is('filtered-resumes') || request()->is('candidates') || request()->is('interviews') || request()->is('request-rooms') || request()->is('offer-acceptance')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class("nav-item")>
                        <a href="{{ route('applications') }}"
                            @class('nav-link text-dark' . (request()->is('applications') ? 'active' : ''))>
                            <i @class('bi bi-journal-text me-2')></i> Applications
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('interviews') }}"
                        @class('nav-link text-dark' . (request()->is('interviews') ? 'active' : ''))>
                            <i @class('bi bi-calendar-event me-2')></i> Interviews
                        </a>
                    </li>
                </ul>
            </div>
        </li>



   {{--

        <li @class("nav-item")>
            <a href="#applicantsMenu"
            role="button"
            aria-expanded="{{ (request()->is('applications') || request()->is('filtered-resumes') || request()->is('candidates') || request()->is('interviews') || request()->is('request-rooms') || request()->is('offer-acceptance')) ? 'true' : 'false' }}"
            aria-controls="applicantsMenu"
            data-bs-toggle="collapse"
            @class("nav-link text-dark d-flex justify-content-between align-items-center")>
                <span><i @class('bi bi-people-fill me-2')></i> Applicants</span>
                <i @class('bi bi-chevron-down small')></i>
            </a>

            <div id="applicantsMenu" @class('collapse ps-4 ' . ((request()->is('applications') || request()->is('filtered-resumes') || request()->is('candidates') || request()->is('interviews') || request()->is('request-rooms') || request()->is('offer-acceptance')) ? 'show' : ''))>
                <ul @class('nav flex-column')>
                    <li @class("nav-item")>
                        <a href="{{ route('applications') }}"
                            @class('nav-link text-dark' . (request()->is('applications') ? 'active' : ''))>
                            <i @class('bi bi-journal-text me-2')></i> Applications
                        </a>
                    </li>

                    
                    <li @class("nav-item")>
                        <a href="{{ route('filtered-resumes') }}"
                            @class('nav-link text-dark' . (request()->is('filtered-resumes') ? 'active' : ''))>
                            <i @class('bi bi-funnel-fill me-2')></i> Filtered Applicants
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('candidates') }}"
                            @class('nav-link text-dark' . (request()->is('candidates') ? 'active' : ''))>
                            <i @class('bi bi-person-lines-fill me-2')></i> Candidates
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('request-rooms') }}"
                        @class('nav-link text-dark' . (request()->is('request-rooms') ? 'active' : ''))>
                            <i @class('bi bi-door-open me-2')></i> Request Room
                        </a>
                    </li>
                    <li @class("nav-item")>
                        <a href="{{ route('offer-acceptance') }}"
                        @class('nav-link text-dark' . (request()->is('offer-acceptance') ? 'active' : ''))>
                            <i @class('bi bi-hand-thumbs-up me-2')></i> Offer Acceptance
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        --}}

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
                        <a href="{{ route('employees') }}"
                            @class('nav-link text-dark' . (request()->is('employees') ? 'active' : ''))>
                            <i @class('bi bi-person-plus-fill me-2')></i> Employees
                        </a>
                    </li>

                     
                    <li @class("nav-item")>
                        <a href="{{ route('document-checklists') }}"
                            @class('nav-link text-dark' . (request()->is('document-checklists') ? 'active' : ''))>
                            <i @class('bi bi-check2-square me-2')></i> Document Checklist
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
                        <a href="{{ route('criteria') }}"
                        @class('nav-link text-dark'. (request()->is('criteria') ? 'active' : ''))>
                            <i @class('bi bi-gift me-2')></i> Recognition Criteria
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
            <a href="{{ route('reports') }}" @class('nav-link text-dark'. (request()->is('reports') ? 'active' : ''))">
                <i @class('bi bi-file-earmark-text me-2')></i> Reports & Analytics
            </a>
        </li>

        <hr>

        <livewire:auth.logout />
    </ul>

</aside>
