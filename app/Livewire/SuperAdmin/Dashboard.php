<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use App\Models\Recruitment\Requisition;
use App\Models\Recruitment\JobListing;
use App\Models\Applicants\Application;
use App\Models\Onboarding\DocumentChecklist;
use App\Models\Onboarding\Orientation;
use App\Models\Performance\Evaluation;
use App\Models\Recognition\Reward;
use App\Models\Recognition\GiveReward;
use App\Models\Applicants\Candidate;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public function render()
    {
        // Fetch User Stats for the Graph
        $userStats = $this->getUserStats();

        // Basic Counts (Keeping existing logic just in case, though unused in view)
        $employeeCount = $this->getEmployeeCount();
        
        $statusCounts = [
            'requisitions'  => Requisition::where('status', 'Pending')->count(),
            'jobs'          => JobListing::where('status', 'Active')->count(),
            'applications'  => Application::count(),
            'employees'     => $employeeCount,
        ];

        return view('livewire.superadmin.dashboard', [
            'userStats' => $userStats,
            'statusCounts' => $statusCounts,
            'aiAnalytics' => $this->getAiAnalytics(),
            // ... (keeping other variables if needed, but for now just add userStats)
        ])->layout('layouts.superadmin');
    }

    private function getUserStats()
    {
        try {
            $response = Http::withoutVerifying()->timeout(5)->get('https://hr4.jetlougetravels-ph.com/api/accounts');
            
            if ($response->successful()) {
                $data = $response->json();
                $systemAccounts = count($data['data']['system_accounts'] ?? []);
                $essAccounts = count($data['data']['ess_accounts'] ?? []);
                $total = $systemAccounts + $essAccounts;
                
                // Calculate percentages
                $systemPercent = $total > 0 ? round(($systemAccounts / $total) * 100, 1) : 0;
                $essPercent = $total > 0 ? round(($essAccounts / $total) * 100, 1) : 0;
                
                return [
                    'system' => $systemAccounts,
                    'ess' => $essAccounts,
                    'total' => $total,
                    'systemPercent' => $systemPercent,
                    'essPercent' => $essPercent,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch user stats: ' . $e->getMessage());
        }

        return ['system' => 0, 'ess' => 0, 'total' => 0, 'systemPercent' => 0, 'essPercent' => 0];
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

        return [
            'months' => $months,
            'applications' => $applications,
            'evaluations' => $evaluations,
            'rewards' => $rewards,
        ];
    }

    private function getDepartmentData()
    {
        try {
            $response = Http::timeout(10)->get('http://hr4.jetlougetravels-ph.com/api/employees');
            
            if (!$response->successful()) {
                return ['No Data' => 1];
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
            
            return empty($departments) ? ['No Departments' => 1] : $departments;
        } catch (\Exception $e) {
            return ['Error Loading Data' => 1];
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
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getAiAnalytics()
    {
        // Qualification Distribution
        $candidates = Candidate::all();
        
        $distribution = [
            'Exceptional' => 0,
            'Highly Qualified' => 0,
            'Qualified' => 0,
            'Moderately Qualified' => 0,
            'Not Qualified' => 0,
        ];
        
        // Map keys for chart labels
        $distributionKeys = [
            'Exceptional' => 'Exceptional (90-100)',
            'Highly Qualified' => 'Highly Qualified (80-89)',
            'Qualified' => 'Qualified (70-79)',
            'Moderately Qualified' => 'Moderately Qualified (60-69)',
            'Not Qualified' => 'Not Qualified (<60)',
        ];

        $skillsCount = [];

        foreach ($candidates as $candidate) {
            $score = $candidate->rating_score;
            if ($score >= 90) $distribution['Exceptional']++;
            elseif ($score >= 80) $distribution['Highly Qualified']++;
            elseif ($score >= 70) $distribution['Qualified']++;
            elseif ($score >= 60) $distribution['Moderately Qualified']++;
            else $distribution['Not Qualified']++;

            // Skills
            if ($candidate->skills) {
                $skills = is_array($candidate->skills) ? $candidate->skills : json_decode($candidate->skills, true);
                if (is_array($skills)) {
                    foreach ($skills as $skill) {
                        $skillName = trim($skill);
                        if (!empty($skillName)) {
                            if (!isset($skillsCount[$skillName])) {
                                $skillsCount[$skillName] = 0;
                            }
                            $skillsCount[$skillName]++;
                        }
                    }
                }
            }
        }

        arsort($skillsCount);
        $topSkills = array_slice($skillsCount, 0, 5);

        return [
            'average_score' => round($candidates->avg('rating_score') ?? 0, 1),
            'distribution' => array_combine(array_values($distributionKeys), array_values($distribution)),
            'top_skills' => $topSkills,
            'total_candidates' => $candidates->count(),
        ];
    }
}
