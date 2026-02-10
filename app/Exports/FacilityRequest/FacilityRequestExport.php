<?php

namespace App\Exports\FacilityRequest;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FacilityRequestExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $reservations;

    public function __construct(array $reservations)
    {
        $this->reservations = $reservations;
    }

    public function collection()
    {
        return collect($this->reservations);
    }

    public function headings(): array
    {
        return [
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
    }

    public function map($reservation): array
    {
        return [
            $reservation['facility_name'] ?? '',
            $reservation['location'] ?? '',
            $reservation['full_name'] ?? '',
            $reservation['email'] ?? '',
            $reservation['booking_date'] ?? '',
            $reservation['start_time'] ?? '',
            $reservation['end_time'] ?? '',
            $reservation['purpose'] ?? '',
            $reservation['expected_attendees'] ?? '',
            $reservation['priority_level'] ?? '',
            $reservation['status'] ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
