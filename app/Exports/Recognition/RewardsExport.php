<?php

namespace App\Exports\Recognition;

use App\Models\Recognition\Reward;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RewardsExport
{
    public function export(): StreamedResponse
    {
        $query = Reward::query();
        
        $headers = [
            'ID',
            'Name',
            'Type',
            'Description',
            'Points Required',
            'Is Active',
            'Created At',
            'Updated At'
        ];

        $mappings = [
            'ID' => 'id',
            'Name' => 'name',
            'Type' => 'type',
            'Description' => 'description',
            'Points Required' => 'points_required',
            'Is Active' => function($item) {
                return $item->is_active ? 'Yes' : 'No';
            },
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
            'Updated At' => function($item) {
                return $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToCsv($data, $headers, 'rewards_' . date('Y-m-d') . '.csv');
    }
}
