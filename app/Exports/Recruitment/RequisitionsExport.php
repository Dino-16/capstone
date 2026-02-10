<?php

namespace App\Exports\Recruitment;

use App\Models\Recruitment\Requisition;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RequisitionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Requisition::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Requested By',
            'Department',
            'Position',
            'No. of Openings',
            'Status',
            'Created At',
            'Updated At'
        ];
    }

    public function map($requisition): array
    {
        return [
            $requisition->id,
            $requisition->requested_by,
            $requisition->department,
            $requisition->position,
            $requisition->opening,
            $requisition->status ?? 'Pending', // Default if not set
            $requisition->created_at ? $requisition->created_at->format('M d, Y h:i A') : '',
            $requisition->updated_at ? $requisition->updated_at->format('M d, Y h:i A') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
