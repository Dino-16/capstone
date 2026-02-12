<?php

namespace App\Exports\Recognition;

use App\Exports\Traits\CsvExportable;
use App\Models\Recognition\GiveReward;

class GiveRewardsExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee Email',
            'Reward Name',
            'Reward Type',
            'Points',
            'Given By',
            'Status',
            'Reason',
            'Notes',
            'Created At',
            'Updated At',
        ];
    }

    public function rows(): array
    {
        return GiveReward::with('reward')->get()->map(function ($giveReward) {
            return [
                $giveReward->employee_name ?? 'N/A',
                $giveReward->employee_email ?? 'N/A',
                optional($giveReward->reward)->name ?? 'N/A',
                optional($giveReward->reward)->type ?? 'N/A',
                optional($giveReward->reward)->points ?? 0,
                $giveReward->given_by ?? 'N/A',
                $giveReward->status ?? 'N/A',
                $giveReward->reason ?? 'N/A',
                $giveReward->notes ?? 'N/A',
                $giveReward->created_at?->format('Y-m-d H:i:s'),
                $giveReward->updated_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
