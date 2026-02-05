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
                'description' => "- Consistently exceeds monthly goals or KPIs.\n- Delivers high-quality work with minimal supervision.\n- Supports colleagues, shares knowledge, and maintains a positive, collaborative attitude.\n- Demonstrates excellent attendance, punctuality, and professional integrity.\n- Exemplifies the core values and mission of the organization.",
                'type' => 'monetary',
                'benefits' => 'Paid Time Off (PTO): A "bonus day" or a few extra hours of leave.',
                'status' => 'active',
            ],
            [
                'name' => 'Years of Service Award',
                'description' => "- The employee must have maintained active status for the specific duration (e.g., a full 5 or 10 years).\n- Often requires the employee to be meeting performance expectations and have no active disciplinary actions.",
                'type' => 'monetary',
                'benefits' => 'Travel Vouchers: Credits for flights, hotels, or vacation packages to celebrate a major milestone.',
                'status' => 'active',
            ],
            [
                'name' => 'Rising Star Award',
                'description' => "- Shows initiative in identifying new \"hot\" destinations or sustainable travel trends.\n- Achieved high booking volumes or upselling success early in their tenure\n- Handled travel disruptions (flight cancels, weather) with calm and creative problem-solving.",
                'type' => 'non_monetary',
                'benefits' => 'Industry "Passport" access: Paid tickets to major travel trade shows (like ITB Berlin or WTM London) or local tourism board networking events.',
                'status' => 'active',
            ],
            [
                'name' => 'Customer Service',
                'description' => "- Problem Resolution: Demonstrates the ability to handle difficult customers with patience and effective solutions\n- Empathy: Consistently receives positive feedback regarding their tone, listening skills, and helpfulness.\n- Product Knowledge: Displays a deep understanding of services to provide accurate, fast assistance.",
                'type' => 'non_monetary',
                'benefits' => 'Schedule Flexibility: Priority for choosing shifts or the option to work from home for a set number of days.',
                'status' => 'active',
            ],
            [
                'name' => 'Birthday Social Award',
                'description' => "The individual must be a current member of the team on their birth date.",
                'type' => 'non_monetary',
                'benefits' => 'Birthday Leave: A "free" day off to be used on their birthday or any day during their birth month.',
                'status' => 'active',
            ],
            [
                'name' => 'Promotion',
                'description' => "- Consistently meeting or exceeding booking targets and conversion rates.\n- Maintaining low error rates in complex itineraries and visa processing.\n- Exceptional negotiation skills with suppliers (airlines, hotels) to get better rates.",
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
