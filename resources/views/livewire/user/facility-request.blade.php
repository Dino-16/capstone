@section('page-title', 'Facility Requests')
@section('page-subtitle', 'Manage facility booking requests')
@section('breadcrumbs', 'Facility Requests')

<div @class('pt-2')>

    {{-- SUCCESS/ERROR TOAST --}}
    {{-- SUCCESS/ERROR TOAST --}}
    <x-toast />

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- STATUS CARDS --}}
    <div @class('row g-3 mb-3')>
        <div @class('col-md-3 col-6')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                <div @class('mb-2')>
                    <i @class('bi bi-collection-fill text-primary fs-3')></i>
                </div>
                <div @class('ps-2')>
                    <div @class('fw-semibold fs-4')>{{ $stats['total'] ?? 0 }}</div>
                    <div @class('text-muted small')>Total Requests</div>
                </div>
            </div>
        </div>
        <div @class('col-md-3 col-6')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                <div @class('mb-2')>
                    <i @class('bi bi-hourglass-split text-warning fs-3')></i>
                </div>
                <div @class('ps-2')>
                    <div @class('fw-semibold fs-4')>{{ $stats['pending'] ?? 0 }}</div>
                    <div @class('text-muted small')>Pending</div>
                </div>
            </div>
        </div>
        <div @class('col-md-3 col-6')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                <div @class('mb-2')>
                    <i @class('bi bi-check-circle-fill text-success fs-3')></i>
                </div>
                <div @class('ps-2')>
                    <div @class('fw-semibold fs-4')>{{ $stats['approved'] ?? 0 }}</div>
                    <div @class('text-muted small')>Approved</div>
                </div>
            </div>
        </div>
        <div @class('col-md-3 col-6')>
            <div @class('card p-3 shadow-sm border-0 h-100')>
                <div @class('mb-2')>
                    <i @class('bi bi-x-circle-fill text-danger fs-3')></i>
                </div>
                <div @class('ps-2')>
                    <div @class('fw-semibold fs-4')>{{ $stats['rejected'] ?? 0 }}</div>
                    <div @class('text-muted small')>Rejected</div>
                </div>
            </div>
        </div>
    </div>

    {{-- HEADER ACTIONS --}}
    <div @class('d-flex justify-content-between align-items-center')>

        {{-- LEFT SIDE --}}
        <div @class('mb-3 d-flex justify-content-between align-items-center gap-2')>
            
            {{-- SEARCH BAR --}}
            <div>
                <x-search-input
                    wire:model.live="search" 
                    placeholder="Search..."
                />
            </div>

            {{-- FILTER DROPDOWN --}}
            <div @class('dropdown')>
                <button
                    type="button"
                    id="filterDropdown"
                    data-bs-toggle="dropdown"
                    @class('btn btn-outline-body-tertiary dropdown-toggle d-flex align-items-center border rounded bg-secondary-subtle')
                >
                    <i @class('bi bi-funnel-fill me-2')></i>
                    Filter: {{ $statusFilter ?? 'All' }}
                </button>

                <ul @class('dropdown-menu') aria-labelledby="filterDropdown">
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'All')">
                            All
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Pending')">
                           Pending
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Approved')">
                            Approved
                        </a>
                    </li>
                    <li>
                        <a @class('dropdown-item') wire:click="$set('statusFilter', 'Rejected')">
                            Rejected
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div @class('mb-3')>
            <div @class('d-flex justify-content-between align-items-center gap-2')>
                
                <button
                    @class('btn btn-success')
                    wire:click="exportData"
                >
                    <i class="bi bi-download me-2"></i>Export
                </button>

                <button
                    @class('btn btn-primary')
                    wire:click="openBookingModal"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    New Booking
                </button>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE --}}
    <div @class('p-5 bg-white rounded border rounded-bottom-0 border-bottom-0')>
        <h3>All Reservations</h3>
        <p @class('text-secondary mb-0')>
            Overview of facility booking requests
        </p>
    </div>
    <div @class('table-responsive border rounded bg-white px-5 rounded-top-0 border-top-0')>
        @if($loading)
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading reservations...</p>
            </div>
        @else
            <table @class('table')>
                <thead>
                    <tr @class('bg-dark')>
                        <th @class('text-secondary')>Facility</th>
                        <th @class('text-secondary')>Requested By</th>
                        <th @class('text-secondary')>Date & Time</th>
                        <th @class('text-secondary')>Purpose</th>
                        <th @class('text-secondary')>Priority</th>
                        <th @class('text-secondary')>Status</th>
                        <th @class('text-secondary')>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredReservations as $reservation)
                        <tr wire:key="{{ $reservation['request_id'] }}">
                            <td>
                                <div class="fw-medium">{{ $reservation['facility_name'] }}</div>
                                <small class="text-muted">{{ $reservation['location'] }}</small>
                            </td>
                            <td>
                                <div class="fw-medium">{{ $reservation['full_name'] }}</div>
                                <small class="text-muted">{{ $reservation['email'] }}</small>
                            </td>
                            <td>
                                <div class="fw-medium">{{ \Carbon\Carbon::parse($reservation['booking_date'])->format('M d, Y') }}</div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($reservation['start_time'])->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($reservation['end_time'])->format('h:i A') }}
                                </small>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 150px;" title="{{ $reservation['purpose'] }}">
                                    {{ $reservation['purpose'] }}
                                </div>
                                <small class="text-muted">{{ $reservation['expected_attendees'] }} attendees</small>
                            </td>
                            <td>
                                @switch(strtolower($reservation['priority_level']))
                                    @case('urgent')
                                        <span @class('badge bg-danger')>{{ ucfirst($reservation['priority_level']) }}</span>
                                        @break
                                    @case('high')
                                        <span @class('badge bg-warning text-dark')>{{ ucfirst($reservation['priority_level']) }}</span>
                                        @break
                                    @case('medium')
                                        <span @class('badge bg-info text-dark')>{{ ucfirst($reservation['priority_level']) }}</span>
                                        @break
                                    @case('low')
                                        <span @class('badge bg-secondary')>{{ ucfirst($reservation['priority_level']) }}</span>
                                        @break
                                    @default
                                        <span @class('badge bg-secondary')>{{ ucfirst($reservation['priority_level']) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @switch(strtolower($reservation['status']))
                                    @case('approved')
                                        <span @class('badge bg-success')>{{ ucfirst($reservation['status']) }}</span>
                                        @break
                                    @case('pending')
                                        <span @class('badge bg-warning text-dark')>{{ ucfirst($reservation['status']) }}</span>
                                        @break
                                    @case('rejected')
                                        <span @class('badge bg-danger')>{{ ucfirst($reservation['status']) }}</span>
                                        @break
                                    @case('cancelled')
                                        <span @class('badge bg-secondary')>{{ ucfirst($reservation['status']) }}</span>
                                        @break
                                    @case('completed')
                                        <span @class('badge bg-primary')>{{ ucfirst($reservation['status']) }}</span>
                                        @break
                                    @default
                                        <span @class('badge bg-secondary')>{{ ucfirst($reservation['status']) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <button
                                    @class('btn btn-sm btn-outline-primary')
                                    wire:click="viewDetails({{ $reservation['request_id'] }})"
                                    title="View Details"
                                >
                                    <i @class('bi bi-eye')></i>
                                </button>
                                @if(session('user.position') === 'HR Manager')
                                <button
                                    @class('btn btn-sm btn-danger')
                                    wire:click="deleteRequest({{ $reservation['request_id'] }})"
                                    wire:confirm="Are you sure you want to delete this request?"
                                    title="Delete"
                                >
                                    <i @class('bi bi-trash')></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted py-5')>
                                @if($search)
                                    <i @class('bi bi-search d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No reservations found matching "{{ $search }}".</div>
                                @elseif($statusFilter && $statusFilter !== 'All')
                                    <i @class('bi bi-funnel d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No {{ $statusFilter }} reservations found.</div>
                                @else
                                    <i @class('bi bi-calendar-x d-block mx-auto fs-1')></i>
                                    <div class="mt-3">No reservations found.</div>
                                    <button class="btn btn-primary mt-3" wire:click="openBookingModal">
                                        <i class="bi bi-plus-lg me-1"></i> Create New Booking
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>

    {{-- Modals --}}
    @include('livewire.user.facility-request.includes.facility-new-booking-modal')
    @include('livewire.user.facility-request.includes.facility-view-details-modal')
</div>
