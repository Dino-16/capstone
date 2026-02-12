<?php

namespace App\Exports;

use App\Exports\Traits\CsvExportable;
use App\Models\SupportTicket;

class SupportTicketsExport
{
    use CsvExportable;

    protected $requesterEmail;

    public function __construct(?string $requesterEmail = null)
    {
        $this->requesterEmail = $requesterEmail;
    }

    public function headings(): array
    {
        return [
            'Requester Name',
            'Requester Email',
            'Requester Position',
            'Subject',
            'Description',
            'Priority',
            'Status',
            'Admin Notes',
            'Created At',
            'Updated At',
        ];
    }

    public function rows(): array
    {
        $query = SupportTicket::query();

        if ($this->requesterEmail) {
            $query->where('requester_email', $this->requesterEmail);
        }

        return $query->get()->map(function ($ticket) {
            return [
                $ticket->requester_name,
                $ticket->requester_email,
                $ticket->requester_position ?? 'N/A',
                $ticket->subject,
                $ticket->description,
                $ticket->priority,
                $ticket->status,
                $ticket->admin_notes ?? 'N/A',
                $ticket->created_at?->format('Y-m-d H:i:s'),
                $ticket->updated_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
