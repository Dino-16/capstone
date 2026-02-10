<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

trait RequiresPasswordVerification
{
    public bool $isPasswordVerified = false;
    public string $verificationPassword = '';
    public string $verificationError = '';
    public bool $showPasswordModal = true;

    /**
     * Initialize password verification state
     */
    public function initializePasswordVerification(): void
    {
        // Always require verification when the component is mounted (visited)
        // This ensures that navigating away and coming back will trigger the password prompt again
        $this->isPasswordVerified = false;
        $this->showPasswordModal = true;
    }

    /**
     * Get unique page key for tracking verification
     */
    protected function getPageKey(): string
    {
        return class_basename(static::class);
    }

    /**
     * Verify the password against the API
     */
    public function verifyPassword(): void
    {
        $this->verificationError = '';

        if (empty($this->verificationPassword)) {
            $this->verificationError = 'Please enter your password.';
            return;
        }

        try {
            $email = session('user.email');
            
            if (!$email) {
                $this->verificationError = 'Session expired. Please login again.';
                return;
            }

            // Verify password against the same API used for login
            $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

            if ($response->successful()) {
                $data = $response->json();
                $accounts = $data['data']['system_accounts'] ?? [];
                
                $matchFound = false;

                foreach ($accounts as $account) {
                    $apiEmail = $account['employee']['email'] ?? '';
                    // Check 'password_plain' first, then 'password' (same as login)
                    $apiPassword = $account['password_plain'] ?? $account['password'] ?? '';
                    
                    // Plain text password comparison (same as login)
                    if ($apiEmail === $email && $apiPassword === $this->verificationPassword) {
                        $matchFound = true;
                        
                        // Password verified - store in session
                        $verifiedFor = Session::get('password_verified_for', []);
                        $verifiedFor[] = $this->getPageKey();
                        
                        Session::put('password_verified_at', now());
                        Session::put('password_verified_for', array_unique($verifiedFor));
                        
                        $this->isPasswordVerified = true;
                        $this->showPasswordModal = false;
                        $this->verificationPassword = '';
                        
                        session()->flash('status', 'Access granted. You may now view this content.');
                        return;
                    }
                }
                
                // If no match found, password is incorrect
                if (!$matchFound) {
                    $this->verificationError = 'Invalid credential.';
                    $this->verificationPassword = '';
                }
            } else {
                $this->verificationError = 'Verification failed. Please try again.';
                $this->verificationPassword = '';
            }

        } catch (\Exception $e) {
            $this->verificationError = 'Verification failed. Please try again.';
            $this->verificationPassword = '';
        }
    }

    /**
     * Cancel verification and go back
     */
    public function cancelVerification(): void
    {
        // Redirect to dashboard
        $this->redirect(route('dashboard'));
    }

    /**
     * Reset verification (for logout or manual reset)
     */
    public static function resetVerification(): void
    {
        Session::forget('password_verified_at');
        Session::forget('password_verified_for');
    }
}
