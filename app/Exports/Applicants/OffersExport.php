<?php

namespace App\Exports\Applicants;

use App\Models\Applicants\Candidate;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OffersExport
{
    public function export(): StreamedResponse
    {
        $query = Candidate::query()
            ->whereIn('status', ['passed', 'hired'])
            ->where('interview_result', 'passed')
            ->latest();
        
        $headers = [
            'ID',
            'Name',
            'Email',
            'Position',
            'Department',
            'Contract Status',
            'Contract Sent At',
            'Contract Signed At',
            'Status'
        ];

        $mappings = [
            'ID' => 'id',
            'Name' => 'candidate_name',
            'Email' => 'candidate_email',
            'Position' => 'applied_position',
            'Department' => 'department',
            'Contract Status' => 'contract_status',
            'Contract Sent At' => function($item) {
                return $item->contract_sent_at ? $item->contract_sent_at->format('Y-m-d H:i') : 'N/A';
            },
            'Contract Signed At' => function($item) {
                return $item->contract_signed_at ? $item->contract_signed_at->format('Y-m-d H:i') : 'N/A';
            },
            'Status' => 'status',
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToXls($data, $headers, 'offers_' . date('Y-m-d') . '.xls');
    }
}
