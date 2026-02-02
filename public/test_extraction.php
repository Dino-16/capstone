<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use OpenAI\Laravel\Facades\OpenAI;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<pre>";
echo "<h1>Diagnostic AI Extraction Test</h1>";

// 1. Get the latest application
$application = \App\Models\Applicants\Application::latest()->first();

if (!$application) {
    die("No applications found to test.");
}

echo "Testing with latest application: <strong>{$application->first_name} {$application->last_name}</strong> (ID: {$application->id})\n";
echo "Resume Path: " . $application->resume_path . "\n";

$filePath = storage_path('app/public/' . $application->resume_path);
if (!file_exists($filePath)) {
    die("File not found at: $filePath");
}

echo "File exists. Size: " . filesize($filePath) . " bytes.\n\n";

// 2. Try PDF Parsing (Smalot)
echo "<h2>Step 1: Local PDF Parsing</h2>";
try {
    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    $text = $pdf->getText();
    $length = strlen(trim($text));
    
    echo "Parser: smalot/pdfparser\n";
    echo "Extracted Text Length: {$length} chars\n";
    echo "Preview (first 500 chars):\n";
    echo "--------------------------------------------------\n";
    echo htmlspecialchars(substr($text, 0, 500));
    echo "\n--------------------------------------------------\n";
    
    if ($length < 50) {
        echo "<strong style='color:red'>WARNING: Very little text extracted. This might be an image-based PDF.</strong>\n";
    } else {
        echo "<strong style='color:green'>SUCCESS: Text extraction seems to work.</strong>\n";
    }

} catch (\Exception $e) {
    echo "<strong style='color:red'>ERROR: PDF Parsing failed. " . $e->getMessage() . "</strong>\n";
    $text = "";
}

// 3. Try OpenAI API
echo "\n<h2>Step 2: AI API Test</h2>";

if (empty(trim($text))) {
    echo "Text is empty. Skipping text-based AI test.\n";
    echo "<strong>Only Vision API would attempt to run here (and fail on PDF).</strong>\n";
} else {
    echo "Attempting to send text to OpenAI (gpt-3.5-turbo)...\n";
    
    try {
        // Manually create curl to verify connectivity if facade fails
        // But let's verify config first
        echo "API Key check: " . (config('openai.api_key') ? "Set (starts with " . substr(config('openai.api_key'), 0, 8) . "...)" : "MISSING") . "\n";
        echo "SSL Verify: " . (config('recaptcha.curl_options.CURLOPT_SSL_VERIFYPEER') === false ? "False" : "Default") . "\n"; // Checking where verify might be set. 
        // Actually checking logic in ApplyNow
        
        $prompt = "Extract skills, experience, and education from this resume.\n\nRecall text:\n" . substr($text, 0, 2000); // Truncate for test
        
        // Direct facade call
        $startTime = microtime(true);
        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 100,
        ]);
        $duration = microtime(true) - $startTime;
        
        echo "API Call Duration: " . number_format($duration, 2) . "s\n";
        echo "Response:\n";
        print_r($result['choices'][0]['message']['content']);
        echo "\n<strong style='color:green'>SUCCESS: AI responded.</strong>\n";
        
    } catch (\Exception $e) {
        echo "<strong style='color:red'>ERROR: OpenAI API Failed.</strong>\n";
        echo "Message: " . $e->getMessage() . "\n";
        echo "Trace: " . $e->getTraceAsString() . "\n";
    }
}

echo "</pre>";
