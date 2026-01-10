<?php

namespace App\Exports\Recognition;

use App\Models\Recognition\GiveReward;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GiveRewardsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return GiveReward::with('reward')->orderBy('created_at', 'desc');
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

    public function map($giveReward): array
    {
        return [
            $giveReward->id,
            $giveReward->employee_name,
            $giveReward->employee_email,
            $giveReward->employee_position,
            $giveReward->employee_department,
            $giveReward->reward ? $giveReward->reward->name : 'N/A',
            $giveReward->reward ? $giveReward->reward->type : 'N/A',
            $giveReward->given_by,
            $giveReward->given_date->format('Y-m-d'),
            ucfirst($giveReward->status),
            $giveReward->reason ?? 'N/A',
            $giveReward->notes ?? 'N/A',
            $giveReward->created_at->format('Y-m-d H:i:s'),
            $giveReward->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
