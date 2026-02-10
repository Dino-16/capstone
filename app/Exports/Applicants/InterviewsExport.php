<?php

namespace App\Exports\Applicants;

use App\Models\Applicants\Candidate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InterviewsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Candidate::whereIn('status', ['interview_ready', 'interviewed'])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
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
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->candidate_name,
            $item->candidate_email,
            $item->applied_position,
            $item->rating_score,
            $item->interview_schedule ? $item->interview_schedule->format('M d, Y h:i A') : 'N/A',
            $item->status,
            $item->interview_total_score,
            $item->interview_result,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
