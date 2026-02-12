<?php

namespace App\Exports\Recognition;

use App\Exports\Traits\CsvExportable;
use App\Models\Recognition\Reward;

class RewardsExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Name',
            'Type',
            'Description',
            'Points',
            'Active',
            'Created At',
            'Updated At',
        ];
    }

    public function rows(): array
    {
        return Reward::all()->map(function ($reward) {
            return [
                $reward->name,
                $reward->type ?? 'N/A',
                $reward->description ?? 'N/A',
                $reward->points ?? 0,
                $reward->is_active ? 'Yes' : 'No',
                $reward->created_at?->format('Y-m-d H:i:s'),
                $reward->updated_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
