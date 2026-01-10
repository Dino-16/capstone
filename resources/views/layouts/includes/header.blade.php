{{-- Navbar --}}
<nav @class('navbar navbar-expand-lg navbar-dark fixed-top') style="background-color: var(--jetlouge-primary);">
    <div @class('container-fluid')>
        <button id="desktop-toggle" title="Toggle Sidebar" @class('sidebar-toggle desktop-toggle me-3')>
            <i @class('bi bi-list fs-5')></i>
        </button>

        <a href="#" @class('navbar-brand fw-bold')>
            <i @class('bi bi-airplane me-2')></i>Jetlouge Travels
        </a>

        <div @class('d-flex align-items-center')>
            <button id="menu-btn" title="Open Menu" @class('sidebar-toggle mobile-toggle')>
                <i @class('bi bi-list fs-5')></i>
            </button>
        </div>
    </div>
</nav>