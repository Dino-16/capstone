<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://hr4.jetlougetravels-ph.com/api/accounts");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);

if (isset($data['data']['system_accounts'])) {
    $accounts = $data['data']['system_accounts'];
    $found = false;
    foreach ($accounts as $account) {
        $emp = $account['employee'];
        // Loose check for email
        if (strpos($emp['email'] ?? '', 'encarnacion') !== false) {
             echo "MATCH FOUND:\n";
             echo "password_plain key exists? " . (array_key_exists('password_plain', $account) ? "YES" : "NO") . "\n";
             echo "password key exists? " . (array_key_exists('password', $account) ? "YES" : "NO") . "\n";
             echo "password value: " . $account['password'] . "\n";
             if (isset($account['password_plain'])) echo "password_plain value: " . $account['password_plain'] . "\n";
        }

    }
    if (!$found) {
        echo "No account found matching 'encarnacion'.\n";
    }
} else {
    echo "No system_accounts data returned.\n";
}
