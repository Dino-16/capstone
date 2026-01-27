<?php

namespace App\Livewire\User\Performance;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Tracker extends Component
{
    public $employees = [];
    public $selectedEmployee = null;
    public $email = '';
    public $position = '';
    public $department = '';
    public $hireDate = '';
    public $monthlyEvaluations = [];
    public $loading = false;
    public $attendanceRecords = [];

    public function mount()
    {
        $this->loadEmployees();
        $this->loadAttendance();
    }

    public function loadAttendance()
    {
        try {
            $response = Http::withoutVerifying()->get('https://hr3.jetlougetravels-ph.com/api/attendance');
            
            if ($response->successful()) {
                $json = $response->json();
                
                // Try to find the array of records
                if (isset($json['data']) && is_array($json['data'])) {
                    // Check if it's a paginated response (data inside data)
                    if (isset($json['data']['data']) && is_array($json['data']['data'])) {
                         $this->attendanceRecords = $json['data']['data'];
                    } else {
                         // Standard resource wrapper
                         $this->attendanceRecords = $json['data'];
                    }
                } else {
                    // Direct array response
                    $this->attendanceRecords = is_array($json) ? $json : [];
                }
            } else {
                // Silently fail or log if needed, or add to existing error mechanisms
                // session()->flash('error', 'Failed to load attendance data');
            }
        } catch (\Exception $e) {
            // session()->flash('error', 'Error loading attendance: ' . $e->getMessage());
        }
    }

    public function loadEmployees()
    {
        $this->loading = true;
        
        try {
            // Clear cache to get fresh data
            Cache::forget('employees_data');
            
            // Get fresh data without caching for debugging
            $response = Http::timeout(30)->withoutVerifying()->get('http://hr4.jetlougetravels-ph.com/api/employees', [
                'per_page' => 1000
            ]);


            if ($response->successful()) {
                $json = $response->json();
                $employeesData = $json['data'] ?? $json; // Handle wrapped 'data' key
                
                // Debug: Log the response structure
                if (empty($employeesData)) {
                    session()->flash('error', 'No employee data received from API');
                    $this->employees = [];
                } else {
                    $processedEmployees = [];
                    
                    foreach ($employeesData as $employee) {
                        // Calculate monthly evaluations based on hire date
                        $hireDateString = $employee['date_hired'] ?? $employee['created_at'] ?? null;
                        $hireDate = $hireDateString ? Carbon::parse($hireDateString) : null;
                        $monthlyEvaluations = [];
                        
                        if ($hireDate) {
                            $currentDate = Carbon::now();
                            $monthsDiff = $hireDate->diffInMonths($currentDate);
                            
                            // Generate evaluation schedule from hire date to present
                            // Skip the first month (probationary period)
                            for ($i = 1; $i <= $monthsDiff + 1; $i++) { // +1 to include upcoming if close
                                $evaluationDate = $hireDate->copy()->addMonths($i);
                                $monthName = $evaluationDate->format('F Y');
                                $monthlyEvaluations[$monthName] = [
                                    'month' => $monthName,
                                    'evaluation_date' => $evaluationDate->format('Y-m-d'),
                                    'status' => $this->getEvaluationStatus($evaluationDate, $currentDate, $employee['id'] ?? 0),
                                    'is_past' => $evaluationDate->isPast(),
                                    'is_current' => $evaluationDate->isCurrentMonth(),
                                    'is_future' => $evaluationDate->isFuture()
                                ];
                            }
                        }
                        
                        $processedEmployees[] = [
                            'id' => $employee['id'] ?? null,
                            'name' => $employee['full_name'] ?? ($employee['first_name'] . ' ' . $employee['last_name']) ?? 'N/A',
                            'email' => $employee['email'] ?? 'N/A',
                            'position' => $employee['position'] ?? 'N/A',
                            'department' => $employee['department']['name'] ?? 'N/A',
                            'role' => $employee['role'] ?? 'N/A',
                            'phone' => $employee['phone'] ?? 'N/A',
                            'hire_date' => $hireDate ? $hireDate->format('M d, Y') : 'N/A',
                            'created_at' => $employee['created_at'] ?? null,
                            'monthly_evaluations' => $monthlyEvaluations,
                            'total_evaluations' => count($monthlyEvaluations),
                            'completed_evaluations' => collect($monthlyEvaluations)->where('status', 'completed')->count(),
                            'pending_evaluations' => collect($monthlyEvaluations)->where('status', 'pending')->count(),
                            'upcoming_evaluations' => collect($monthlyEvaluations)->where('status', 'upcoming')->count()
                        ];
                    }
                    
                    $this->employees = $processedEmployees;
                }
            } else {
                session()->flash('error', 'Failed to load employees data');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading employees: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    private function getEvaluationStatus($evaluationDate, $currentDate, $employeeId)
    {
        if ($evaluationDate->isFuture()) {
            return 'upcoming';
        } elseif ($evaluationDate->isCurrentMonth()) {
            return 'current';
        } else {
            // For first 3 months, don't show as pending (probationary period)
            $monthsAgo = $evaluationDate->diffInMonths($currentDate);
            if ($monthsAgo <= 3) {
                return 'pending'; // Show as pending, not completed
            }
            
            // Check if evaluation was actually completed for this employee and month
            return $this->isEvaluationCompleted($employeeId, $evaluationDate) ? 'completed' : 'pending';
        }
    }

    private function isEvaluationCompleted($employeeId, $evaluationDate)
    {
        // This would check against actual evaluation records
        // For now, return false to show all past evaluations as pending
        // TODO: Connect to actual evaluation database
        return false;
    }

    
    
    public function render()
    {
        return view('livewire.user.performance.tracker')->layout('layouts.app');
    }
}
