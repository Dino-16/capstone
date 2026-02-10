<?php

namespace App\Exports\Applicants;

use App\Models\Applicants\Candidate;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InterviewsExport
{
    public function export(): StreamedResponse
    {
        $query = Candidate::query()
            ->whereIn('status', ['interview_ready', 'interviewed'])
            ->latest();
        
        $headers = [
            'ID',
            'Name',
            'Email',
            'Position',
            'AI Rating',
            'Interview Schedule',
            'Status',
            'Interview Score',
            'Result'
        ];

        $mappings = [
            'ID' => 'id',
            'Name' => 'candidate_name',
            'Email' => 'candidate_email',
            'Position' => 'applied_position',
            'AI Rating' => 'rating_score',
            'Interview Schedule' => function($item) {
                return $item->interview_schedule ? $item->interview_schedule->format('Y-m-d H:i') : 'N/A';
            },
            'Status' => 'status',
            'Interview Score' => 'interview_total_score',
            'Result' => 'interview_result',
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToCsv($data, $headers, 'interviews_' . date('Y-m-d') . '.csv');
    }
}
