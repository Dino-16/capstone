<?php

namespace App\Exports\Recognition;

use App\Models\Recognition\Reward;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class RewardsExport implements FromQuery
{
    use Exportable;

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return Reward::query();
    }
}
