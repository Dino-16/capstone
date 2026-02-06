<?php
use Illuminate\Support\Facades\Http;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://legal-admin.jetlougetravels-ph.com/laravel_contract_api.php';
$filePath = __DIR__ . '/test_contract.txt';

$types = ['employment_agreement', 'partnership_agreement', 'employment_contract'];

foreach ($types as $type) {
    $response = Http::withoutVerifying()->attach('file', file_get_contents($filePath), 'test.txt')->post($url, [
        'contract_title' => 'Test ' . $type, 'client_name' => 'Tester', 'client_email' => 'test@test.com',
        'contract_type' => $type, 'start_date' => '2024-01-01', 'end_date' => '2024-01-02',
        'contract_value' => '100', 'description' => 'test', 'created_by' => 'Debug', 'status' => 'pending_review'
    ]);
    echo "$type: " . $response->status() . "\n";
}
