<?php

namespace App\Exports\Applicants;

use App\Exports\Traits\CsvExportable;
use App\Models\Applicants\Candidate;

class OffersExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Applied Position',
            'Department',
            'Status',
            'Contract Status',
            'Contract Sent At',
            'Contract Approved At',
            'Created At',
        ];
    }

    public function rows(): array
    {
        return Candidate::whereIn('status', ['passed', 'hired'])
            ->where('interview_result', 'passed')
            ->get()
            ->map(function ($candidate) {
                return [
                    $candidate->candidate_name,
                    $candidate->candidate_email,
                    $candidate->applied_position,
                    $candidate->department,
                    ucfirst($candidate->status),
                    ucfirst($candidate->contract_status ?? 'Pending'),
                    $candidate->contract_sent_at ? $candidate->contract_sent_at->format('M d, Y') : 'N/A',
                    $candidate->contract_approved_at ? $candidate->contract_approved_at->format('M d, Y') : 'N/A',
                    $candidate->created_at?->format('Y-m-d H:i:s'),
                ];
            })->toArray();
    }
}
