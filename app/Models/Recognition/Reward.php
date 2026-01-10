<?php

namespace App\Models\Recognition;

use Illuminate\Database\Eloquent\Model;
use App\Models\Recognition\GiveReward;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'value',
        'type',
        'is_active',
        'status',
        'points_required',
        'icon',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
        'points_required' => 'integer',
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
            'recognition' => '<span class="badge bg-warning">Recognition</span>',
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
