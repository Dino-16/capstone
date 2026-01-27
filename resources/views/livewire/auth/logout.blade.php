<div>
    @if($isDropdown)
        <a class="dropdown-item" role="button" wire:click="logout" style="cursor: pointer;">
            Logout
        </a>
    @else
        <li class="nav-item">
            <a class="nav-link text-dark" role="button" wire:click="logout" style="cursor: pointer;">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    @endif
</div>