<?php

namespace App\Exports\Recruitment;

use App\Models\Recruitment\JobListing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JobPostsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return JobListing::where('status', 'Active')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Position',
            'Department',
            'Employment Type',
            'Work Arrangement',
            'Location',
            'Expiration Date',
            'Status',
            'Description',
            'Qualifications',
            'Posted Date',
            'Created At',
            'Updated At'
        ];
    }

    public function map($listing): array
    {
        return [
            $listing->id,
            $listing->position,
            $listing->department,
            $listing->type,
            $listing->arrangement,
            $listing->location,
            $listing->expiration_date ? $listing->expiration_date->format('M d, Y') : 'N/A',
            $listing->status,
            strip_tags($listing->description), // Clean HTML if any
            strip_tags($listing->qualifications),
            $listing->created_at ? $listing->created_at->format('M d, Y') : '',
            $listing->created_at ? $listing->created_at->format('M d, Y h:i A') : '',
            $listing->updated_at ? $listing->updated_at->format('M d, Y h:i A') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
