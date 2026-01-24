<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Recruitment\Requisition;
use App\Models\Recruitment\JobListing;
use App\Models\Applicants\Application;
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
        $statusCounts = [
            'requisitions'  => Requisition::where('status', 'Pending')->count(),
            'jobs'          => JobListing::where('status', 'Active')->count(),
            'applications'  => Application::count(),
            'employees'     => count(Http::get('http://hr4.jetlougetravels-ph.com/api/employees')->json()),
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

        return [
            'months' => $months,
            'applications' => $applications,
            'evaluations' => $evaluations,
            'rewards' => $rewards,
        ];
    }

    private function getDepartmentData()
    {
        $employees = Http::get('http://hr4.jetlougetravels-ph.com/api/employees')->json();
        $departments = [];
        
        if (is_array($employees)) {
            foreach ($employees as $employee) {
                $dept = $employee['department'] ?? 'Unknown';
                $departments[$dept] = ($departments[$dept] ?? 0) + 1;
            }
        }

        return $departments;
    }

    private function getRecentActivities()
    {
        return [
            'recent_applications' => Application::latest()->take(3)->get(),
            'recent_evaluations' => Evaluation::latest()->take(3)->get(),
            'recent_rewards' => GiveReward::with('reward')->latest()->take(3)->get(),
        ];
    }
}
