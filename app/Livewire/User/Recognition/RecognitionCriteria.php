<?php

namespace App\Livewire\User\Recognition;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RecognitionCriteria extends Component
{
    public $attendanceData = [];
    public $loading = false;
    public $error = null;

    public function mount()
    {
        $this->loadAttendanceData();
    }

    public function loadAttendanceData()
    {
        $this->loading = true;
        $this->error = null;

        try {
            // Clear the cache to get fresh data
            Cache::forget('attendance_data');
            
            // Get fresh data without caching for debugging
            $response = Http::timeout(30)->withoutVerifying()->get('https://hr3.jetlougetravels-ph.com/api/attendance');

            if ($response->successful()) {
                $data = $response->json();
                
                // Use the raw attendance data directly (all individual records)
                $this->attendanceData = $data['data'] ?? [];
            } else {
                $this->error = 'Failed to fetch attendance data. Status: ' . $response->status();
            }
        } catch (\Exception $e) {
            $this->error = 'Error fetching attendance data: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    private function processAttendanceData($attendanceRecords)
    {
        $employeeStats = [];

        foreach ($attendanceRecords as $record) {
            $employee = $record['employee'] ?? null;
            if (!$employee) continue;

            $employeeId = $employee['id'];
            $employeeName = trim(($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''));
            $email = $employee['email'] ?? 'N/A';

            if (!isset($employeeStats[$employeeId])) {
                $employeeStats[$employeeId] = [
                    'id' => $employeeId,
                    'name' => $employeeName ?: 'N/A',
                    'email' => $email,
                    'total_days' => 0,
                    'present_days' => 0,
                    'total_hours' => 0,
                    'overtime_hours' => 0,
                    'position' => $employee['position'] ?? 'N/A',
                    'department' => $employee['department'] ?? 'N/A',
                ];
            }

            // Increment total days
            $employeeStats[$employeeId]['total_days']++;

            // Count as present if they have clock_in_time
            if (!empty($record['clock_in_time'])) {
                $employeeStats[$employeeId]['present_days']++;
            }

            // Add hours
            $employeeStats[$employeeId]['total_hours'] += floatval($record['total_hours'] ?? 0);
            $employeeStats[$employeeId]['overtime_hours'] += floatval($record['overtime_hours'] ?? 0);
        }

        // Convert to array and sort by name
        return array_values($employeeStats);
    }

    public function render()
    {
        return view('livewire.user.recognition.recognition-criteria')->layout('layouts.app');
    }
}
