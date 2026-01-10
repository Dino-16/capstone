<?php

namespace App\Exports\Recruitment;

use App\Models\Recruitment\JobListing;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobPostsExport
{
    public function export(): StreamedResponse
    {
        $query = JobListing::query()->where('status', 'Active');
        
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
            'Posted Date',
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
            'Posted Date' => function($item) {
                return $item->posted_date->format('Y-m-d');
            },
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
            'Updated At' => function($item) {
                return $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToXls($data, $headers, 'job_posts_' . date('Y-m-d') . '.xls');
    }
}
