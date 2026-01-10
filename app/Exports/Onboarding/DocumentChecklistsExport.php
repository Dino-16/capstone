<?php

namespace App\Exports\Onboarding;

use App\Models\Onboarding\DocumentChecklist;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class DocumentChecklistsExport implements FromQuery
{
    use Exportable;

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return DocumentChecklist::query();
    }
}
