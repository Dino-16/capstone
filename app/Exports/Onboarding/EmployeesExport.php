<?php

namespace App\Exports\Onboarding;

use App\Services\ExportService;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeesExport
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Name',
            'Position', 
            'Department',
            'Contract Signing',
            'HR Documents',
            'Training Modules',
        ];

        $data = collect($this->employees)->map(function ($employee) use ($headers) {
            $row = [];
            foreach ($headers as $header) {
                $row[$header] = $employee[$header] ?? '';
            }
            return $row;
        });
        
        return ExportService::exportToCsv($data, $headers, 'employees_' . date('Y-m-d') . '.csv');
    }
}
