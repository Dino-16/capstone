<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MfaLog extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'role', 'ip_address', 'user_agent', 'action', 'status'];
}
