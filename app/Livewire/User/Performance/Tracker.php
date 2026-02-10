<?php

namespace App\Livewire\User\Performance;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

use App\Models\Performance\Evaluation;
use App\Exports\Performance\PerformanceTrackerExport;
use App\Exports\Performance\AttendanceTrackerExport;
use App\Livewire\Traits\RequiresPasswordVerification;

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
    
    // Modal properties
    public $showScheduleModal = false;
    public $scheduleEmployee = null;
    
    // Search and filter properties
    public $search = '';
    public $nextEvaluationFilter = '';
    
    // Computed filtered employees
    public function getFilteredEmployeesProperty()
    {
        $filtered = collect($this->employees);
        
        // Apply search filter
        if (!empty($this->search)) {
            $search = strtolower($this->search);
            $filtered = $filtered->filter(function ($employee) use ($search) {
                return str_contains(strtolower($employee['name'] ?? ''), $search)
                    || str_contains(strtolower($employee['position'] ?? ''), $search)
                    || str_contains(strtolower($employee['department'] ?? ''), $search)
                    || str_contains(strtolower($employee['email'] ?? ''), $search);
            });
        }
        
        // Apply next evaluation filter
        if (!empty($this->nextEvaluationFilter)) {
            $filtered = $filtered->filter(function ($employee) {
                $nextEval = collect($employee['monthly_evaluations'] ?? [])
                    ->where('status', '!=', 'completed')
                    ->first();
                    
                if (!$nextEval) {
                    return $this->nextEvaluationFilter === 'caught_up';
                }
                
                return $nextEval['status'] === $this->nextEvaluationFilter;
            });
        }
        
        return $filtered->values()->toArray();
    }
    
    public function exportData()
    {
        $export = new PerformanceTrackerExport($this->filteredEmployees);
        return $export->export();
    }
    
    // Attendance search and filter properties
    public $attendanceSearch = '';
    public $attendanceStatusFilter = '';
    
    // Computed filtered attendance records
    public function getFilteredAttendanceProperty()
    {
        $filtered = collect($this->attendanceRecords);
        
        // Apply search filter
        if (!empty($this->attendanceSearch)) {
            $search = strtolower($this->attendanceSearch);
            $filtered = $filtered->filter(function ($record) use ($search) {
                $firstName = strtolower($record['employee']['first_name'] ?? '');
                $lastName = strtolower($record['employee']['last_name'] ?? '');
                $position = strtolower($record['employee']['position'] ?? '');
                $location = strtolower($record['location'] ?? '');
                
                return str_contains($firstName, $search)
                    || str_contains($lastName, $search)
                    || str_contains($firstName . ' ' . $lastName, $search)
                    || str_contains($position, $search)
                    || str_contains($location, $search);
            });
        }
        
        // Apply status filter
        if (!empty($this->attendanceStatusFilter)) {
            $filtered = $filtered->filter(function ($record) {
                return strtolower($record['status'] ?? '') === strtolower($this->attendanceStatusFilter);
            });
        }
        
        return $filtered->values()->toArray();
    }
    
    public function exportAttendanceData()
    {
        $export = new AttendanceTrackerExport($this->filteredAttendance);
        return $export->export();
    }

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
                                $employeeName = $employee['full_name'] ?? ($employee['first_name'] . ' ' . $employee['last_name']) ?? 'N/A';
                                
                                $monthlyEvaluations[$monthName] = [
                                    'month' => $monthName,
                                    'evaluation_date' => $evaluationDate->format('Y-m-d'),
                                    'status' => $this->getEvaluationStatus($evaluationDate, $currentDate, $employeeName),
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
                            'raw_hire_date' => $hireDate ? $hireDate->format('Y-m-d') : null,
                            'created_at' => $employee['created_at'] ?? null,
                            'monthly_evaluations' => $monthlyEvaluations,
                            'total_evaluations' => count($monthlyEvaluations),
                            'completed_evaluations' => collect($monthlyEvaluations)->where('status', 'completed')->count(),
                            'pending_evaluations' => collect($monthlyEvaluations)->whereIn('status', ['pending', 'current'])->count(),
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

    private function getEvaluationStatus($evaluationDate, $currentDate, $employeeName)
    {
        // Check if evaluation was actually completed for this employee and month first
        if ($this->isEvaluationCompleted($employeeName, $evaluationDate)) {
            return 'completed';
        }

        if ($evaluationDate->isFuture()) {
            return 'upcoming';
        } elseif ($evaluationDate->isCurrentMonth()) {
            return 'current';
        } else {
            // For first 3 months, don't show as pending (probationary period)
            // Actually let's keep it simple for now as per user request
            return 'pending';
        }
    }

    private function isEvaluationCompleted($employeeName, $evaluationDate)
    {
        if (!$employeeName || $employeeName === 'N/A') {
            return false;
        }
        
        return Evaluation::where('employee_name', $employeeName)
            ->whereMonth('evaluation_date', $evaluationDate->month)
            ->whereYear('evaluation_date', $evaluationDate->year)
            ->where('status', 'Completed')
            ->exists();
    }

    public function openScheduleModal($employeeId)
    {
        $this->scheduleEmployee = collect($this->employees)->firstWhere('id', $employeeId);
        
        if ($this->scheduleEmployee) {
            // Get evaluation count from database
            $this->scheduleEmployee['db_evaluations'] = Evaluation::where('employee_name', $this->scheduleEmployee['name'])->count();
            $this->scheduleEmployee['completed_db_evaluations'] = Evaluation::where('employee_name', $this->scheduleEmployee['name'])
                ->where('status', 'Completed')->count();
        }
        
        $this->showScheduleModal = true;
    }

    public function closeScheduleModal()
    {
        $this->showScheduleModal = false;
        $this->scheduleEmployee = null;
    }

    public function goToEvaluate($employeeId)
    {
        $employee = collect($this->employees)->firstWhere('id', $employeeId);
        
        if ($employee) {
            // Find the next pending evaluation date
            $nextEval = collect($employee['monthly_evaluations'] ?? [])
                ->where('status', '!=', 'completed')
                ->first();
                
            $nextEvalDate = $nextEval ? $nextEval['evaluation_date'] : date('Y-m-d');

            // Store employee data in session for auto-fill
            session()->put('prefill_evaluation', [
                'employeeName' => $employee['name'],
                'email' => $employee['email'],
                'position' => $employee['position'],
                'department' => $employee['department'],
                'employmentDate' => $employee['raw_hire_date'] ?? null,
                'evaluationDate' => $nextEvalDate,
            ]);
            
            return redirect()->route('evaluations');
        }
    }

    
    
    public function render()
    {
        return view('livewire.user.performance.tracker')->layout('layouts.app');
    }
}
