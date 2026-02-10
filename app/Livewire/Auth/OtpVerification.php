<?php
namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OtpVerification extends Component
{
    public $otpDigits = [];
    public $otpExpiresAt = null;

    public function mount()
    {
        $sessionData = session('otp_session');
        if ($sessionData && isset($sessionData['otp_expires'])) {
            $this->otpExpiresAt = Carbon::parse($sessionData['otp_expires'])->timestamp;
        }
    }

    public function getOtpRemainingSecondsProperty()
    {
        if (!$this->otpExpiresAt) {
            return 0;
        }
        $remaining = $this->otpExpiresAt - Carbon::now()->timestamp;
        return max(0, $remaining);
    }
    public function verifyOtp()
    {
        $sessionData = session('otp_session');

        if (!$sessionData) {
            return redirect()->route('login');
        }

        $storedOtp = $sessionData['otp'];
        $expiresAt = $sessionData['otp_expires'];
        $userData = $sessionData['user_data'];

        $enteredOtp = implode('', $this->otpDigits);

        if ($enteredOtp == $storedOtp && Carbon::now()->lt($expiresAt)) {
            // Login Success
            session([
                'user' => array_merge($userData, ['authenticated' => true]),
                'last_activity_time' => Carbon::now()->timestamp
            ]);
            
            session()->forget('otp_session');

            \App\Models\Admin\MfaLog::create([
                'email' => $userData['email'],
                'role' => $userData['position'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'mfa_verified',
                'status' => 'success',
            ]);

            return redirect()->route('dashboard');
        } else {
            \App\Models\Admin\MfaLog::create([
                'email' => $userData['email'],
                'role' => $userData['position'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'mfa_failed',
                'status' => 'failed',
            ]);

            $this->addError('otp', 'Invalid or expired OTP.');
        }
    }

    public function resendOtp()
    {
        $sessionData = session('otp_session');
        
        if ($sessionData) {
            $otp = rand(100000, 999999);
            $email = $sessionData['user_data']['email'];
            $newExpiresAt = Carbon::now()->addMinutes(3);

            session([
                'otp_session' => array_merge($sessionData, [
                    'otp' => $otp,
                    'otp_expires' => $newExpiresAt
                ])
            ]);

            // Update the public property for the view
            $this->otpExpiresAt = $newExpiresAt->timestamp;

            try {
                $result = \App\Services\MailService::sendOtp($email, $otp, 'Resent Login OTP Verification');
                
                if ($result['success']) {
                    session()->flash('status', 'A new OTP has been sent to your email. Timer has been reset.');
                    
                    \App\Models\Admin\MfaLog::create([
                        'email' => $email,
                        'role' => $sessionData['user_data']['position'],
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'action' => 'mfa_resent',
                        'status' => 'success',
                    ]);
                } else {
                    session()->flash('status', $result['message']);
                }

            } catch (\Exception $e) {
                 session()->flash('status', 'Failed to send OTP: ' . $e->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.auth.otp-verification')->layout('layouts.auth');
    }
}