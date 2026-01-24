<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\User;

class Login extends Component
{
    public $email;
    public $password;

    public function login()
    {
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if(Auth::attempt($credentials)) {
            return redirect()->route('dashboard');
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
    }


    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }
}
