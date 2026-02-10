<?php

namespace App\Exports\Applicants;

use App\Models\Applicants\Candidate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OffersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Candidate::whereIn('status', ['passed', 'hired'])
            ->where('interview_result', 'passed')
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
            'Department',
            'Contract Status',
            'Contract Sent At',
            'Contract Approved At',
            'Status'
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->candidate_name,
            $item->candidate_email,
            $item->applied_position,
            $item->department,
            $item->contract_status,
            $item->contract_sent_at ? $item->contract_sent_at->format('M d, Y h:i A') : 'N/A',
            $item->contract_approved_at ? $item->contract_approved_at->format('M d, Y h:i A') : 'N/A',
            $item->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
