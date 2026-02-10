<?php

namespace App\Exports\Performance;

use App\Models\Performance\Evaluation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluationRecordsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Evaluation::all();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Email',
            'Evaluation Date',
            'Evaluator Name',
            'Position',
            'Department',
            'Employment Date',
            'Evaluation Type',
            'Overall Score',
            'Job Knowledge',
            'Work Quality',
            'Initiative',
            'Communication',
            'Dependability',
            'Attendance',
            'Strengths',
            'Areas for Improvement',
            'Comments',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function map($item): array
    {
        return [
            $item->employee_name,
            $item->email,
            $item->evaluation_date ? $item->evaluation_date->format('M d, Y') : '',
            $item->evaluator_name,
            $item->position,
            $item->department,
            $item->employment_date ? $item->employment_date->format('M d, Y') : '',
            $item->evaluation_type,
            $item->overall_score,
            $item->job_knowledge,
            $item->work_quality,
            $item->initiative,
            $item->communication,
            $item->dependability,
            $item->attendance,
            $item->strengths,
            $item->areas_for_improvement,
            $item->comments,
            $item->status,
            $item->created_at ? $item->created_at->format('M d, Y h:i A') : '',
            $item->updated_at ? $item->updated_at->format('M d, Y h:i A') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
