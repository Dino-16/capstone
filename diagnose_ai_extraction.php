#!/usr/bin/env php
<?php

/**
 * AI Extraction Diagnostic Script
 * 
 * Run this script on your production server to diagnose AI extraction issues.
 * Usage: php diagnose_ai_extraction.php
 */

echo "=== AI Extraction Diagnostics ===\n\n";

// 1. Check PHP Version
echo "1. PHP Version: " . PHP_VERSION . "\n";
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    echo "   ⚠ WARNING: PHP version is below 8.0\n";
}
echo "\n";

// 2. Check if Laravel is accessible
echo "2. Laravel Installation:\n";
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "   ❌ vendor/autoload.php not found. Run: composer install\n";
    exit(1);
}
require __DIR__ . '/vendor/autoload.php';
echo "   ✓ Vendor autoload found\n\n";

// 3. Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "   ✓ Laravel loaded successfully\n\n";

// 4. Check PDF Parser Package
echo "3. PDF Parser (smalot/pdfparser):\n";
if (class_exists('\Smalot\PdfParser\Parser')) {
    echo "   ✓ PdfParser class is available\n";
    try {
        $parser = new \Smalot\PdfParser\Parser();
        echo "   ✓ Can instantiate Parser\n";
    } catch (\Exception $e) {
        echo "   ❌ Cannot instantiate Parser: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ PdfParser class NOT found\n";
    echo "   → Run: composer require smalot/pdfparser\n";
}
echo "\n";

// 5. Check OpenAI Package
echo "4. OpenAI Package:\n";
if (class_exists('\OpenAI\Laravel\Facades\OpenAI')) {
    echo "   ✓ OpenAI facade is available\n";
} else {
    echo "   ❌ OpenAI facade NOT found\n";
    echo "   → Run: composer require openai-php/laravel\n";
}

$apiKey = config('openai.api_key');
if (empty($apiKey)) {
    echo "   ❌ OPENAI_API_KEY is NOT set in .env\n";
} else {
    $maskedKey = substr($apiKey, 0, 10) . '...' . substr($apiKey, -4);
    echo "   ✓ OPENAI_API_KEY is set: $maskedKey\n";
}
echo "\n";

// 6. Check File Permissions
echo "5. File Permissions:\n";
$storagePath = storage_path('app/public');
if (!is_dir($storagePath)) {
    echo "   ❌ Storage directory does not exist: $storagePath\n";
    echo "   → Run: mkdir -p $storagePath\n";
} else {
    echo "   ✓ Storage directory exists: $storagePath\n";
    
    if (!is_writable($storagePath)) {
        echo "   ❌ Storage directory is NOT writable\n";
        echo "   → Run: chmod -R 775 storage/\n";
        echo "   → Run: chown -R www-data:www-data storage/\n";
    } else {
        echo "   ✓ Storage directory is writable\n";
    }
}

$resumesPath = storage_path('app/public/resumes');
if (!is_dir($resumesPath)) {
    echo "   ⚠ Resumes directory does not exist: $resumesPath\n";
} else {
    echo "   ✓ Resumes directory exists: $resumesPath\n";
    if (!is_readable($resumesPath)) {
        echo "   ❌ Resumes directory is NOT readable\n";
    } else {
        echo "   ✓ Resumes directory is readable\n";
    }
}
echo "\n";

// 7. Test OpenAI Connection
echo "6. OpenAI API Connection Test:\n";
if (!empty($apiKey) && class_exists('\OpenAI\Laravel\Facades\OpenAI')) {
    try {
        echo "   Testing API connection...\n";
        $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => 'Say "test successful" if you can read this.'],
            ],
            'max_tokens' => 10,
        ]);
        
        $content = $response['choices'][0]['message']['content'] ?? '';
        echo "   ✓ OpenAI API is working! Response: $content\n";
    } catch (\Exception $e) {
        echo "   ❌ OpenAI API test FAILED\n";
        echo "   Error: " . $e->getMessage() . "\n";
        echo "   Error class: " . get_class($e) . "\n";
    }
} else {
    echo "   ⚠ Skipped (API key or package missing)\n";
}
echo "\n";

// 8. Check Log Files
echo "7. Log File Access:\n";
$logPath = storage_path('logs/laravel.log');
if (!file_exists($logPath)) {
    echo "   ⚠ Log file does not exist yet: $logPath\n";
} else {
    echo "   ✓ Log file exists: $logPath\n";
    if (!is_writable($logPath)) {
        echo "   ❌ Log file is NOT writable\n";
    } else {
        echo "   ✓ Log file is writable\n";
        
        // Show last 10 lines
        $lines = file($logPath);
        if ($lines) {
            $lastLines = array_slice($lines, -5);
            echo "\n   Last 5 log entries:\n";
            foreach ($lastLines as $line) {
                echo "   " . trim($line) . "\n";
            }
        }
    }
}
echo "\n";

// 9. PHP Extensions
echo "8. Required PHP Extensions:\n";
$requiredExtensions = ['curl', 'json', 'mbstring', 'openssl', 'pdo', 'tokenizer', 'xml'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✓ $ext\n";
    } else {
        echo "   ❌ $ext NOT loaded\n";
    }
}
echo "\n";

// 10. Memory and Timeouts
echo "9. PHP Configuration:\n";
echo "   Memory Limit: " . ini_get('memory_limit') . "\n";
echo "   Max Execution Time: " . ini_get('max_execution_time') . "s\n";
echo "   Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
echo "   Post Max Size: " . ini_get('post_max_size') . "\n";
echo "\n";

echo "=== Diagnostics Complete ===\n";
echo "\nIf you see any ❌ or ⚠ symbols above, those need to be fixed.\n";
echo "Check your Laravel logs at: $logPath\n";
