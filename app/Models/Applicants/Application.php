<?php

namespace App\Models\Applicants;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'applied_position',
        'department',

        // Personal Info
        'first_name',
        'middle_name',
        'last_name',
        'suffix_name',

        // Contact & Address
        'email',
        'phone',
        'region',
        'province',
        'city',
        'barangay',
        'house_street',

        // Resume
        'resume_path',

        // Application status
        'status',
        'agreed_to_terms',
    ];

    /**
     * Relationship:
     * One Application → One Filtered Resume (AI analyzed)
     */
    public function filteredResume(): HasOne
    {
        return $this->hasOne(FilteredResume::class, 'application_id');
    }
}
