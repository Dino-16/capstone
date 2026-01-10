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
                <x-text-input
                    type="search"
                    wire:model.live.debounce.3s="search"
                    placeholder="Search..."
                />
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                <button
                    @class('btn btn-success')
                    wire:click="export"
                >
                    Export to Excel
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
                            </div>
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
                            <span @class('badge {{ $orientation->status_badge }}')>
                                {{ ucfirst($orientation->status) }}
                            </span>
                        </td>
                        <td @class('gap-3')>
                            <button
                                @class('btn btn-primary btn-sm me-2')
                                wire:click="editOrientation({{ $orientation->id }})"
                                title="Edit"
                            >
                                <i @class('bi bi-pencil')></i>
                            </button>
                            <button
                                @class('btn btn-info btn-sm me-2')
                                wire:click="openMessageModal({{ $orientation->id }})"
                                title="Message"
                            >
                                <i @class('bi bi-envelope')></i>
                            </button>
                            <button
                                @class('btn btn-danger btn-sm')
                                wire:click="deleteOrientation({{ $orientation->id }})"
                                wire:confirm="Are you sure you want to delete this orientation?"
                                title="Delete"
                            >
                                <i @class('bi bi-trash')></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" @class('text-center text-muted')>No orientations scheduled</td>
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

    {{-- Add Orientation Modal --}}
    @include('livewire.user.onboarding.includes.orientation-add')

    {{-- Edit Orientation Modal --}}
    @include('livewire.user.onboarding.includes.orientation-edit')

    {{-- Message Employee Modal --}}
    @include('livewire.user.onboarding.includes.orientation-message')
</div>
