<?php

namespace App\Exports;

use App\Models\SupportTicket;
use App\Services\ExportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportTicketsExport
{
    protected $requesterEmail;

    public function __construct($requesterEmail = null)
    {
        $this->requesterEmail = $requesterEmail;
    }

    public function export(): StreamedResponse
    {
        $query = SupportTicket::query()->latest();

        if ($this->requesterEmail) {
            $query->where('requester_email', $this->requesterEmail);
        }
        
        $headers = [
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

        $mappings = [
            'ID' => 'id',
            'Requester Name' => 'requester_name',
            'Requester Email' => 'requester_email',
            'Position' => 'requester_position',
            'Subject' => 'subject',
            'Description' => 'description',
            'Priority' => 'priority',
            'Status' => 'status',
            'Admin Notes' => 'admin_notes',
            'Created At' => function($item) {
                return $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '';
            },
        ];

        $data = ExportService::transformQuery($query, $mappings);
        
        $filename = ($this->requesterEmail ? 'my_tickets_' : 'all_tickets_') . date('Y-m-d') . '.csv';
        
        return ExportService::exportToCsv($data, $headers, $filename);
    }
}
