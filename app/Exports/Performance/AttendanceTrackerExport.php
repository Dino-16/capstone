<?php

namespace App\Exports\Performance;

use App\Exports\Traits\CsvExportable;
use Carbon\Carbon;

class AttendanceTrackerExport
{
    use CsvExportable;

    protected $attendanceRecords;

    public function __construct(array $attendanceRecords)
    {
        $this->attendanceRecords = $attendanceRecords;
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Position',
            'Department',
            'Date',
            'Time In',
            'Time Out',
            'Total Hours',
            'Status',
            'Location',
        ];
    }

    public function rows(): array
    {
        return collect($this->attendanceRecords)->map(function ($record) {
            return [
                $record['employee_name'] ?? ($record['first_name'] . ' ' . ($record['last_name'] ?? '')),
                $record['position'] ?? 'N/A',
                $record['department'] ?? 'N/A',
                isset($record['date']) ? Carbon::parse($record['date'])->format('M d, Y') : 'N/A',
                $record['time_in'] ?? 'N/A',
                $record['time_out'] ?? 'N/A',
                $record['total_hours'] ?? 'N/A',
                $record['status'] ?? 'N/A',
                $record['location'] ?? 'N/A',
            ];
        })->toArray();
    }
}
