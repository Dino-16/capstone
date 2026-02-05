<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recruitment\Requisition;

class RequisitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Requisition::truncate();
        $requisitions = [
            [
                'requested_by' => 'John Doe',
                'department' => 'Core',
                'position' => 'Travel Agent',
                'opening' => 3,
                'status' => 'Accepted',
            ],
            [
                'requested_by' => 'Jane Smith',
                'department' => 'Human Resource',
                'position' => 'HR Specialist',
                'opening' => 1,
                'status' => 'Pending',
            ],
            [
                'requested_by' => 'Robert Johnson',
                'department' => 'Logistics',
                'position' => 'Logistics Staff',
                'opening' => 2,
                'status' => 'Accepted',
            ],
            [
                'requested_by' => 'Michael Brown',
                'department' => 'Financial',
                'position' => 'Accountant',
                'opening' => 1,
                'status' => 'Drafted',
            ],
            [
                'requested_by' => 'Sarah Wilson',
                'department' => 'Administrative',
                'position' => 'Administrative Assistant',
                'opening' => 1,
                'status' => 'Accepted',
            ],
            [
                'requested_by' => 'David Lee',
                'department' => 'Logistics',
                'position' => 'Fleet Manager',
                'opening' => 2,
                'status' => 'Pending',
            ],
        ];

        foreach ($requisitions as $requisition) {
            Requisition::create($requisition);
        }
    }
}
