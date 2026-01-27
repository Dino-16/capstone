{{-- Navbar --}}
<nav @class('navbar navbar-expand-lg navbar-dark fixed-top') style="background-color: var(--jetlouge-primary);">
    <div @class('container-fluid')>
        <button id="desktop-toggle" title="Toggle Sidebar" @class('sidebar-toggle desktop-toggle me-3')>
            <i @class('bi bi-list fs-5')></i>
        </button>

        @php
            $pendingRequisitionsCount = \App\Models\Recruitment\Requisition::where('status', 'Pending')->count();
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
                    @if($pendingRequisitionsCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem;">
                            {{ $pendingRequisitionsCount }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if($pendingRequisitionsCount > 0)
                        <li>
                            <a class="dropdown-item" href="{{ route('recruitment-requests') }}">
                                There is {{ $pendingRequisitionsCount }} pending request{{ $pendingRequisitionsCount > 1 ? 's' : '' }}
                            </a>
                        </li>
                    @else
                        <li><span class="dropdown-item-text text-muted">No New Notifications</span></li>
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