<?php

namespace App\Exports\Recognition;

use App\Models\Recognition\GiveReward;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GiveRewardsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return GiveReward::with('reward')->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Employee Name',
            'Employee Email',
            'Employee Position',
            'Employee Department',
            'Reward Name',
            'Reward Type',
            'Given By',
            'Given Date',
            'Status',
            'Reason',
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
            $item->employee_email,
            $item->employee_position,
            $item->employee_department,
            $item->reward ? $item->reward->name : 'N/A',
            $item->reward ? $item->reward->type : 'N/A',
            $item->given_by,
            $item->given_date ? $item->given_date->format('M d, Y') : '',
            ucfirst($item->status),
            $item->reason ?? 'N/A',
            $item->notes ?? 'N/A',
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
