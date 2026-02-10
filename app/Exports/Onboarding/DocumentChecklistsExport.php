<?php

namespace App\Exports\Onboarding;

use App\Models\Onboarding\DocumentChecklist;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentChecklistsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return DocumentChecklist::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Document Type',
            'Document Name',
            'Status',
            'Submitted Date',
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
            $item->document_type,
            $item->document_name,
            $item->status,
            $item->submitted_date ? $item->submitted_date->format('M d, Y') : '',
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
