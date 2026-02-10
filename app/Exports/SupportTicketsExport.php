<?php

namespace App\Exports;

use App\Models\SupportTicket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupportTicketsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $requesterEmail;

    public function __construct($requesterEmail = null)
    {
        $this->requesterEmail = $requesterEmail;
    }

    public function collection()
    {
        $query = SupportTicket::query()->latest();

        if ($this->requesterEmail) {
            $query->where('requester_email', $this->requesterEmail);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Requester Name',
            'Requester Email',
            'Position',
            'Subject',
            'Description',
            'Priority',
            'Status',
            'Admin Notes',
            'Created At'
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->requester_name,
            $item->requester_email,
            $item->requester_position,
            $item->subject,
            $item->description,
            $item->priority,
            $item->status,
            $item->admin_notes,
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
