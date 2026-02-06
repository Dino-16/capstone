<?php

use Illuminate\Support\Facades\Http;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = 'https://legal-admin.jetlougetravels-ph.com/laravel_contract_api.php';
$filePath = __DIR__ . '/test_contract.txt';

echo "Testing URL: $url\n";
echo "File: $filePath\n";

try {
    $response = Http::withoutVerifying()
        ->attach(
            'file', 
            file_get_contents($filePath), 
            'test_contract.txt'
        )
        ->post($url, [
            'contract_title' => 'Laravel Http Test',
            'client_name' => 'Jane Doe',
            'client_email' => 'jane@example.com',
            'contract_type' => 'other',
            'start_date' => '2024-02-01',
            'end_date' => '2024-02-28',
            'contract_value' => '500.00',
            'description' => 'Test via facade',
            'created_by' => 'Debug Script',
            'status' => 'pending_review'
        ]);

    echo "Status Code: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";

} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
