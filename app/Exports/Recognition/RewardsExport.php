<?php

namespace App\Exports\Recognition;

use App\Models\Recognition\Reward;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RewardsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Reward::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Description',
            'Points Required',
            'Is Active',
            'Created At',
            'Updated At'
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->type,
            $item->description,
            $item->points_required,
            $item->is_active ? 'Yes' : 'No',
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
