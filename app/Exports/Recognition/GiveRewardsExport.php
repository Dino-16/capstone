<?php

namespace App\Exports\Recognition;

use App\Models\Recognition\GiveReward;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GiveRewardsExport
{
    public function export(): StreamedResponse
    {
        $query = GiveReward::with('reward')->orderBy('created_at', 'desc');
        
        $headers = [
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

        $mappings = [
            'ID' => 'id',
            'Employee Name' => 'employee_name',
            'Employee Email' => 'employee_email',
            'Employee Position' => 'employee_position',
            'Employee Department' => 'employee_department',
            'Reward Name' => function($item) {
                return $item->reward ? $item->reward->name : 'N/A';
            },
            'Reward Type' => function($item) {
                return $item->reward ? $item->reward->type : 'N/A';
            },
            'Given By' => 'given_by',
            'Given Date' => function($item) {
                return $item->given_date->format('Y-m-d');
            },
            'Status' => function($item) {
                return ucfirst($item->status);
            },
            'Reason' => function($item) {
                return $item->reason ?? 'N/A';
            },
            'Notes' => function($item) {
                return $item->notes ?? 'N/A';
            },
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
            'Updated At' => function($item) {
                return $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToCsv($data, $headers, 'give_rewards_' . date('Y-m-d') . '.csv');
    }
}
