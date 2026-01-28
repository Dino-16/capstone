<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class FacilityRequest extends Component
{
    // Form properties (matching API field names)
    public $facilityType;
    public $requestedBy;
    public $department;
    public $contactEmail;
    public $bookingDate;
    public $startTime;
    public $endTime;
    public $expectedAttendees;
    public $priority;
    public $purpose;
    public $specialRequirements;

    // Modal and data properties
    public $showBookingModal = false;
    public $reservations = [];
    public $stats = [];
    public $loading = true;
    public $selectedReservation = null;
    public $showDetailsModal = false;
    public $statusFilter = 'All';
    public $availableFacilities = [];

    public function mount()
    {
        $this->fetchReservations();
        $this->loadAvailableFacilities();
    }

    public function loadAvailableFacilities()
    {
        // Facility options from API specification
        $this->availableFacilities = [
            [
                'facility_type' => 'conference-room',
                'facility_name' => 'Conference Room A',
                'capacity' => '20',
                'equipment' => 'Projector, Video Conference, Whiteboard, WiFi',
                'location' => 'Building A, Floor 2'
            ],
            [
                'facility_type' => 'meeting-room',
                'facility_name' => 'Meeting Room B1',
                'capacity' => '8',
                'equipment' => 'TV Screen, Whiteboard, WiFi',
                'location' => 'Building B, Floor 1'
            ],
            [
                'facility_type' => 'training-room',
                'facility_name' => 'Training Room C',
                'capacity' => '30',
                'equipment' => 'Computers, Projector, Sound System, WiFi',
                'location' => 'Building C, Floor 1'
            ],
            [
                'facility_type' => 'auditorium',
                'facility_name' => 'Main Auditorium',
                'capacity' => '200',
                'equipment' => 'Stage, Sound System, Lighting, Projector',
                'location' => 'Building A, Ground Floor'
            ],
            [
                'facility_type' => 'event-hall',
                'facility_name' => 'Event Hall',
                'capacity' => '150',
                'equipment' => 'Sound System, Catering Setup, Stage Area',
                'location' => 'Building D, Ground Floor'
            ]
        ];
    }

    public function fetchReservations()
    {
        $this->loading = true;
        
        try {
            $response = Http::withoutVerifying()->get('https://facilities-admin.jetlougetravels-ph.com/reservation_status_api.php');
            
            if ($response->successful()) {
                $data = $response->json();
                $this->reservations = $data['reservations'] ?? [];
                $this->stats = $data['stats'] ?? [];
            } else {
                $this->reservations = [];
                $this->stats = [];
            }
        } catch (\Exception $e) {
            \Log::error('Failed to fetch reservations: ' . $e->getMessage());
            $this->reservations = [];
            $this->stats = [];
        }
        
        $this->loading = false;
    }

    public function getFilteredReservationsProperty()
    {
        if ($this->statusFilter === 'All') {
            return $this->reservations;
        }

        return collect($this->reservations)->filter(function ($reservation) {
            return strtolower($reservation['status']) === strtolower($this->statusFilter);
        })->values()->toArray();
    }

    public function openBookingModal()
    {
        $this->resetForm();
        $this->showBookingModal = true;
    }

    public function closeBookingModal()
    {
        $this->showBookingModal = false;
    }

    public function viewDetails($reservationId)
    {
        $this->selectedReservation = collect($this->reservations)->firstWhere('request_id', $reservationId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedReservation = null;
    }

    public function sendBookingRequest()
    {
        $this->validate([
            'facilityType' => 'required',
            'requestedBy' => 'required',
            'department' => 'required',
            'contactEmail' => 'required|email',
            'bookingDate' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required',
            'expectedAttendees' => 'required|integer|min:1',
            'priority' => 'required',
            'purpose' => 'required',
        ]);

        $response = Http::withoutVerifying()->asForm()->post('https://facilities-admin.jetlougetravels-ph.com/booking_request_api.php', [
            'facilityType' => $this->facilityType,
            'requestedBy' => $this->requestedBy,
            'department' => $this->department,
            'contactEmail' => $this->contactEmail,
            'bookingDate' => $this->bookingDate,
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'expectedAttendees' => (int) $this->expectedAttendees,
            'priority' => $this->priority,
            'purpose' => $this->purpose,
            'specialRequirements' => $this->specialRequirements ?? '',
        ]);

        if ($response->successful()) {
            $result = $response->json();
            if (isset($result['success']) && $result['success']) {
                session()->flash('message', $result['message'] ?? 'Booking request submitted successfully.');
            } else {
                session()->flash('message', 'Booking request submitted successfully.');
            }
            $this->resetForm();
            $this->showBookingModal = false;
            $this->fetchReservations(); // Refresh the table
        } else {
            $errorMessage = 'Failed to submit booking request.';
            $result = $response->json();
            if (isset($result['message'])) {
                $errorMessage = $result['message'];
            }
            session()->flash('error', $errorMessage . ' Status: ' . $response->status());
        }
    }

    public function resetForm()
    {
        $this->reset([
            'facilityType', 'requestedBy', 'department', 'bookingDate',
            'startTime', 'endTime', 'expectedAttendees', 'purpose',
            'specialRequirements', 'priority', 'contactEmail'
        ]);
    }

    public function render()
    {
        return view('livewire.user.facility-request', [
            'filteredReservations' => $this->filteredReservations
        ])->layout('layouts.app');
    }
}
