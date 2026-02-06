<?php

use Illuminate\Support\Facades\Http;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://legal-admin.jetlougetravels-ph.com/laravel_contract_api.php';
$filePath = __DIR__ . '/test_contract.txt';

$typesToTest = [
    'service_agreement',
    'employment_contract',
    'employee_contract',
    'employment',
    'partnership_agreement',
    'vendor_contract',
    'non_disclosure_agreement',
    'lease_agreement',
    'other'
];

foreach ($typesToTest as $type) {
    echo "Testing Type: $type ... ";
    try {
        $response = Http::withoutVerifying()
            ->attach('file', file_get_contents($filePath), 'test.txt')
            ->post($url, [
                'contract_title' => 'Test ' . $type,
                'client_name' => 'Tester',
                'client_email' => 'test@test.com',
                'contract_type' => $type,
                'start_date' => '2024-01-01',
                'end_date' => '2024-01-02',
                'contract_value' => '100',
                'description' => 'test',
                'created_by' => 'Debug',
                'status' => 'pending_review'
            ]);
        
        if ($response->successful()) {
            echo "SUCCESS\n";
        } else {
            echo "FAILED (" . $response->status() . ")\n";
        }
    } catch (\Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
