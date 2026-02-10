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
