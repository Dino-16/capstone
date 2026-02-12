<?php

namespace App\Exports\FacilityRequest;

use App\Exports\Traits\CsvExportable;

class FacilityRequestExport
{
    use CsvExportable;

    protected $reservations;

    public function __construct(array $reservations)
    {
        $this->reservations = $reservations;
    }

    public function headings(): array
    {
        return [
            'Requested By',
            'Facility Name',
            'Location',
            'Booking Date',
            'Start Time',
            'End Time',
            'Purpose',
            'Status',
        ];
    }

    public function rows(): array
    {
        return collect($this->reservations)->map(function ($reservation) {
            return [
                $reservation['requested_by'] ?? $reservation['full_name'] ?? 'N/A',
                $reservation['facility_name'] ?? 'N/A',
                $reservation['location'] ?? 'N/A',
                $reservation['booking_date'] ?? 'N/A',
                $reservation['start_time'] ?? 'N/A',
                $reservation['end_time'] ?? 'N/A',
                $reservation['purpose'] ?? 'N/A',
                $reservation['status'] ?? 'N/A',
            ];
        })->toArray();
    }
}
