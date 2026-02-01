<?php

namespace App\Models\Onboarding;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orientation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'email',
        'position',
        'orientation_date',
        'location',
        'facilitator',
        'notes',
        'status',
    ];

    protected $casts = [
        'orientation_date' => 'datetime',
    ];

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'scheduled' => 'bg-warning',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
