<?php

namespace App\Models\Recognition;

use Illuminate\Database\Eloquent\Model;

class GiveReward extends Model
{
    protected $table = 'give_rewards';
    protected $fillable = [
        'reward_id',
        'employee_name',
        'employee_email',
        'employee_position',
        'employee_department',
        'given_date',
        'given_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'given_date' => 'date',
        'reward_id' => 'integer',
    ];

    public function reward()
    {
        return $this->belongsTo(Reward::class, 'reward_id');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }
}
