<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'employee_name',
        'email',
        'evaluation_date',
        'evaluator_name',
        'position',
        'department',
        'employment_date',
        'evaluation_type',
        'performance_areas',
        'overall_score',
        'job_knowledge',
        'work_quality',
        'initiative',
        'communication',
        'dependability',
        'attendance',
        'strengths',
        'areas_for_improvement',
        'comments',
        'employee_comments',
        'status',
        'evaluator_signature',
        'employee_signature',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'employment_date' => 'date',
        'overall_score' => 'integer',
        'job_knowledge' => 'integer',
        'work_quality' => 'integer',
        'initiative' => 'integer',
        'communication' => 'integer',
        'dependability' => 'integer',
        'attendance' => 'integer',
    ];

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Pending' => '<span class="badge bg-warning">Pending</span>',
            'Completed' => '<span class="badge bg-success">Completed</span>',
            'In Progress' => '<span class="badge bg-info">In Progress</span>',
            'Cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . $this->status . '</span>';
    }
}
