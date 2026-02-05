<?php
use Illuminate\Support\Facades\Http;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$targetEmail = 'encarnacion.ceejay.barena@gmail.com';

try {
    $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');
    
    if ($response->successful()) {
        $result = $response->json();
        $accounts = [];
        if (isset($result['data']['system_accounts'])) $accounts = array_merge($accounts, $result['data']['system_accounts']);
        if (isset($result['data']['ess_accounts'])) $accounts = array_merge($accounts, $result['data']['ess_accounts']);

        $found = false;
        foreach ($accounts as $account) {
            $apiEmail = $account['employee']['email'] ?? '';
            if ($apiEmail === $targetEmail) {
                echo "\nFOUND_USER_START\n";
                echo "Email: " . $apiEmail . "\n";
                echo "Position: " . ($account['employee']['position'] ?? 'N/A') . "\n";
                echo "Password Plain: " . ($account['password_plain'] ?? 'NOT FOUND') . "\n";
                echo "Password Hash: " . ($account['password'] ?? 'NOT FOUND') . "\n";
                echo "FOUND_USER_END\n";
                $found = true;
                break;
            }
        }
        
        if (!$found) echo "User not found.\n";

    } else {
        echo "API Failed.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
