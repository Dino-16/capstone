<?php

namespace App\Exports\Performance;

use App\Exports\Traits\CsvExportable;
use App\Models\Performance\Evaluation;

class EvaluationRecordsExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Employee Name',
            'Position',
            'Department',
            'Evaluator',
            'Evaluation Period',
            'Quality of Work',
            'Communication',
            'Teamwork',
            'Initiative',
            'Attendance',
            'Overall Score',
            'Status',
            'Created At',
        ];
    }

    public function rows(): array
    {
        return Evaluation::all()->map(function ($evaluation) {
            return [
                $evaluation->employee_name,
                $evaluation->position ?? 'N/A',
                $evaluation->department ?? 'N/A',
                $evaluation->evaluator_name ?? 'N/A',
                $evaluation->evaluation_period ?? 'N/A',
                $evaluation->quality_of_work ?? 'N/A',
                $evaluation->communication ?? 'N/A',
                $evaluation->teamwork ?? 'N/A',
                $evaluation->initiative ?? 'N/A',
                $evaluation->attendance_score ?? 'N/A',
                $evaluation->overall_score ?? 'N/A',
                $evaluation->status ?? 'N/A',
                $evaluation->created_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
