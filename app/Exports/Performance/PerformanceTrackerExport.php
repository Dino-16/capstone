<?php

namespace App\Exports\Performance;

use App\Exports\Traits\CsvExportable;

class PerformanceTrackerExport
{
    use CsvExportable;

    protected $employees;

    public function __construct(array $employees)
    {
        $this->employees = $employees;
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Position',
            'Department',
            'Completed Evaluations',
            'Pending Evaluations',
            'Next Evaluation',
        ];
    }

    public function rows(): array
    {
        return collect($this->employees)->map(function ($employee) {
            return [
                $employee['name'] ?? ($employee['full_name'] ?? 'N/A'),
                $employee['position'] ?? 'N/A',
                $employee['department'] ?? 'N/A',
                $employee['completed_evaluations'] ?? 0,
                $employee['pending_evaluations'] ?? 0,
                $employee['next_evaluation'] ?? 'N/A',
            ];
        })->toArray();
    }
}
