@section('page-title', 'Orientation Schedule')
@section('page-subtitle', 'Manage employee orientations')
@section('breadcrumbs', 'Orientation Schedule')

<div @class('pt-2')>

    {{-- Toast --}}
    <x-toast />
    
    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- Search Bar --}}
            <div>
                <x-search-input
                    wire:model.live.debounce.3s="search"
                    placeholder="Search..."
                />
            </div>

            {{-- Department Filter --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-building me-2')></i>
                    Department: {{ $departmentFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('departmentFilter', '')">All Departments</a>
                    </li>
                    @foreach($departments as $dept)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('departmentFilter', '{{ $dept }}')">{{ $dept }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Position Filter --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-person-badge me-2')></i>
                    Position: {{ $positionFilter ?: 'All' }}
                </button>

                <ul @class('dropdown-menu') style="max-height: 300px; overflow-y: auto;">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('positionFilter', '')">All Positions</a>
                    </li>
                    @foreach($positions as $pos)
                        <li>
                            <a @class('dropdown-item') wire:click="$set('positionFilter', '{{ $pos }}')">{{ $pos }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                <button
                    @class('btn btn-success')
                    wire:click="export"
                >
                    Export
                </button>

                <button
                    @class('btn btn-primary')
                    wire:click="openModal"
                >
                    <i @class('bi bi-plus-circle me-2')></i>
                    Schedule Orientation
                </button>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}

    <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
        <h3 @class('mb-0')>Orientation Schedule</h3>
        <p @class('text-secondary mb-0')>
            Manage employee orientation sessions
        </p>
    </div>
    <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
        <table @class('table')>
            <thead>
            <tr @class('bg-dark')>
                <th @class('text-secondary')>Employee</th>
                <th @class('text-secondary')>Position</th>
                <th @class('text-secondary')>Department</th>
                <th @class('text-secondary')>Date & Time</th>
                <th @class('text-secondary')>Location</th>
                <th @class('text-secondary')>Facilitator</th>
                <th @class('text-secondary')>Status</th>
                <th @class('text-secondary')>Actions</th>
            </tr>
        </thead>
            <tbody>
                @forelse($orientations as $orientation)
                    <tr wire:key="orient-{{ $orientation->id }}">
                        <td>
                            <div @class('d-flex align-items-center')>
                                <div @class('rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-2') style="width: 32px; height: 32px;">
                                    <i @class('bi bi-person text-primary')></i>
                                </div>
                                <div>
                                    <strong>{{ $orientation->employee_name }}</strong>
                                    @if($orientation->email)
                                        <br><small @class('text-muted')>{{ $orientation->email }}</small>
                                    @endif
                            </div>
                        </td>
                        <td>
                            {{ $orientation->position ?? '---' }}
                        </td>
                        <td>
                            {{ $orientation->department ?? '---' }}
                        </td>
                        <td>
                            <strong>{{ $orientation->orientation_date->format('M j, Y') }}</strong>
                            <br><small @class('text-muted')>{{ $orientation->orientation_date->format('g:i A') }}</small>
                        </td>
                        <td>
                            <i @class('bi bi-geo-alt me-1')></i>{{ $orientation->location }}
                        </td>
                        <td>
                            <i @class('bi bi-person-badge me-1')></i>{{ $orientation->facilitator }}
                        </td>
                        <td>
                            <span class="badge {{ $orientation->status_badge }}">
                                {{ ucfirst($orientation->status) }}
                            </span>
                        </td>
                        <td @class('gap-3')>
                            <div class="d-flex gap-2">
                                    @if(session('user.position') === 'HR Manager')
                                    <button
                                        @class('btn btn-sm btn-outline-primary')
                                        wire:click="editOrientation({{ $orientation->id }})"
                                        title="Edit"
                                    >
                                        <i @class('bi bi-pencil')></i>
                                    </button>
                                    @endif
                                <button
                                    @class('btn btn-sm btn-outline-info')
                                    wire:click="openMessageModal({{ $orientation->id }})"
                                    title="Message"
                                >
                                    <i @class('bi bi-envelope')></i>
                                </button>
                                @if(in_array(session('user.position'), ['Super Admin', 'HR Manager']))
                                <button
                                    @class('btn btn-sm btn-outline-danger')
                                    wire:click="confirmDelete({{ $orientation->id }})"
                                    title="Delete"
                                >
                                    <i @class('bi bi-trash')></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" @class('text-center text-muted py-5')>
                            @if($search)
                                <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                <div class="mt-3">No orientations found matching "{{ $search }}".</div>
                            @else
                                <i @class('bi bi-calendar d-block mx-auto fs-1')></i>
                                <div class="mt-3">No orientations scheduled</div>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($orientations->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orientations->links() }}
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this orientation?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteOrientation">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Add Orientation Modal --}}
    @include('livewire.user.onboarding.includes.orientation-add')

    {{-- Edit Orientation Modal --}}
    @include('livewire.user.onboarding.includes.orientation-edit')

    {{-- Message Employee Modal --}}
    @include('livewire.user.onboarding.includes.orientation-message')
</div>
