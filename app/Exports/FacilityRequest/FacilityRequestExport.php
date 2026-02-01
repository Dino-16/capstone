<?php

namespace App\Exports\FacilityRequest;

use App\Services\ExportService;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FacilityRequestExport
{
    protected array $reservations;

    public function __construct(array $reservations)
    {
        $this->reservations = $reservations;
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Facility',
            'Location',
            'Requested By',
            'Email',
            'Booking Date',
            'Start Time',
            'End Time',
            'Purpose',
            'Attendees',
            'Priority',
            'Status',
        ];

        $data = collect($this->reservations)->map(function ($reservation) {
            return [
                'Facility' => $reservation['facility_name'] ?? '',
                'Location' => $reservation['location'] ?? '',
                'Requested By' => $reservation['full_name'] ?? '',
                'Email' => $reservation['email'] ?? '',
                'Booking Date' => $reservation['booking_date'] ?? '',
                'Start Time' => $reservation['start_time'] ?? '',
                'End Time' => $reservation['end_time'] ?? '',
                'Purpose' => $reservation['purpose'] ?? '',
                'Attendees' => $reservation['expected_attendees'] ?? '',
                'Priority' => $reservation['priority_level'] ?? '',
                'Status' => $reservation['status'] ?? '',
            ];
        });

        return ExportService::exportToXls($data, $headers, 'facility_requests_' . date('Y-m-d') . '.xls');
    }
}
