<?php

namespace App\Exports\Onboarding;

use App\Models\Onboarding\OrientationSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrientationSchedulesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return OrientationSchedule::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Orientation Date',
            'Orientation Time',
            'Location',
            'Facilitator',
            'Status',
            'Notes',
            'Created At',
            'Updated At'
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->employee_name,
            $item->orientation_date ? $item->orientation_date->format('M d, Y') : '',
            $item->orientation_time ? $item->orientation_time->format('h:i A') : '',
            $item->location,
            $item->facilitator,
            $item->status,
            $item->notes ?? '',
            $item->created_at ? $item->created_at->format('M d, Y h:i A') : '',
            $item->updated_at ? $item->updated_at->format('M d, Y h:i A') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
