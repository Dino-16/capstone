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
        // Debugging to ensure we are running THIS code.
        // Uncomment the line below to verify execution flow on screen.
        // dd('DEBUG: Login Method Reached');

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
                
                $matchFound = false;

                foreach ($accounts as $account) {
                    $apiEmail = $account['employee']['email'] ?? '';
                    // Fix: Check 'password_plain' first, then 'password'
                    $apiPassword = $account['password_plain'] ?? $account['password'] ?? '';
                    
                    // Get raw values
                    $userPosition = $account['employee']['position'] ?? '';
                    $userRole = $account['employee']['role'] ?? ''; 

                    // Plain text password comparison
                    if ($apiEmail === $this->email && $apiPassword === $this->password) {
                        $matchFound = true;
                        
                        // Normalization
                        $normalizedRole = trim($userRole);
                        $normalizedPosition = trim($userPosition);

                        \Log::info("Login attempt matched credential - API Role: '{$normalizedRole}', API Position: '{$normalizedPosition}'");

                        // ---------------------------------------------------------
                        // ROLE MAPPING LOGIC
                        // ---------------------------------------------------------
                        $appRole = null;
                        
                        // 1. Super Admin Mapping
                        // Matches if role is 'superadmin' OR position is 'IT Officer'
                        if (strcasecmp($normalizedRole, 'superadmin') === 0 || strcasecmp($normalizedPosition, 'IT Officer') === 0) {
                            $appRole = 'Super Admin';
                        }
                        // 2. HR Manager Mapping
                        // Matches if role is 'admin' OR position is 'HR Manager'
                        elseif (strcasecmp($normalizedRole, 'admin') === 0 || strcasecmp($normalizedPosition, 'HR Manager') === 0) {
                            $appRole = 'HR Manager';
                        }
                        // 3. HR Staff Mapping
                        // Matches if role is 'ess' OR position is 'HR Staff'
                        elseif (strcasecmp($normalizedRole, 'ess') === 0 || strcasecmp($normalizedPosition, 'HR Staff') === 0) {
                            $appRole = 'HR Staff';
                        }
                        // ---------------------------------------------------------

                        $allowedAppRoles = ['HR Staff', 'HR Manager', 'Super Admin'];
                        
                        $isAuthorized = false;
                        if ($appRole && in_array($appRole, $allowedAppRoles)) {
                            $isAuthorized = true;
                        }

                        if (!$isAuthorized) {
                            \App\Models\Admin\MfaLog::create([
                                'email' => $this->email,
                                'role' => $userRole, 
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                                'action' => 'login_attempt',
                                'status' => 'failed',
                            ]);
                            
                            // Explicit error message from our new logic
                            $this->addError('email', "ACCESS DENIED: User with Role '{$userRole}' / Position '{$userPosition}' is not authorized. Mapped App Role: " . ($appRole ?? 'None'));
                            return;
                        }

                        // Check MFA Settings
                        $mfaSetting = \App\Models\Admin\MfaSetting::first();
                        $isMfaEnabled = $mfaSetting ? $mfaSetting->is_global_enabled : true;

                        // Granular MFA Disable
                        if ($isMfaEnabled) {
                            if ($appRole === 'HR Staff' && !$mfaSetting->hr_staff_enabled) {
                                $isMfaEnabled = false;
                            } elseif ($appRole === 'HR Manager' && !$mfaSetting->hr_manager_enabled) {
                                $isMfaEnabled = false;
                            } elseif ($appRole === 'Super Admin' && !$mfaSetting->super_admin_enabled) {
                                $isMfaEnabled = false;
                            }
                        }

                        if ($isMfaEnabled) {
                            // MFA Flow
                            $otp = rand(100000, 999999);
                            
                            session([
                                'otp_session' => [
                                    'otp' => $otp,
                                    'otp_expires' => Carbon::now()->addMinutes(3), 
                                    'user_data' => [
                                        'id' => $account['id'],
                                        'name' => $account['employee']['first_name'] . ' ' . $account['employee']['last_name'],
                                        'email' => $apiEmail,
                                        'position' => $appRole, // Store MAPPED role
                                        'original_role' => $normalizedRole,
                                        'original_position' => $normalizedPosition,
                                        'details' => $account['employee'],
                                        'authenticated' => true 
                                    ]
                                ]
                            ]);

                            try {
                                $result = \App\Services\MailService::sendOtp($apiEmail, $otp, 'Secure Login OTP');
                                if (!$result['success']) {
                                    $this->addError('email', $result['message']);
                                    return;
                                }
                            } catch (\Exception $e) {
                                $this->addError('email', 'Failed to send OTP email: ' . $e->getMessage());
                                return;
                            }

                            \App\Models\Admin\MfaLog::create([
                                'email' => $this->email,
                                'role' => $appRole,
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                                'action' => 'mfa_sent',
                                'status' => 'success',
                            ]);

                            return redirect()->route('otp.verify');

                        } else {
                            // Direct Login Flow
                            session([
                                'user' => [
                                    'id' => $account['id'],
                                    'name' => $account['employee']['first_name'] . ' ' . $account['employee']['last_name'],
                                    'email' => $apiEmail,
                                    'position' => $appRole, // Store MAPPED role
                                    'original_role' => $normalizedRole,
                                    'original_position' => $normalizedPosition,
                                    'details' => $account['employee'],
                                    'authenticated' => true
                                ],
                                'last_activity_time' => \Carbon\Carbon::now()->timestamp
                            ]);

                             \App\Models\Admin\MfaLog::create([
                                'email' => $this->email,
                                'role' => $appRole,
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                                'action' => 'login_attempt',
                                'status' => 'success_mfa_disabled',
                            ]);

                            return redirect()->route('dashboard');
                        }
                    }
                }
                
                if (!$matchFound) {
                    \App\Models\Admin\MfaLog::create([
                        'email' => $this->email,
                        'role' => 'Unknown',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'action' => 'login_attempt',
                        'status' => 'failed_credentials',
                    ]);

                    $this->addError('email', 'Invalid credentials.');
                }

            } else {
                 $this->addError('email', 'API Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            $this->addError('email', 'Connection failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }
}
