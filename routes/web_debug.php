<?php
/**
 * TEMPORARY DEBUG ROUTES
 * Add these to your routes/web.php temporarily to check production status
 * REMOVE AFTER DEBUGGING for security!
 */

use Illuminate\Support\Facades\Route;

// Check if PDF Parser is available
Route::get('/debug/check-pdf-parser', function () {
    $results = [
        'pdf_parser_class_exists' => class_exists('\Smalot\PdfParser\Parser'),
        'openai_facade_exists' => class_exists('\OpenAI\Laravel\Facades\OpenAI'),
        'openai_key_set' => !empty(config('openai.api_key')),
        'openai_key_length' => strlen(config('openai.api_key')),
        'storage_path_exists' => is_dir(storage_path('app/public')),
        'storage_path_writable' => is_writable(storage_path('app/public')),
        'resumes_path_exists' => is_dir(storage_path('app/public/resumes')),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
    ];
    
    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
});

// View last 50 lines of Laravel log
Route::get('/debug/view-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    
    if (!file_exists($logFile)) {
        return response()->json(['error' => 'Log file does not exist'], 404);
    }
    
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    
    return response('<pre>' . implode('', $lastLines) . '</pre>');
});

// Test OpenAI API
Route::get('/debug/test-openai', function () {
    try {
        if (!class_exists('\OpenAI\Laravel\Facades\OpenAI')) {
            return response()->json(['error' => 'OpenAI facade not found'], 500);
        }
        
        $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => 'Say "test successful"'],
            ],
            'max_tokens' => 10,
        ]);
        
        return response()->json([
            'success' => true,
            'response' => $response['choices'][0]['message']['content'] ?? 'No response',
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'class' => get_class($e),
        ], 500, [], JSON_PRETTY_PRINT);
    }
});
