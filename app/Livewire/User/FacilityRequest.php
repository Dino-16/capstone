<?php

namespace App\Livewire\User;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Exports\FacilityRequest\FacilityRequestExport;

class FacilityRequest extends Component
{
    use \App\Livewire\Traits\HandlesToasts;
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
    public $search = '';

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
        $userPosition = session('user.position');
        
        $filtered = collect($this->reservations);

        // If not Super Admin, apply department/role filtering
        if ($userPosition !== 'Super Admin') {
            $filtered = $filtered->filter(function ($reservation) {
                $name = strtolower($reservation['requested_by'] ?? $reservation['full_name'] ?? '');
                $dept = strtolower($reservation['department_name'] ?? $reservation['department'] ?? '');
                
                // Visible if name or department contains HR-related keywords
                $hrKeywords = ['hr staff', 'hr manager', 'hr', 'human resource'];
                
                $matchesName = false;
                foreach ($hrKeywords as $keyword) {
                    if (str_contains($name, $keyword)) {
                        $matchesName = true;
                        break;
                    }
                }

                $matchesDept = false;
                foreach ($hrKeywords as $keyword) {
                    if (str_contains($dept, $keyword)) {
                        $matchesDept = true;
                        break;
                    }
                }
                
                return $matchesName || $matchesDept;
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'All') {
            $filtered = $filtered->filter(function ($reservation) {
                return strtolower($reservation['status']) === strtolower($this->statusFilter);
            });
        }

        // Apply search filter
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $filtered = $filtered->filter(function ($reservation) use ($search) {
                return str_contains(strtolower($reservation['facility_name'] ?? ''), $search)
                    || str_contains(strtolower($reservation['full_name'] ?? ''), $search)
                    || str_contains(strtolower($reservation['email'] ?? ''), $search)
                    || str_contains(strtolower($reservation['purpose'] ?? ''), $search)
                    || str_contains(strtolower($reservation['location'] ?? ''), $search)
                    || str_contains(strtolower($reservation['status'] ?? ''), $search);
            });
        }

        return $filtered->values()->toArray();
    }

    public function exportData()
    {
        $reservations = $this->filteredReservations;

        if (empty($reservations)) {
            $this->toast('No data to export.');
            return;
        }

        $export = new FacilityRequestExport($reservations);
        return $export->export();
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
                $this->toast('Booking request submitted successfully.');
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
            $this->toast($errorMessage . ' Status: ' . $response->status());
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


    public function deleteRequest($reservationId)
    {
         // Placeholder for future API integration
         $this->toast('Deletion is not currently supported for external API records.');
    }

    public function render()
    {
        return view('livewire.user.facility-request', [
            'filteredReservations' => $this->filteredReservations
        ])->layout('layouts.app');
    }
}
