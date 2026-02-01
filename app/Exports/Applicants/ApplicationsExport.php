<?php

namespace App\Exports\Applicants;

use App\Models\Applicants\Application;
use App\Models\Applicants\FilteredResume;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationsExport
{
    public function export(): StreamedResponse
    {
        $query = Application::query()->latest();
        
        $headers = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Applied Position',
            'Department',
            'Status',
            'AI Score',
            'Qualification Status',
            'Created At'
        ];

        $mappings = [
            'ID' => 'id',
            'Name' => function($item) {
                return $item->first_name . ' ' . $item->last_name;
            },
            'Email' => 'email',
            'Phone' => 'phone',
            'Applied Position' => 'applied_position',
            'Department' => 'department',
            'Status' => 'status',
            'AI Score' => function($item) {
                 $resume = FilteredResume::where('application_id', $item->id)->first();
                 return $resume ? $resume->rating_score : 'N/A';
            },
            'Qualification Status' => function($item) {
                 $resume = FilteredResume::where('application_id', $item->id)->first();
                 return $resume ? $resume->qualification_status : 'N/A';
            },
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToXls($data, $headers, 'applications_' . date('Y-m-d') . '.xls');
    }
}
