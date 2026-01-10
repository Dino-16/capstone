<?php

namespace App\Exports\Recruitment;

use App\Models\Recruitment\JobListing;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class JobPostsExport implements FromQuery
{
    use Exportable;

    public function query()
    {
        return JobListing::query()->where('status', 'Active');
    }
}
