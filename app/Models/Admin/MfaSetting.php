<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MfaSetting extends Model
{
    use HasFactory;

    protected $fillable = ['is_global_enabled', 'hr_staff_enabled', 'hr_manager_enabled'];
}
