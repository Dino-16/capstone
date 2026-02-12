<?php

namespace App\Exports\Onboarding;

use App\Exports\Traits\CsvExportable;

class EmployeesExport
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
            'Name',
            'Position',
            'Department',
            'Contract Signing',
            'HR Documents',
            'Training Modules',
        ];
    }

    public function rows(): array
    {
        return collect($this->employees)->map(function ($employee) {
            return [
                $employee['Name'] ?? 'N/A',
                $employee['Position'] ?? 'N/A',
                $employee['Department'] ?? 'N/A',
                $employee['Contract Signing'] ?? 'N/A',
                $employee['HR Documents'] ?? 'N/A',
                $employee['Training Modules'] ?? 'N/A',
            ];
        })->toArray();
    }
}
