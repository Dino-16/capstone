<?php

namespace App\Exports\Performance;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PerformanceTrackerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $employees;

    public function __construct(array $employees)
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
            'Position',
            'Department',
            'Email',
            'Hire Date',
            'Completed Evaluations',
            'Pending Evaluations',
            'Next Evaluation',
        ];
    }

    public function map($employee): array
    {
        $nextEval = collect($employee['monthly_evaluations'] ?? [])
            ->where('status', '!=', 'completed')
            ->first();
        $nextEvalMonth = $nextEval ? ($nextEval['month'] ?? 'Unknown') : 'All Caught Up';

        return [
            $employee['name'] ?? '',
            $employee['position'] ?? '',
            $employee['department'] ?? '',
            $employee['email'] ?? '',
            $employee['hire_date'] ?? '',
            $employee['completed_evaluations'] ?? 0,
            $employee['pending_evaluations'] ?? 0,
            $nextEvalMonth,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
