<?php

namespace App\Exports\Applicants;

use App\Models\Applicants\Candidate;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidatesExport
{
    public function export(): StreamedResponse
    {
        $query = Candidate::query()
            ->whereIn('status', ['scheduled', 'interview_ready'])
            ->latest();
        
        $headers = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Position',
            'Department',
            'AI Rating',
            'Schedule',
            'Status',
            'Created At'
        ];

        $mappings = [
            'ID' => 'id',
            'Name' => 'candidate_name',
            'Email' => 'candidate_email',
            'Phone' => 'candidate_phone',
            'Position' => 'applied_position',
            'Department' => 'department',
            'AI Rating' => 'rating_score',
            'Schedule' => function($item) {
                return $item->interview_schedule ? $item->interview_schedule->format('Y-m-d H:i') : 'Not Scheduled';
            },
            'Status' => 'status',
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToXls($data, $headers, 'candidates_' . date('Y-m-d') . '.xls');
    }
}
