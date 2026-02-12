<?php

namespace App\Exports\Applicants;

use App\Exports\Traits\CsvExportable;
use App\Models\Applicants\Candidate;

class CandidatesExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Applied Position',
            'Department',
            'Status',
            'Interview Schedule',
            'Interview Result',
            'Created At',
        ];
    }

    public function rows(): array
    {
        return Candidate::whereIn('status', ['scheduled', 'interview_ready', 'failed'])
            ->get()
            ->map(function ($candidate) {
                return [
                    $candidate->candidate_name,
                    $candidate->candidate_email,
                    $candidate->candidate_phone,
                    $candidate->applied_position,
                    $candidate->department,
                    ucfirst($candidate->status),
                    $candidate->interview_schedule ? $candidate->interview_schedule->format('M d, Y h:i A') : 'N/A',
                    ucfirst($candidate->interview_result ?? 'Pending'),
                    $candidate->created_at?->format('Y-m-d H:i:s'),
                ];
            })->toArray();
    }
}
