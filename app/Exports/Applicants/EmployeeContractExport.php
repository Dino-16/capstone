<?php

namespace App\Exports\Applicants;

use App\Exports\Traits\CsvExportable;
use Carbon\Carbon;

class EmployeeContractExport
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
            'Email',
            'Position',
            'Department',
            'Date Hired',
            'End of Contract',
        ];
    }

    public function rows(): array
    {
        return collect($this->employees)->map(function ($employee) {
            return [
                $employee['name'] ?? 'N/A',
                $employee['email'] ?? 'N/A',
                $employee['position'] ?? 'N/A',
                $employee['department'] ?? 'N/A',
                isset($employee['date_hired']) ? Carbon::parse($employee['date_hired'])->format('M d, Y') : 'N/A',
                isset($employee['end_contract']) ? Carbon::parse($employee['end_contract'])->format('M d, Y') : 'N/A',
            ];
        })->toArray();
    }
}
