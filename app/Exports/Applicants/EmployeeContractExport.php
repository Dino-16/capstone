<?php

namespace App\Exports\Applicants;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\Exportable; // Add this
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeContractExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    use Exportable; // Add this

    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return collect($this->employees);
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Email',
            'Department',
            'Position',
            'Date Hired',
            'End of Contract',
            'Status'
        ];
    }

    public function map($employee): array
    {
        return [
            $employee['name'],
            $employee['email'],
            $employee['department'],
            $employee['position'],
            $employee['date_hired'] ? $employee['date_hired']->format('M d, Y') : 'N/A',
            $employee['end_contract'] ? $employee['end_contract']->format('M d, Y') : 'N/A',
            'Active', // Assuming active since they are in the list
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
