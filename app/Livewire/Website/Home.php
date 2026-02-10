<?php

namespace App\Livewire\Website;

use Livewire\Component;
use App\Models\Admin\RecaptchaSetting;
use App\Models\Admin\RecaptchaLog;
use Illuminate\Support\Facades\Http;

class Home extends Component
{
    public bool $showRecaptchaModal = true;
    public bool $recaptchaVerified = false;

    public function mount()
    {
        // Check if reCAPTCHA is enabled
        $setting = RecaptchaSetting::first();
        if ($setting && !$setting->is_enabled) {
            $this->showRecaptchaModal = false;
            $this->recaptchaVerified = true;
        }
    }

    public function verifyRecaptcha($recaptchaResponse)
    {
        $secretKey = config('recaptcha.secret_key');
        
        try {
            $response = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $recaptchaResponse,
                'remoteip' => request()->ip(),
            ]);
            
            $result = $response->json();
            
            // Log the reCAPTCHA attempt
            RecaptchaLog::create([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => ($result['success'] ?? false) ? 'success' : 'failed',
            ]);
            
            if ($result['success'] ?? false) {
                $this->recaptchaVerified = true;
                $this->showRecaptchaModal = false;
            } else {
                $this->addError('recaptcha', 'reCAPTCHA verification failed. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            $this->addError('recaptcha', 'An error occurred during verification. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.website.home')->layout('layouts.website');
    }
}
