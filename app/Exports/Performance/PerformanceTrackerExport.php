<?php

namespace App\Exports\Performance;

use App\Services\ExportService;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PerformanceTrackerExport
{
    protected array $employees;

    public function __construct(array $employees)
    {
        $this->employees = $employees;
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Employee Name',
            'Position',
            'Department',
            'Email',
            'Hire Date',
            'Completed Evaluations',
            'Pending Evaluations',
            'Next Evaluation',
        ];

        $data = collect($this->employees)->map(function ($employee) {
            $nextEval = collect($employee['monthly_evaluations'] ?? [])
                ->where('status', '!=', 'completed')
                ->first();
            $nextEvalMonth = $nextEval ? ($nextEval['month'] ?? 'Unknown') : 'All Caught Up';

            return [
                'Employee Name' => $employee['name'] ?? '',
                'Position' => $employee['position'] ?? '',
                'Department' => $employee['department'] ?? '',
                'Email' => $employee['email'] ?? '',
                'Hire Date' => $employee['hire_date'] ?? '',
                'Completed Evaluations' => $employee['completed_evaluations'] ?? 0,
                'Pending Evaluations' => $employee['pending_evaluations'] ?? 0,
                'Next Evaluation' => $nextEvalMonth,
            ];
        });

        return ExportService::exportToCsv($data, $headers, 'performance_tracker_' . date('Y-m-d') . '.csv');
    }
}
