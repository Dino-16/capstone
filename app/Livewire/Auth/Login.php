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
    public $email;
    public $password;

    public function login()
    {
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

                    // Plain text password comparison as requested
                    if ($apiEmail === $this->email && $apiPassword === $this->password) {
                        
                        // Store user data in session
                        session([
                            'user' => [
                                'id' => $account['id'],
                                'name' => $account['employee']['first_name'] . ' ' . $account['employee']['last_name'],
                                'email' => $apiEmail,
                                'position' => $account['employee']['position'],
                                'details' => $account['employee'], // Store full employee details
                                'authenticated' => true
                            ]
                        ]);

                        return redirect()->route('dashboard');
                    }
                }
                
                $this->addError('email', 'Invalid credentials.');
            } else {
                 $this->addError('email', 'API Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            // Display the specific error message for debugging
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
