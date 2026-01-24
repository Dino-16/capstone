<?php

namespace App\Models\Applicants;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $table = 'candidates';
    
    protected $fillable = [
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'candidate_sex',
        'candidate_birth_date',
        'candidate_civil_status',
        'candidate_age',
        'candidate_region',
        'candidate_province',
        'candidate_city',
        'candidate_barangay',
        'candidate_house_street',
        'skills',
        'experience',
        'education',
        'resume_url',
        'status',
        'interview_schedule',
        'created_at',
        'updated_at',
 
    ];
    
    protected $casts = [
        'skills' => 'array',
        'experience' => 'array',
        'education' => 'array',
        'interview_schedule' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

