<?php

namespace App\Exports\Applicants;

use App\Models\Applicants\Application;
use App\Models\Applicants\FilteredResume;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicationsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Application::latest()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Applied Position',
            'Department',
            'Status',
            'AI Score',
            'Qualification Status',
            'Created At'
        ];
    }

    public function map($item): array
    {
        $resume = FilteredResume::where('application_id', $item->id)->first();
        
        return [
            $item->id,
            $item->first_name . ' ' . $item->last_name,
            $item->email,
            $item->phone,
            $item->applied_position,
            $item->department,
            $item->status,
            $resume ? $resume->rating_score : 'N/A',
            $resume ? $resume->qualification_status : 'N/A',
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
