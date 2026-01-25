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
        // Validate input
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Call HR API to get system accounts
            $response = Http::get('http://hr4.jetlougetravels-ph.com/api/accounts');

            if ($response->successful()) {
                $data = $response->json();
                $systemAccounts = $data['system_accounts'] ?? [];
                
                // Find matching account by email and password
                $authenticatedAccount = null;
                foreach ($systemAccounts as $account) {
                    if ($account['employee']['email'] === $this->email && 
                        $account['password'] === $this->password && 
                        !$account['blocked']) {
                        $authenticatedAccount = $account;
                        break;
                    }
                }
                
                if ($authenticatedAccount) {
                    // Store user data in session for API-based authentication
                    session([
                        'user' => [
                            'email' => $this->email,
                            'first_name' => $authenticatedAccount['employee']['first_name'],
                            'last_name' => $authenticatedAccount['employee']['last_name'],
                            'full_name' => $authenticatedAccount['employee']['first_name'] . ' ' . $authenticatedAccount['employee']['last_name'],
                            'position' => $authenticatedAccount['employee']['position'],
                            'department' => $authenticatedAccount['employee']['department']['name'],
                            'employee_id' => $authenticatedAccount['employee']['id'],
                            'account_id' => $authenticatedAccount['id'],
                            'authenticated' => true,
                        ]
                    ]);
                    
                    return redirect()->route('dashboard');
                } else {
                    $this->addError('email', 'Invalid credentials or account blocked.');
                }
            } else {
                $this->addError('email', 'Unable to connect to authentication system.');
            }
        } catch (\Exception $e) {
            $this->addError('email', 'Login failed: ' . $e->getMessage());
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
