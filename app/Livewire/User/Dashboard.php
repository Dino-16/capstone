<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Recruitment\Requisition;
use App\Models\Recruitment\JobListing;
use App\Models\Applicants\Application;
use App\Models\Applicants\Candidate;
use App\Models\Onboarding\DocumentChecklist;
use App\Models\Onboarding\Orientation;
use App\Models\Performance\Evaluation;
use App\Models\Recognition\Reward;
use App\Models\Recognition\GiveReward;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        // Basic Counts
        $employeeCount = $this->getEmployeeCount();
        
        $statusCounts = [
            'requisitions'  => Requisition::where('status', 'Pending')->count(),
            'jobs'          => JobListing::where('status', 'Active')->count(),
            'applications'  => Application::count(),
            'employees'     => $employeeCount,
        ];

        // Onboarding Stats
        $onboardingStats = [
            'document_checklists' => DocumentChecklist::count(),
            'orientations' => Orientation::count(),
            'pending_documents' => 0, // DocumentChecklist doesn't have status column
            'completed_orientations' => Orientation::where('status', 'completed')->count(),
        ];

        // Performance Stats
        $performanceStats = [
            'evaluations' => Evaluation::count(),
            'pending_evaluations' => Evaluation::where('status', 'Pending')->count(),
            'completed_evaluations' => Evaluation::where('status', 'Completed')->count(),
            'average_score' => round(Evaluation::avg('overall_score') ?? 0, 1),
        ];

        // Recognition Stats
        $recognitionStats = [
            'rewards' => Reward::count(),
            'active_rewards' => Reward::where('status', 'active')->count(),
            'rewards_given' => GiveReward::count(),
            'pending_rewards' => GiveReward::where('status', 'pending')->count(),
        ];

        // Monthly Data for Charts
        $monthlyData = $this->getMonthlyData();

        // Department Distribution
        $departmentData = $this->getDepartmentData();

        // Recent Activities
        $recentActivities = $this->getRecentActivities();

        return view('livewire.user.dashboard', [
            'statusCounts' => $statusCounts,
            'onboardingStats' => $onboardingStats,
            'performanceStats' => $performanceStats,
            'recognitionStats' => $recognitionStats,
            'monthlyData' => $monthlyData,
            'departmentData' => $departmentData,
            'recentActivities' => $recentActivities,
        ])->layout('layouts.app');
    }

    private function getMonthlyData()
    {
        $months = [];
        $applications = [];
        $evaluations = [];
        $rewards = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M');
            
            $applications[] = Application::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)->count();
            
            $evaluations[] = Evaluation::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)->count();
            
            $rewards[] = GiveReward::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)->count();
        }

        // Calculate totals for percentage calculation
        $totalApplications = array_sum($applications);
        $totalEvaluations = array_sum($evaluations);
        $totalRewards = array_sum($rewards);

        // Calculate percentages (avoid division by zero)
        $applicationsPercent = $totalApplications > 0 
            ? array_map(fn($val) => round(($val / $totalApplications) * 100, 1), $applications) 
            : array_fill(0, 6, 0);
        $evaluationsPercent = $totalEvaluations > 0 
            ? array_map(fn($val) => round(($val / $totalEvaluations) * 100, 1), $evaluations) 
            : array_fill(0, 6, 0);
        $rewardsPercent = $totalRewards > 0 
            ? array_map(fn($val) => round(($val / $totalRewards) * 100, 1), $rewards) 
            : array_fill(0, 6, 0);

        return [
            'months' => $months,
            'applications' => $applications,
            'evaluations' => $evaluations,
            'rewards' => $rewards,
            'applicationsPercent' => $applicationsPercent,
            'evaluationsPercent' => $evaluationsPercent,
            'rewardsPercent' => $rewardsPercent,
            'totalApplications' => $totalApplications,
            'totalEvaluations' => $totalEvaluations,
            'totalRewards' => $totalRewards,
        ];
    }

    private function getDepartmentData()
    {
        try {
            $response = Http::timeout(10)->get('http://hr4.jetlougetravels-ph.com/api/employees');
            
            if (!$response->successful()) {
                return ['counts' => ['No Data' => 100], 'percentages' => ['No Data' => 100], 'total' => 0];
            }
            
            $responseData = $response->json();
            $departments = [];
            
            // The API returns {'success': ..., 'data': [...]} structure
            $employees = $responseData['data'] ?? $responseData;
            
            if (is_array($employees)) {
                foreach ($employees as $employee) {
                    // Handle department being either a string or an object with 'name' property
                    $dept = 'Unknown';
                    
                    if (isset($employee['department'])) {
                        if (is_array($employee['department']) && isset($employee['department']['name'])) {
                            $dept = $employee['department']['name'];
                        } elseif (is_string($employee['department']) && !empty($employee['department'])) {
                            $dept = $employee['department'];
                        }
                    }
                    
                    // Skip if department is empty or null
                    if (empty($dept) || $dept === '') {
                        $dept = 'Unassigned';
                    }
                    
                    $departments[$dept] = ($departments[$dept] ?? 0) + 1;
                }
            }

            // Sort by count descending
            arsort($departments);
            
            if (empty($departments)) {
                return ['counts' => ['No Departments' => 100], 'percentages' => ['No Departments' => 100], 'total' => 0];
            }

            // Calculate total and percentages
            $total = array_sum($departments);
            $percentages = [];
            foreach ($departments as $dept => $count) {
                $percentages[$dept] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            }

            return [
                'counts' => $departments,
                'percentages' => $percentages,
                'total' => $total,
            ];
        } catch (\Exception $e) {
            return ['counts' => ['Error Loading Data' => 100], 'percentages' => ['Error Loading Data' => 100], 'total' => 0];
        }
    }

    private function getRecentActivities()
    {
        return [
            'recent_applications' => Application::latest()->take(3)->get(),
            'recent_evaluations' => Evaluation::latest()->take(3)->get(),
            'recent_rewards' => GiveReward::with('reward')->latest()->take(3)->get(),
        ];
    }

    private function getEmployeeCount()
    {
        try {
            $response = Http::timeout(10)->get('http://hr4.jetlougetravels-ph.com/api/employees');
            
            if (!$response->successful()) {
                return 0;
            }
            
            $data = $response->json();
            
            // Check if API returns paginated response with 'total' key
            if (isset($data['total'])) {
                return $data['total'];
            }
            
            // Check if it's a paginated response with 'data' array
            if (isset($data['data']) && is_array($data['data'])) {
                // If there's a total in the paginated response, use it
                if (isset($data['total'])) {
                    return $data['total'];
                }
                // Otherwise count the data array (but this might be just one page)
                return count($data['data']);
            }
            
            // If it's a direct array of employees
            if (is_array($data)) {
                return count($data);
            }
            
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }


}
