<?php

namespace App\Exports\Onboarding;

use App\Exports\Traits\CsvExportable;
use App\Models\Onboarding\OrientationSchedule;

class OrientationSchedulesExport
{
    use CsvExportable;

    public function headings(): array
    {
        return [
            'Employee Name',
            'Position',
            'Department',
            'Orientation Date',
            'Start Time',
            'End Time',
            'Location',
            'Facilitator',
            'Created At',
            'Updated At',
        ];
    }

    public function rows(): array
    {
        return OrientationSchedule::all()->map(function ($schedule) {
            return [
                $schedule->employee_name,
                $schedule->position ?? 'N/A',
                $schedule->department ?? 'N/A',
                $schedule->orientation_date ? $schedule->orientation_date->format('M d, Y') : 'N/A',
                $schedule->start_time ?? 'N/A',
                $schedule->end_time ?? 'N/A',
                $schedule->location ?? 'N/A',
                $schedule->facilitator ?? 'N/A',
                $schedule->created_at?->format('Y-m-d H:i:s'),
                $schedule->updated_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }
}
