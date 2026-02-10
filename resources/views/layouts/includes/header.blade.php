{{-- Navbar --}}
<nav @class('navbar navbar-expand-lg navbar-dark fixed-top') style="background-color: var(--jetlouge-primary);">
    <div @class('container-fluid')>
        <button id="desktop-toggle" title="Toggle Sidebar" @class('sidebar-toggle desktop-toggle me-3')>
            <i @class('bi bi-list fs-5')></i>
        </button>

        @php
            $isSuperAdmin = session('user.position') === 'Super Admin';
            
            $pendingRequisitionsCount = !$isSuperAdmin ? \App\Models\Recruitment\Requisition::where('status', 'Pending')->count() : 0;
            $pendingEvaluationsCount = !$isSuperAdmin ? \App\Services\PerformanceTrackerService::getPendingEvaluationsCount() : 0;
            $expiringJobsCount = !$isSuperAdmin ? \App\Models\Recruitment\JobListing::where('status', 'Active')
                ->where('expiration_date', '<=', now()->addDays(3))
                ->count() : 0;
            
            $pendingTicketsCount = 0;
            if($isSuperAdmin || session('user.position') === 'HR Manager') {
                $pendingTicketsCount = \App\Models\SupportTicket::where('status', 'Pending')->count();
            }

            $isHR = in_array(session('user.position'), ['HR Staff', 'HR Manager', 'HR']);
            
            $pendingDocumentsCount = $isHR ? \App\Models\Onboarding\DocumentChecklist::where('status', 'active')->get()->filter(function($doc) {
                return $doc->getCompletionPercentage() < 100;
            })->count() : 0;

            $eligibleRewardsCount = $isHR ? \App\Models\Performance\Evaluation::where('status', 'Completed')
                ->whereMonth('evaluation_date', now()->month)
                ->whereYear('evaluation_date', now()->year)
                ->where('overall_score', '>=', 4)
                ->count() : 0;

            $totalNotifications = $pendingRequisitionsCount + $pendingEvaluationsCount + $expiringJobsCount + $pendingTicketsCount + $pendingDocumentsCount + $eligibleRewardsCount;
            
            $user = session('user');
            $userName = $user['name'] ?? 'Guest';
            $avatarInitial = $userName !== 'Guest' ? substr($userName, 0, 1) : 'G';
        @endphp

        <a href="#" @class('navbar-brand fw-bold position-absolute start-50 top-50 translate-middle')>
            <i @class('bi bi-airplane me-2')></i>Jetlouge Travels
        </a>

        <div @class('d-flex align-items-center ms-auto')>
            <div class="dropdown me-3">
                <a href="#" class="text-white position-relative" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-5"></i>
                    @if($totalNotifications > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
                            {{ $totalNotifications }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width: 250px;">
                    @if($totalNotifications > 0)
                        <li class="dropdown-header fw-bold border-bottom">Notifications</li>
                        @if($pendingRequisitionsCount > 0)
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('recruitment-requests') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-bold">Recruitment Request</div>
                                            <div class="small text-muted">{{ $pendingRequisitionsCount }} request{{ $pendingRequisitionsCount > 1 ? 's' : '' }} pending</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        @if($pendingEvaluationsCount > 0)
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('tracker') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                            <i class="bi bi-clipboard-check"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-bold">Performance Evaluation</div>
                                            <div class="small text-muted">{{ $pendingEvaluationsCount }} evaluation{{ $pendingEvaluationsCount > 1 ? 's' : '' }} due/overdue</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        @if($expiringJobsCount > 0)
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('job-postings') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3">
                                            <i class="bi bi-calendar-x"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-bold">Job Expiration</div>
                                            <div class="small text-muted">{{ $expiringJobsCount }} active job{{ $expiringJobsCount > 1 ? 's' : '' }} expiring soon</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        @if($pendingTicketsCount > 0)
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('superadmin.tickets.index') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                            <i class="bi bi-ticket-detailed"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-bold">Support Tickets</div>
                                            <div class="small text-muted">{{ $pendingTicketsCount }} ticket{{ $pendingTicketsCount > 1 ? 's' : '' }} pending</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        @if($pendingDocumentsCount > 0)
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('document-checklists') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                            <i class="bi bi-file-earmark-check"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-bold">Pending HR Documents</div>
                                            <div class="small text-muted">{{ $pendingDocumentsCount }} checklist{{ $pendingDocumentsCount > 1 ? 's' : '' }} incomplete</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                        @if($eligibleRewardsCount > 0)
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('rewards') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                            <i class="bi bi-stars"></i>
                                        </div>
                                        <div>
                                            <div class="small fw-bold">Reward Eligibility</div>
                                            <div class="small text-muted">{{ $eligibleRewardsCount }} employee{{ $eligibleRewardsCount > 1 ? 's' : '' }} recognized for high performance</div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endif
                    @else
                        <li><span class="dropdown-item-text text-center text-muted py-3">No New Notifications</span></li>
                    @endif
                </ul>
            </div>

            <div class="dropdown me-3">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-light text-primary d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px; font-weight: bold;">
                        {{ $avatarInitial }}
                    </div>
                    <span class="d-none d-md-block">{{ $userName }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile') }}">Profile</a></li>
                    <li>
                        <livewire:auth.logout :is-dropdown="true" />
                    </li>
                </ul>
            </div>

            <button id="menu-btn" title="Open Menu" @class('sidebar-toggle mobile-toggle')>
                <i @class('bi bi-list fs-5')></i>
            </button>
        </div>
    </div>
</nav>