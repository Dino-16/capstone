<?php

namespace App\Models\Recognition;

use Illuminate\Database\Eloquent\Model;
use App\Models\Recognition\GiveReward;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'benefits',
        'status',
    ];


    public function rewardGiven()
    {
        return $this->hasMany(GiveReward::class, 'reward_id');
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            'monetary' => '<span class="badge bg-success">Monetary</span>',
            'non_monetary' => '<span class="badge bg-info">Non-Monetary</span>',
        ];

        return $badges[$this->type] ?? '<span class="badge bg-secondary">' . $this->type . '</span>';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => '<span class="badge bg-success">Active</span>',
            'draft' => '<span class="badge bg-warning text-dark">Draft</span>',
            'inactive' => '<span class="badge bg-danger">Inactive</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }
}
