<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Website\Home;
use App\Livewire\Website\About;
use App\Livewire\Website\Contact;
use App\Livewire\Website\Careers;
use App\Livewire\Website\ApplyNow;
use App\Livewire\Website\LoginOptions;

use App\Livewire\Auth\Login;
use App\Livewire\Auth\OtpVerification;
use App\Livewire\Auth\Register;

use App\Livewire\User\Dashboard;
use App\Livewire\Setting\Profile;
use App\Livewire\User\Recruitment\Requisitions;
use App\Livewire\User\Recruitment\JobPostings;
use App\Livewire\User\Applicants\Applications;
use App\Livewire\User\Applicants\Candidates;
use App\Livewire\User\Applicants\Interviews;
use App\Livewire\User\Applicants\Offers;
use App\Livewire\User\Onboarding\Employees;
use App\Livewire\User\Onboarding\DocumentChecklists;
use App\Livewire\User\Onboarding\OrientationSchedule;
use App\Livewire\User\Performance\Tracker;
use App\Livewire\User\Performance\Evaluations;
use App\Livewire\User\Performance\EvaluationRecords;
use App\Livewire\User\Recognition\Rewards;
use App\Livewire\User\Recognition\GiveRewards;
use App\Livewire\User\Reports;
use App\Livewire\User\FacilityRequest;
use App\Livewire\Admin\Recaptcha;
use App\Livewire\Admin\Mfa;
use App\Livewire\Admin\Honeypots;

Route::middleware('session.guest')->group(function() {
    Route::get('/', Home::class)->name('home');
    Route::get('/about', About::class)->name('about');
    Route::get('/contact', Contact::class)->name('contact');
    Route::get('/careers', Careers::class)->name('careers');
    Route::get('/apply-now/{id}', ApplyNow::class)->name('apply-now');
    Route::get('/login-options', LoginOptions::class)->name('login-options');
    Route::get('/login', Login::class)->name('login');
    Route::get('/otp-verification', OtpVerification::class)->name('otp.verify');
    Route::get('/register', Register::class)->name('register');

});

Route::middleware('session.auth')->group(function() {
    // Shared Routes (All Roles)
    Route::get('/profile', Profile::class)->name('profile');
    Route::post('/logout', function() {
        session()->forget('user');
        session()->flush();
        return redirect()->route('login');
    })->name('logout');

    // Level 3: Super Admin Only
    Route::middleware('role:Super Admin')->group(function() {
        Route::get('/superadmin-dashboard', \App\Livewire\SuperAdmin\Dashboard::class)->name('superadmin.dashboard');
        Route::get('/recaptcha', \App\Livewire\SuperAdmin\Recaptcha::class)->name('superadmin.recaptcha');
        Route::get('/mfa', \App\Livewire\SuperAdmin\Mfa::class)->name('superadmin.mfa');
        Route::get('/honeypots', \App\Livewire\SuperAdmin\Honeypots::class)->name('superadmin.honeypots');
        Route::get('/support-tickets', \App\Livewire\SuperAdmin\SupportTicket::class)->name('superadmin.tickets.index');
    });

    // Level 2 & 3: Admin & Super Admin (HR Manager, Super Admin)
    Route::middleware('role:HR Manager,Super Admin')->group(function() {
        Route::get('/admin-dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
        Route::get('/reports', Reports::class)->name('reports');
        
        // Support Tickets (HR Manager)
        Route::get('/submit-ticket', \App\Livewire\Admin\SubmitTicket::class)->name('admin.tickets.index');
    });

    // Level 1, 2, & 3: User & Up (HR Staff, HR Manager, Super Admin)
    Route::middleware('role:HR Staff,HR Manager,Super Admin')->group(function() {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('/recruitment-requests', Requisitions::class)->name('recruitment-requests');
        Route::get('/job-postings', JobPostings::class)->name('job-postings');    
        Route::get('/applications', Applications::class)->name('applications');
        Route::get('/candidates', Candidates::class)->name('candidates');
        Route::get('/interviews', Interviews::class)->name('interviews');
        Route::get('/offers', Offers::class)->name('offers');
        Route::get('/employees', Employees::class)->name('employees');
        Route::get('/document-checklists', DocumentChecklists::class)->name('document-checklists');
        Route::get('/orientation-schedule', OrientationSchedule::class)->name('orientation-schedule');
        Route::get('/evaluations', Evaluations::class)->name('evaluations');
        Route::get('/tracker', Tracker::class)->name('tracker');
        Route::get('/evaluation-records', EvaluationRecords::class)->name('evaluation-records');
        Route::get('/rewards', Rewards::class)->name('rewards');
        Route::get('/reward-giving', GiveRewards::class)->name('reward-giving');
        Route::get('/facility-request', FacilityRequest::class)->name('facility-request');
    });
});


Route::get('/storage/resumes/{filename}', function ($filename) {
    $path = 'resumes/' . $filename;
    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'Resume file not found');
    }
    
    $file = Storage::disk('public')->get($path);
    $mimeType = Storage::disk('public')->mimeType($path);
    
    return response($file, 200, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
})->name('resume.view');    
Route::get('/server-debug', function() {
    $logFile = storage_path('logs/laravel.log');
    $logs = file_exists($logFile) ? file_get_contents($logFile) : 'No log file found.';
    
    // Get last 2000 characters of logs to show recent errors
    $recentLogs = substr($logs, -3000);

    echo "<div style='font-family: monospace; padding: 20px;'>";
    echo "<h1>Server Environment Debugger</h1>";
    
    // 1. Check PHP Extensions
    echo "<h3>1. PHP Extensions</h3>";
    echo "cURL: " . (extension_loaded('curl') ? "<span style='color:green'>Installed</span>" : "<span style='color:red'>MISSING</span>") . "<br>";
    echo "OpenSSL: " . (extension_loaded('openssl') ? "<span style='color:green'>Installed</span>" : "<span style='color:red'>MISSING</span>") . "<br>";
    
    // 2. Check Dependencies
    echo "<h3>2. Dependencies (Vendor)</h3>";
    echo "PDF Parser (Smalot): " . (class_exists('Smalot\PdfParser\Parser') ? "<span style='color:green'>Found</span>" : "<span style='color:red'>MISSING (Run composer install)</span>") . "<br>";
    echo "OpenAI PHP: " . (class_exists('OpenAI\Laravel\Facades\OpenAI') ? "<span style='color:green'>Found</span>" : "<span style='color:red'>MISSING</span>") . "<br>";

    // 3. Configuration
    echo "<h3>3. Configuration (.env)</h3>";
    $apiKey = config('openai.api_key');
    $verify = config('openai.verify');
    
    echo "OPENAI_API_KEY: " . (!empty($apiKey) ? "<span style='color:green'>Set (starts with " . substr($apiKey, 0, 8) . "...)</span>" : "<span style='color:red'>NOT SET in .env</span>") . "<br>";
    echo "OPENAI_SSL_VERIFY: " . ($verify ? "TRUE (Strict SSL)" : "FALSE (Insecure/Allow)") . "<br>";
    
    // 4. Connectivity Test
    echo "<h3>4. Connectivity Test</h3>";
    try {
        $start = microtime(true);
        // Attempt a simple model list request
        $client = OpenAI::client($apiKey);
        $result = $client->models()->list();
        $duration = round(microtime(true) - $start, 2);
        
        echo "<div style='color:green; padding: 10px; border: 1px solid green;'>";
        echo "<strong>SUCCESS!</strong> Connection to OpenAI established in {$duration}s.<br>";
        echo "Found " . count($result->data) . " models.";
        echo "</div>";
    } catch (\Exception $e) {
        echo "<div style='color:red; padding: 10px; border: 1px solid red;'>";
        echo "<strong>CONNECTION FAILED:</strong> " . $e->getMessage() . "<br>";
        echo "<small>If error represents a certificate issue, try setting <code>OPENAI_SSL_VERIFY=false</code> in your .env file.</small>";
        echo "</div>";
    }

    // 5. Recent Logs
    echo "<h3>5. Recent System Logs (Last 3000 chars)</h3>";
    echo "<textarea style='width:100%; height: 300px; font-size: 11px;'>" . htmlspecialchars($recentLogs) . "</textarea>";

    // 6. SMTP Connectivity Test
    echo "<h3>6. SMTP Connectivity Test</h3>";
    $host = config('mail.mailers.smtp.host');
    $port = config('mail.mailers.smtp.port');
    $encryption = config('mail.mailers.smtp.encryption');
    
    echo "Current Config: <strong>{$encryption}://{$host}:{$port}</strong><br>";
    
    $timeout = 5;
    // Test basic TCP connection first
    echo "Testing TCP connection to {$host}:{$port}... ";
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($fp) {
        echo "<span style='color:green'>SUCCESS (TCP reachable)</span><br>";
        fclose($fp);
    } else {
        echo "<span style='color:red'>FAILED (Network Unreachable/Refused): {$errstr} ({$errno})</span><br>";
        echo "<strong>Recommendation:</strong> Your server is blocking port {$port}. Try switching to Port 587 (TLS).<br>";
    }

    echo "</div>";
});