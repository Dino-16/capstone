<?php

namespace App\Exports\Onboarding;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class EmployeesExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees;
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
}
