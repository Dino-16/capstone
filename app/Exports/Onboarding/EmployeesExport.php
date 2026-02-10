<?php

namespace App\Exports\Onboarding;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
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
            'Name',
            'Position', 
            'Department',
            'Contract Signing',
            'HR Documents',
            'Training Modules',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee['Name'] ?? '',
            $employee['Position'] ?? '',
            $employee['Department'] ?? '',
            $employee['Contract Signing'] ?? '',
            $employee['HR Documents'] ?? '',
            $employee['Training Modules'] ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
