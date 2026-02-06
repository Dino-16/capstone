<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recognition\Reward;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rewards = [
            [
                'name' => 'Employee of the Month (EOTM)',
                'description' => "- Consistently exceeds monthly goals or KPIs.\n- Produces high-quality work with minimal supervision.\n- Actively supports colleagues, shares knowledge, and fosters a positive, collaborative environment.\n- Demonstrates excellent attendance, punctuality, and professional integrity.\n- Embodies the core values and mission of the organization.",
                'type' => 'monetary',
                'benefits' => 'Paid Time Off (PTO): A "bonus day" or a few extra hours of leave.',
                'status' => 'active',
            ],
            [
                'name' => 'Years of Service Award',
                'description' => "- Maintains active employment status for the required duration (e.g., 5 or 10 years).\n- Consistently meets performance expectations.\n- Holds no active disciplinary actions during the qualifying period.",
                'type' => 'monetary',
                'benefits' => 'Travel Vouchers: Credits for flights, hotels, or vacation packages to celebrate a major milestone.',
                'status' => 'active',
            ],
            [
                'name' => 'Rising Star Award',
                'description' => "- Demonstrates initiative by identifying new destinations or sustainable travel trends.\n- Achieves strong booking volumes or upselling success early in their tenure.\n- Handles travel disruptions (e.g., flight cancellations, weather issues) with calm, creative problem-solving.",
                'type' => 'non_monetary',
                'benefits' => 'Industry "Passport" access: Paid tickets to major travel trade shows (like ITB Berlin or WTM London) or local tourism board networking events.',
                'status' => 'active',
            ],
            [
                'name' => 'Perfect Attendance Award',
                'description' => "- Maintains flawless attendance throughout the evaluation period.\n- Demonstrates punctuality and reliability by avoiding unexcused absences or tardiness.\n- Shows commitment to team schedules and client service by being consistently present.",
                'type' => 'non_monetary',
                'benefits' => 'Recognition Certificate and Gift: Formal acknowledgment plus a small token such as a gift card or company merchandise.',
                'status' => 'active',
            ],
            [
                'name' => 'Birthday Social Award',
                'description' => "- Must be an active team member on their birthday.\n- Eligible to receive recognition and benefits during their birth month.",
                'type' => 'non_monetary',
                'benefits' => 'Birthday Leave: A "free" day off to be used on their birthday or any day during their birth month.',
                'status' => 'active',
            ],
            [
                'name' => 'Promotion',
                'description' => "- Consistently meets or exceeds booking targets and conversion rates.\n- Maintains low error rates in complex itineraries and visa processing.\n- Demonstrates exceptional negotiation skills with suppliers (airlines, hotels) to secure favorable rates.",
                'type' => 'monetary',
                'benefits' => 'Meal & Entertainment Budgets: Larger funds to host clients or local suppliers.',
                'status' => 'active',
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::updateOrCreate(['name' => $reward['name']], $reward);
        }
    }
}
