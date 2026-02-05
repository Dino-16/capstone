<?php

namespace App\Services;

use App\Models\Performance\Evaluation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PerformanceTrackerService
{
    public static function getPendingEvaluationsCount()
    {
        return Cache::remember('pending_evaluations_count', 300, function () {
            try {
                $response = Http::timeout(5)->withoutVerifying()->get('http://hr4.jetlougetravels-ph.com/api/employees', [
                    'per_page' => 1000
                ]);

                if (!$response->successful()) {
                    return 0;
                }

                $employeesData = $response->json()['data'] ?? $response->json();
                if (empty($employeesData)) {
                    return 0;
                }

                $pendingCount = 0;
                $currentDate = Carbon::now();

                // Get all completed evaluations once to avoid N+1 inside the loop
                $completedEvaluations = Evaluation::where('status', 'Completed')
                    ->get(['employee_name', 'evaluation_date'])
                    ->groupBy('employee_name')
                    ->map(function ($items) {
                        return $items->map(function ($item) {
                            return $item->evaluation_date->format('Y-m');
                        })->toArray();
                    });

                foreach ($employeesData as $employee) {
                    $hireDateString = $employee['date_hired'] ?? $employee['created_at'] ?? null;
                    if (!$hireDateString) continue;

                    $hireDate = Carbon::parse($hireDateString);
                    $monthsDiff = $hireDate->diffInMonths($currentDate);
                    $employeeName = $employee['full_name'] ?? ($employee['first_name'] . ' ' . $employee['last_name']) ?? 'N/A';
                    
                    $completedMonths = $completedEvaluations->get($employeeName, []);

                    // Check months from hire date + 1 until now
                    for ($i = 1; $i <= $monthsDiff; $i++) {
                        $evalDate = $hireDate->copy()->addMonths($i);
                        $evalMonth = $evalDate->format('Y-m');
                        
                        if (!in_array($evalMonth, $completedMonths)) {
                            $pendingCount++;
                        }
                    }
                    
                    // Also check current month
                    $currentEvalDate = $hireDate->copy()->addMonths($monthsDiff + 1);
                    if ($currentEvalDate->isCurrentMonth()) {
                        $evalMonth = $currentEvalDate->format('Y-m');
                        if (!in_array($evalMonth, $completedMonths)) {
                            $pendingCount++;
                        }
                    }
                }

                return $pendingCount;
            } catch (\Exception $e) {
                return 0;
            }
        });
    }
}
