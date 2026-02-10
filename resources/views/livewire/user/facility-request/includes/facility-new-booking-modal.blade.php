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
