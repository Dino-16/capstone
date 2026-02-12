<?php

namespace App\Exports\Recruitment;

use App\Exports\Traits\CsvExportable;
use App\Models\Recruitment\JobListing;

class JobPostsExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Position',
            'Department',
            'Description',
            'Qualifications',
            'Type',
            'Arrangement',
            'Status',
            'Expiration Date',
            'Created At',
            'Updated At',
        ];
    }

    public function rows(): array
    {
        return JobListing::where('status', 'Active')->get()->map(function ($job) {
            return [
                $job->position,
                $job->department ?? 'N/A',
                strip_tags($job->description ?? ''),
                strip_tags($job->qualifications ?? ''),
                $job->type ?? 'N/A',
                $job->arrangement ?? 'N/A',
                $job->status,
                $job->expiration_date ?? 'N/A',
                $job->created_at?->format('Y-m-d H:i:s'),
                $job->updated_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
