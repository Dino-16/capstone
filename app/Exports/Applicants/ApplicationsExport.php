<?php

namespace App\Exports\Applicants;

use App\Exports\Traits\CsvExportable;
use App\Models\Applicants\Application;

class ApplicationsExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Applied Position',
            'Department',
            'Rating Score',
            'Qualification Status',
            'Status',
            'Created At',
        ];
    }

    public function rows(): array
    {
        return Application::with('filteredResume')->get()->map(function ($application) {
            return [
                $application->first_name,
                $application->last_name,
                $application->email,
                $application->phone,
                $application->applied_position,
                $application->department,
                optional($application->filteredResume)->rating_score ?? 'N/A',
                optional($application->filteredResume)->qualification_status ?? 'N/A',
                $application->status,
                $application->created_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
