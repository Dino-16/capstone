<?php

namespace App\Exports\Recruitment;

use App\Models\Recruitment\Requisition;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequisitionsExport
{
    public function export(): StreamedResponse
    {
        $query = Requisition::query();
        
        $headers = [
            'ID',
            'Title',
            'Department',
            'Position',
            'Employment Type',
            'Salary Range',
            'Location',
            'Description',
            'Requirements',
            'Status',
            'Created At',
            'Updated At'
        ];

        $mappings = [
            'ID' => 'id',
            'Title' => 'title',
            'Department' => 'department',
            'Position' => 'position',
            'Employment Type' => 'employment_type',
            'Salary Range' => 'salary_range',
            'Location' => 'location',
            'Description' => 'description',
            'Requirements' => 'requirements',
            'Status' => 'status',
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
            'Updated At' => function($item) {
                return $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToCsv($data, $headers, 'requisitions_' . date('Y-m-d') . '.csv');
    }
}
