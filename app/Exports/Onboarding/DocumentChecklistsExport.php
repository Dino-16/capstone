<?php

namespace App\Exports\Onboarding;

use App\Exports\Traits\CsvExportable;
use App\Models\Onboarding\DocumentChecklist;

class DocumentChecklistsExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Employee Name',
            'Resume',
            'Medical Certificate',
            'Valid Government ID',
            'Submission Date',
            'Created At',
            'Updated At',
        ];
    }

    public function rows(): array
    {
        return DocumentChecklist::all()->map(function ($checklist) {
            return [
                $checklist->employee_name,
                $checklist->getDocumentStatus('resume'),
                $checklist->getDocumentStatus('medical_certificate'),
                $checklist->getDocumentStatus('valid_government_id'),
                $checklist->submission_date ?? 'N/A',
                $checklist->created_at?->format('Y-m-d H:i:s'),
                $checklist->updated_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
