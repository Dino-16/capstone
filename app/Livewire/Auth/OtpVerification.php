<?php
namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OtpVerification extends Component
{
    public $otpDigits = [];

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
                'user' => array_merge($userData, ['authenticated' => true])
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

            session([
                'otp_session' => array_merge($sessionData, [
                    'otp' => $otp,
                    'otp_expires' => Carbon::now()->addMinutes(10)
                ])
            ]);

            try {
                $result = \App\Services\MailService::sendOtp($email, $otp, 'Resent Login OTP Verification');
                
                if ($result['success']) {
                    session()->flash('status', 'A new OTP has been sent to your email.');
                    
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