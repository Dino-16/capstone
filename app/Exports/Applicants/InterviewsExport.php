<?php

namespace App\Exports\Applicants;

use App\Exports\Traits\CsvExportable;
use App\Models\Applicants\Candidate;

class InterviewsExport
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
            'Interview Score',
            'Interview Result',
            'Created At',
        ];
    }

    public function rows(): array
    {
        return Candidate::whereIn('status', ['interview_ready', 'interviewed'])
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
                    $candidate->interview_total_score ?? 'N/A',
                    ucfirst($candidate->interview_result ?? 'Pending'),
                    $candidate->created_at?->format('Y-m-d H:i:s'),
                ];
            })->toArray();
    }
}
