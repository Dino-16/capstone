<?php
use Illuminate\Support\Facades\Http;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://legal-admin.jetlougetravels-ph.com/laravel_contract_api.php';
$filePath = __DIR__ . '/test_contract.txt';

$response = Http::withoutVerifying()
    ->attach('file', file_get_contents($filePath), 'test.txt')
    ->post($url, [
        'contract_title' => 'Test Service Agreement',
        'client_name' => 'Tester',
        'client_email' => 'test@test.com',
        'contract_type' => 'service_agreement', // Known good?
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-02',
        'contract_value' => '100',
        'description' => 'test',
        'created_by' => 'Debug',
        'status' => 'pending_review'
    ]);

echo "Service Agreement: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";

$response = Http::withoutVerifying()
    ->attach('file', file_get_contents($filePath), 'test.txt')
    ->post($url, [
        'contract_title' => 'Test Vendor',
        'client_name' => 'Tester',
        'client_email' => 'test@test.com',
        'contract_type' => 'vendor_contract', // Check this
        'start_date' => '2024-01-01',
        'end_date' => '2024-01-02',
        'contract_value' => '100',
        'description' => 'test',
        'created_by' => 'Debug',
        'status' => 'pending_review'
    ]);

echo "Vendor Contract: " . $response->status() . "\n";
