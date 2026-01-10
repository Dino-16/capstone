<?php

namespace App\Exports\Onboarding;

use App\Models\Onboarding\OrientationSchedule;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrientationSchedulesExport
{
    public function export(): StreamedResponse
    {
        $query = OrientationSchedule::query();
        
        $headers = [
            'ID',
            'Employee Name',
            'Orientation Date',
            'Orientation Time',
            'Location',
            'Facilitator',
            'Status',
            'Notes',
            'Created At',
            'Updated At'
        ];

        $mappings = [
            'ID' => 'id',
            'Employee Name' => 'employee_name',
            'Orientation Date' => function($item) {
                return $item->orientation_date->format('Y-m-d');
            },
            'Orientation Time' => function($item) {
                return $item->orientation_time->format('H:i:s');
            },
            'Location' => 'location',
            'Facilitator' => 'facilitator',
            'Status' => 'status',
            'Notes' => function($item) {
                return $item->notes ?? '';
            },
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
            'Updated At' => function($item) {
                return $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToXls($data, $headers, 'orientation_schedules_' . date('Y-m-d') . '.xls');
    }
}
