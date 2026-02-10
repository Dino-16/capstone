<?php

namespace App\Exports\Applicants;

use App\Models\Applicants\Candidate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CandidatesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Candidate::whereIn('status', ['scheduled', 'interview_ready'])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Position',
            'Department',
            'Schedule',
            'Status',
            'Created At'
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->candidate_name,
            $item->candidate_email,
            $item->candidate_phone,
            $item->applied_position,
            $item->department,
            $item->interview_schedule ? $item->interview_schedule->format('M d, Y h:i A') : 'Not Scheduled',
            $item->status,
            $item->created_at ? $item->created_at->format('M d, Y h:i A') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
