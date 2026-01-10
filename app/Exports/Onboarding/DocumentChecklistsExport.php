<?php

namespace App\Exports\Onboarding;

use App\Models\Onboarding\DocumentChecklist;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentChecklistsExport
{
    public function export(): StreamedResponse
    {
        $query = DocumentChecklist::query();
        
        $headers = [
            'ID',
            'Employee Name',
            'Document Type',
            'Document Name',
            'Status',
            'Submitted Date',
            'Notes',
            'Created At',
            'Updated At'
        ];

        $mappings = [
            'ID' => 'id',
            'Employee Name' => 'employee_name',
            'Document Type' => 'document_type',
            'Document Name' => 'document_name',
            'Status' => 'status',
            'Submitted Date' => function($item) {
                return $item->submitted_date ? $item->submitted_date->format('Y-m-d') : '';
            },
            'Notes' => function($item) {
                return $item->notes ?? '';
            },
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
            'Updated At' => function($item) {
                return $item->updated_at ? $item->updated_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        return ExportService::exportToXls($data, $headers, 'document_checklists_' . date('Y-m-d') . '.xls');
    }
}
