<?php

namespace App\Exports\Onboarding;

use App\Models\Onboarding\OrientationSchedule;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class OrientationSchedulesExport implements FromQuery
{
    use Exportable;

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return OrientationSchedule::query();
    }
}
