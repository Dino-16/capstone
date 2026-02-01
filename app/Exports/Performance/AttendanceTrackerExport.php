<?php

namespace App\Exports\Performance;

use App\Services\ExportService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceTrackerExport
{
    protected array $attendanceRecords;

    public function __construct(array $attendanceRecords)
    {
        $this->attendanceRecords = $attendanceRecords;
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Employee Name',
            'Position',
            'Date',
            'Time In',
            'Time Out',
            'Total Hours',
            'Status',
            'Location',
        ];

        $data = collect($this->attendanceRecords)->map(function ($record) {
            $name = trim(($record['employee']['first_name'] ?? '') . ' ' . ($record['employee']['last_name'] ?? ''));
            $position = $record['employee']['position'] ?? '';
            $date = isset($record['date']) ? Carbon::parse($record['date'])->format('M d, Y') : '';
            $timeIn = isset($record['clock_in_time']) ? Carbon::parse($record['clock_in_time'])->format('h:i A') : '-';
            $timeOut = isset($record['clock_out_time']) ? Carbon::parse($record['clock_out_time'])->format('h:i A') : '-';
            $totalHours = $record['total_hours'] ?? '0.00';
            $status = ucfirst(str_replace('_', ' ', $record['status'] ?? 'unknown'));
            $location = $record['location'] ?? 'N/A';

            return [
                'Employee Name' => $name,
                'Position' => $position,
                'Date' => $date,
                'Time In' => $timeIn,
                'Time Out' => $timeOut,
                'Total Hours' => $totalHours,
                'Status' => $status,
                'Location' => $location,
            ];
        });

        return ExportService::exportToXls($data, $headers, 'attendance_tracker_' . date('Y-m-d') . '.xls');
    }
}
