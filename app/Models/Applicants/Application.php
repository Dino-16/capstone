<?php

namespace App\Models\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\FilteredResume;

class Application extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'applied_position', // Storing the string name of the job
        'department',
        'first_name',
        'middle_name',
        'last_name',
        'suffix_name',
        'email',
        'phone',
        'region',
        'province',
        'city',
        'barangay',
        'house_street',
        'resume_path',
        'status',
        'agreed_to_terms',
    ];

    public function filteredResume(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        // Explicitly use the correct class path
        return $this->hasOne(\App\Models\Applicants\FilteredResume::class);
    }
}