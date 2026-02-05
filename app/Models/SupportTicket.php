<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'requester_name',
        'requester_email',
        'requester_position',
        'subject',
        'description',
        'priority',
        'status',
        'admin_notes',
    ];
    //
}
