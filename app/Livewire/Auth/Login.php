<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\User;

class Login extends Component
{
    use \App\Traits\WithHoneypot;

    public $email;
    public $password;

    public function login()
    {
        // Honeypot Check
        if (!$this->checkHoneypot('Login Form')) {
            return; 
        }

        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Use withoutVerifying to avoid SSL certificate issues in local environments
            $response = Http::withoutVerifying()->get('https://hr4.jetlougetravels-ph.com/api/accounts');

            if ($response->successful()) {
                $data = $response->json();
                $accounts = $data['data']['system_accounts'] ?? [];

                foreach ($accounts as $account) {
                    $apiEmail = $account['employee']['email'] ?? '';
                    $apiPassword = $account['password'] ?? '';
                    $userPosition = $account['employee']['position'] ?? '';

                    // Plain text password comparison
                    if ($apiEmail === $this->email && $apiPassword === $this->password) {
                        
                        // Strict Role Check
                        if (!in_array($userPosition, ['HR Staff', 'HR Manager'])) {
                            \App\Models\Admin\MfaLog::create([
                                'email' => $this->email,
                                'role' => $userPosition,
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                                'action' => 'login_attempt',
                                'status' => 'failed', // Denied by role policy
                            ]);
                            $this->addError('email', 'Access Denied: Only HR Staff and HR Managers can login.');
                            return;
                        }

                        // Check MFA Settings
                        $mfaSetting = \App\Models\Admin\MfaSetting::first();
                        $isMfaEnabled = $mfaSetting ? $mfaSetting->is_global_enabled : true;

                        // Granular Role Check
                        if ($isMfaEnabled) {
                            if ($userPosition === 'HR Staff' && !$mfaSetting->hr_staff_enabled) {
                                $isMfaEnabled = false;
                            } elseif ($userPosition === 'HR Manager' && !$mfaSetting->hr_manager_enabled) {
                                $isMfaEnabled = false;
                            }
                        }

                        if ($isMfaEnabled) {
                            // Proceed with MFA
                            $otp = rand(100000, 999999);
                            
                            // Temporary session for OTP
                            session([
                                'otp_session' => [
                                    'otp' => $otp,
                                    'otp_expires' => Carbon::now()->addMinutes(10), // Increased expire time
                                    'user_data' => [
                                        'id' => $account['id'],
                                        'name' => $account['employee']['first_name'] . ' ' . $account['employee']['last_name'],
                                        'email' => $apiEmail,
                                        'position' => $userPosition,
                                        'details' => $account['employee'],
                                        'authenticated' => true // Will be set in session after verification
                                    ]
                                ]
                            ]);

                            // Send OTP (Simulated for now, replace with actual Mail::raw if Mail configured)
                            // For development speed as per "just like in recaptcha", we assume logging it or sending. 
                            // Using Mail facade as seen in commented code.
                            try {
                                Mail::raw("Your Login OTP is: {$otp}", function ($message) use ($apiEmail) {
                                    $message->to($apiEmail)->subject('Secure Login OTP');
                                });
                            } catch (\Exception $e) {
                                $this->addError('email', 'Failed to send OTP email: ' . $e->getMessage());
                                return;
                            }

                            // Log MFA Challenge
                            \App\Models\Admin\MfaLog::create([
                                'email' => $this->email,
                                'role' => $userPosition,
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                                'action' => 'mfa_sent',
                                'status' => 'success',
                            ]);

                            return redirect()->route('otp.verify');

                        } else {
                            // MFA Disabled -> Direct Login
                            session([
                                'user' => [
                                    'id' => $account['id'],
                                    'name' => $account['employee']['first_name'] . ' ' . $account['employee']['last_name'],
                                    'email' => $apiEmail,
                                    'position' => $userPosition,
                                    'details' => $account['employee'],
                                    'authenticated' => true
                                ]
                            ]);

                             \App\Models\Admin\MfaLog::create([
                                'email' => $this->email,
                                'role' => $userPosition,
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                                'action' => 'login_attempt',
                                'status' => 'success_mfa_disabled',
                            ]);

                            return redirect()->route('dashboard');
                        }
                    }
                }
                
                // If loop finishes without return, invalid credentials
                \App\Models\Admin\MfaLog::create([
                    'email' => $this->email,
                    'role' => 'Unknown',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'action' => 'login_attempt',
                    'status' => 'failed',
                ]);

                $this->addError('email', 'Invalid credentials.');
            } else {
                 $this->addError('email', 'API Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            $this->addError('email', 'Connection failed: ' . $e->getMessage());
        }
    }


        /* 

        // ====================================
        // EMAIL VERIFICATION SETUP
        // ====================================
        $credentials = ['email' => $this->email, 'password' => $this->password];

        if (Auth::validate($credentials)) {
          $user = Auth::getProvider()->retrieveByCredentials($credentials);

                // Generate OTP
                $otp = rand(100000, 999999);

                // Store OTP in session
                session([
                    'otp' => $otp,
                    'otp_expires' => Carbon::now()->addMinutes(5),
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);

                // Send OTP email
                Mail::raw("Your OTP code is: {$otp}", function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Login OTP Verification');
                });

                // Redirect to OTP verification component
                return redirect()->route('otp.verify');
            }

            $this->addError('email', 'Invalid credentials.');

            */
    

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }
}
