<?php

namespace App\Exports\Performance;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendanceTrackerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $attendanceRecords;

    public function __construct(array $attendanceRecords)
    {
        $this->attendanceRecords = $attendanceRecords;
    }

    public function collection()
    {
        return collect($this->attendanceRecords);
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Position',
            'Date',
            'Time In',
            'Time Out',
            'Total Hours',
            'Status',
            'Location',
        ];
    }

    public function map($record): array
    {
        $name = trim(($record['employee']['first_name'] ?? '') . ' ' . ($record['employee']['last_name'] ?? ''));
        $position = $record['employee']['position'] ?? '';
        $date = isset($record['date']) ? Carbon::parse($record['date'])->format('M d, Y') : '';
        $timeIn = isset($record['clock_in_time']) ? Carbon::parse($record['clock_in_time'])->format('h:i A') : '-';
        $timeOut = isset($record['clock_out_time']) ? Carbon::parse($record['clock_out_time'])->format('h:i A') : '-';
        $totalHours = $record['total_hours'] ?? '0.00';
        $status = ucfirst(str_replace('_', ' ', $record['status'] ?? 'unknown'));
        $location = $record['location'] ?? 'N/A';

        return [
            $name,
            $position,
            $date,
            $timeIn,
            $timeOut,
            $totalHours,
            $status,
            $location,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
