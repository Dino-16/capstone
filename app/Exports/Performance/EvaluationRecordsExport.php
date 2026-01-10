<?php

namespace App\Exports\Performance;

use App\Models\Performance\Evaluation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EvaluationRecordsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Evaluation::query();
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

    public function map($evaluation): array
    {
        return [
            $evaluation->employee_name,
            $evaluation->email,
            $evaluation->evaluation_date->format('Y-m-d'),
            $evaluation->evaluator_name,
            $evaluation->position,
            $evaluation->department,
            $evaluation->employment_date ? $evaluation->employment_date->format('Y-m-d') : '',
            $evaluation->evaluation_type,
            $evaluation->overall_score,
            $evaluation->job_knowledge,
            $evaluation->work_quality,
            $evaluation->initiative,
            $evaluation->communication,
            $evaluation->dependability,
            $evaluation->attendance,
            $evaluation->strengths,
            $evaluation->areas_for_improvement,
            $evaluation->comments,
            $evaluation->status,
            $evaluation->created_at->format('Y-m-d H:i:s'),
            $evaluation->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
