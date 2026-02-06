<?php

$url = 'https://legal-admin.jetlougetravels-ph.com/laravel_contract_api.php';
$file_path = __DIR__ . '/test_contract.txt';

$cfile = new CURLFile($file_path, 'text/plain', 'test_contract.txt');

$data = [
    'file' => $cfile,
    'contract_title' => 'Test Contract 123',
    'client_name' => 'John Doe',
    'client_email' => 'john@example.com',
    'contract_type' => 'service_agreement',
    'start_date' => '2024-01-01',
    'end_date' => '2024-12-31',
    'contract_value' => '1000.00',
    'description' => 'Test description',
    'created_by' => 'Test Script',
    'status' => 'pending_review'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // Ignore SSL
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Ignore SSL

$response = curl_exec($ch);

if ($response === false) {
    echo 'Curl error: ' . curl_error($ch);
} else {
    echo 'Response: ' . $response;
}

curl_close($ch);
