<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Website\Home;
use App\Livewire\Website\About;
use App\Livewire\Website\Contact;
use App\Livewire\Website\Careers;
use App\Livewire\Website\ApplyNow;
use App\Livewire\User\Dashboard;
use App\Livewire\User\Recruitment\Requisitions;
use App\Livewire\User\Recruitment\JobPostings;
use App\Livewire\User\Applicants\Applications;
use App\Livewire\User\Onboarding\Employees;
use App\Livewire\User\Onboarding\DocumentChecklists;
use App\Livewire\User\Onboarding\OrientationSchedule;
use App\Livewire\User\Performance\Evaluations;
use App\Livewire\User\Performance\EvaluationRecords;
use App\Livewire\User\Recognition\Rewards;
use App\Livewire\User\Recognition\GiveRewards;
use App\Livewire\User\Reports;


Route::middleware('guest')->group(function() {
    Route::get('/', Home::class)->name('home');
    Route::get('/about', About::class)->name('about');
    Route::get('/contact', Contact::class)->name('contact');
    Route::get('/careers', Careers::class)->name('careers');
    Route::get('/apply-now/{id}', ApplyNow::class)->name('apply-now');
});
Route::get('/dashboard', Dashboard::class)->name('dashboard');


Route::get('/positions', Requisitions::class)->name('positions');


Route::get('/job-postings', JobPostings::class)->name('job-postings');    
Route::get('/applications', Applications::class)->name('applications');
Route::get('/employees', Employees::class)->name('employees');
Route::get('/document-checklists', DocumentChecklists::class)->name('document-checklists');
Route::get('/orientation-schedule', OrientationSchedule::class)->name('orientation-schedule');
Route::get('/evaluations', Evaluations::class)->name('evaluations');
Route::get('/evaluation-records', EvaluationRecords::class)->name('evaluation-records');
Route::get('/rewards', Rewards::class)->name('rewards');
Route::get('/reward-giving', GiveRewards::class)->name('reward-giving');
Route::get('/reports', Reports::class)->name('reports');

/*
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
*/