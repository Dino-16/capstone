<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';
    
    protected $fillable = [
        'report_name',
        'report_type',
        'report_file',
    ];
}
