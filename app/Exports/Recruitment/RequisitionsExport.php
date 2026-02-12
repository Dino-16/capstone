<?php

namespace App\Exports\Recruitment;

use App\Exports\Traits\CsvExportable;
use App\Models\Recruitment\Requisition;

class RequisitionsExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Position',
            'Department',
            'Opening',
            'Description',
            'Qualifications',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function rows(): array
    {
        return Requisition::all()->map(function ($requisition) {
            return [
                $requisition->position,
                $requisition->department ?? 'N/A',
                $requisition->opening ?? 0,
                $requisition->description ?? 'N/A',
                $requisition->qualifications ?? 'N/A',
                $requisition->status,
                $requisition->created_at?->format('Y-m-d H:i:s'),
                $requisition->updated_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
