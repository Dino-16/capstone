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
                <x-text-input
                    type="search"
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
                    <i class="bi bi-download me-2"></i>Export to CSV
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
                                    @class('btn btn-primary btn-sm')
                                    wire:click="viewDetails({{ $reservation['request_id'] }})"
                                    title="View Details"
                                >
                                    <i @class('bi bi-eye')></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" @class('text-center text-muted py-5')>
                                <i @class('bi bi-calendar-x d-block mx-auto fs-1')></i>
                                <div class="mt-3">No reservations found.</div>
                                <button class="btn btn-primary mt-3" wire:click="openBookingModal">
                                    <i class="bi bi-plus-lg me-1"></i> Create New Booking
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>

    {{-- NEW BOOKING MODAL --}}
    @if($showBookingModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title">
                            <i class="bi bi-calendar-plus me-2"></i>
                            New Booking Request
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeBookingModal"></button>
                    </div>
                    <form wire:submit.prevent="sendBookingRequest">
                        <div class="modal-body">
                            {{-- Facility Selection Section --}}
                            <div class="mb-4">
                                <h6 class="fw-semibold border-bottom pb-2 mb-3">
                                    <i class="bi bi-building me-2"></i>Select Facility
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="facilityType" class="form-label">Facility Type <span class="text-danger">*</span></label>
                                        <select id="facilityType" wire:model="facilityType" class="form-select">
                                            <option value="">Select a facility...</option>
                                            @foreach($availableFacilities as $facility)
                                                <option value="{{ $facility['facility_type'] }}">
                                                    {{ $facility['facility_name'] }} ({{ $facility['location'] }}) - Capacity: {{ $facility['capacity'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('facilityType') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    {{-- Show selected facility info --}}
                                    @if($facilityType)
                                        @php
                                            $selectedFacility = collect($availableFacilities)->firstWhere('facility_type', $facilityType);
                                        @endphp
                                        @if($selectedFacility)
                                            <div class="col-12">
                                                <div class="alert alert-info mb-0">
                                                    <div class="row small">
                                                        <div class="col-md-6">
                                                            <strong><i class="bi bi-geo-alt me-1"></i>Location:</strong> {{ $selectedFacility['location'] }}
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong><i class="bi bi-people me-1"></i>Capacity:</strong> {{ $selectedFacility['capacity'] }}
                                                        </div>
                                                        <div class="col-md-3">
                                                            <strong><i class="bi bi-gear me-1"></i>Equipment:</strong> {{ $selectedFacility['equipment'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- Requester Information Section --}}
                            <div class="mb-4">
                                <h6 class="fw-semibold border-bottom pb-2 mb-3">
                                    <i class="bi bi-person me-2"></i>Requester Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="requestedBy" class="form-label">Requested By <span class="text-danger">*</span></label>
                                        <input type="text" id="requestedBy" wire:model="requestedBy" class="form-control">
                                        @error('requestedBy') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                        <input type="text" id="department" wire:model="department" placeholder="Enter department" class="form-control">
                                        @error('department') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="contactEmail" class="form-label">Contact Email <span class="text-danger">*</span></label>
                                        <input type="email" id="contactEmail" wire:model="contactEmail" placeholder="Enter email address" class="form-control">
                                        @error('contactEmail') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Booking Schedule Section --}}
                            <div class="mb-4">
                                <h6 class="fw-semibold border-bottom pb-2 mb-3">
                                    <i class="bi bi-calendar-event me-2"></i>Booking Schedule
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="bookingDate" class="form-label">Booking Date <span class="text-danger">*</span></label>
                                        <input type="date" id="bookingDate" wire:model="bookingDate" class="form-control" min="{{ date('Y-m-d') }}">
                                        @error('bookingDate') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="startTime" class="form-label">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" id="startTime" wire:model="startTime" class="form-control">
                                        @error('startTime') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="endTime" class="form-label">End Time <span class="text-danger">*</span></label>
                                        <input type="time" id="endTime" wire:model="endTime" class="form-control">
                                        @error('endTime') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Event Details Section --}}
                            <div class="mb-4">
                                <h6 class="fw-semibold border-bottom pb-2 mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Event Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="expectedAttendees" class="form-label">Expected Attendees <span class="text-danger">*</span></label>
                                        <input type="number" id="expectedAttendees" wire:model="expectedAttendees" placeholder="Number of attendees" class="form-control" min="1">
                                        @error('expectedAttendees') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="priority" class="form-label">Priority Level <span class="text-danger">*</span></label>
                                        <select id="priority" wire:model="priority" class="form-select">
                                            <option value="">Select Priority</option>
                                            <option value="low">Low - Social/Non-core activities</option>
                                            <option value="medium">Medium - Operational/Department meetings</option>
                                            <option value="high">High - Regulatory/Compliance meetings</option>
                                            <option value="urgent">Urgent - Critical/Executive meetings</option>
                                        </select>
                                        @error('priority') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="purpose" class="form-label">Purpose/Event Description <span class="text-danger">*</span></label>
                                        <textarea id="purpose" wire:model="purpose" rows="3" placeholder="Describe the purpose of the booking" class="form-control"></textarea>
                                        @error('purpose') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="specialRequirements" class="form-label">Special Requirements <span class="text-muted">(Optional)</span></label>
                                        <textarea id="specialRequirements" wire:model="specialRequirements" rows="2" placeholder="Any special equipment or setup requirements..." class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeBookingModal">
                                <i class="bi bi-x-lg me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="sendBookingRequest">
                                    <i class="bi bi-check-lg me-1"></i>Submit Request
                                </span>
                                <span wire:loading wire:target="sendBookingRequest">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    Submitting...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- VIEW DETAILS MODAL --}}
    @if($showDetailsModal && $selectedReservation)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-white border-bottom">
                        <h5 class="modal-title">
                            <i class="bi bi-info-circle me-2"></i>
                            Reservation Details
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetailsModal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Status & Priority Badges --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            @switch(strtolower($selectedReservation['status']))
                                @case('approved')
                                    <span @class('badge bg-success fs-6')>{{ ucfirst($selectedReservation['status']) }}</span>
                                    @break
                                @case('pending')
                                    <span @class('badge bg-warning text-dark fs-6')>{{ ucfirst($selectedReservation['status']) }}</span>
                                    @break
                                @case('rejected')
                                    <span @class('badge bg-danger fs-6')>{{ ucfirst($selectedReservation['status']) }}</span>
                                    @break
                                @default
                                    <span @class('badge bg-secondary fs-6')>{{ ucfirst($selectedReservation['status']) }}</span>
                            @endswitch

                            @switch(strtolower($selectedReservation['priority_level']))
                                @case('urgent')
                                    <span @class('badge bg-danger fs-6')>{{ ucfirst($selectedReservation['priority_level']) }} Priority</span>
                                    @break
                                @case('high')
                                    <span @class('badge bg-warning text-dark fs-6')>{{ ucfirst($selectedReservation['priority_level']) }} Priority</span>
                                    @break
                                @default
                                    <span @class('badge bg-info text-dark fs-6')>{{ ucfirst($selectedReservation['priority_level']) }} Priority</span>
                            @endswitch
                        </div>

                        {{-- Facility Information --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-building me-2"></i>Facility Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Facility</small>
                                        <span class="fw-medium">{{ $selectedReservation['facility_name'] }}</span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Location</small>
                                        <span class="fw-medium">{{ $selectedReservation['location'] }}</span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Capacity</small>
                                        <span class="fw-medium">{{ $selectedReservation['capacity'] }} people</span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Equipment</small>
                                        <span class="fw-medium">{{ $selectedReservation['equipment'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Booking Information --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Booking Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Requested By</small>
                                        <span class="fw-medium">{{ $selectedReservation['full_name'] }}</span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Email</small>
                                        <span class="fw-medium">{{ $selectedReservation['email'] }}</span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Date</small>
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($selectedReservation['booking_date'])->format('F d, Y') }}</span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Time</small>
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($selectedReservation['start_time'])->format('h:i A') }} - {{ \Carbon\Carbon::parse($selectedReservation['end_time'])->format('h:i A') }}</span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Expected Attendees</small>
                                        <span class="fw-medium">{{ $selectedReservation['expected_attendees'] }}</span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">Created</small>
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($selectedReservation['created_at'])->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Purpose --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-chat-text me-2"></i>Purpose</h6>
                            </div>
                            <div class="card-body">
                                {{ $selectedReservation['purpose'] }}
                            </div>
                        </div>

                        @if($selectedReservation['special_requirements'])
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Special Requirements</h6>
                                </div>
                                <div class="card-body">
                                    {{ $selectedReservation['special_requirements'] }}
                                </div>
                            </div>
                        @endif

                        @if($selectedReservation['rejection_reason'])
                            <div class="alert alert-danger">
                                <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Rejection Reason</h6>
                                {{ $selectedReservation['rejection_reason'] }}
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDetailsModal">
                            <i class="bi bi-x-lg me-1"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
