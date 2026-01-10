<?php

namespace App\Exports\Performance;

use App\Models\Performance\Evaluation;
use App\Services\ExportService;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvaluationRecordsExport
{
    public function export(): StreamedResponse
    {
        $query = Evaluation::query();
        
        $headers = [
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

        $mappings = [
            'Employee Name' => 'employee_name',
            'Email' => 'email',
            'Evaluation Date' => function($item) {
                return $item->evaluation_date->format('Y-m-d');
            },
            'Evaluator Name' => 'evaluator_name',
            'Position' => 'position',
            'Department' => 'department',
            'Employment Date' => function($item) {
                return $item->employment_date ? $item->employment_date->format('Y-m-d') : '';
            },
            'Evaluation Type' => 'evaluation_type',
            'Overall Score' => 'overall_score',
            'Job Knowledge' => 'job_knowledge',
            'Work Quality' => 'work_quality',
            'Initiative' => 'initiative',
            'Communication' => 'communication',
            'Dependability' => 'dependability',
            'Attendance' => 'attendance',
            'Strengths' => 'strengths',
            'Areas for Improvement' => 'areas_for_improvement',
            'Comments' => 'comments',
            'Status' => 'status',
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
            'Updated At' => function($item) {
                return $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToXls($data, $headers, 'evaluation_records_' . date('Y-m-d') . '.xls');
    }
}
