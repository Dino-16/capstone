<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class MailService
{
    /**
     * Send an email with proper SSL configuration for production
     */
    public static function sendOtp($email, $otp, $subject = 'Secure Login OTP')
    {
        try {
            // Temporarily set mail configuration with SSL workaround
            $originalConfig = Config::get('mail.mailers.smtp');
            
            // Configure SMTP with TLS and disable SSL verification
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'port' => env('MAIL_PORT', 587),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]);

            // Reconnect mail manager with new config
            Mail::purge('smtp');

            // Send the email
            Mail::raw("Your Login OTP is: {$otp}", function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject)
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });

            // Restore original config
            Config::set('mail.mailers.smtp', $originalConfig);
            Mail::purge('smtp');

            return ['success' => true, 'message' => 'OTP sent successfully'];

        } catch (\Exception $e) {
            // Restore config on error
            if (isset($originalConfig)) {
                Config::set('mail.mailers.smtp', $originalConfig);
                Mail::purge('smtp');
            }
            
            return [
                'success' => false, 
                'message' => 'Failed to send OTP: ' . $e->getMessage(),
                'error' => $e
            ];
        }
    }

    /**
     * Send raw email with SSL workaround
     */
    public static function sendRaw($content, $email, $subject)
    {
        try {
            // Configure SMTP with SSL workaround
            $originalConfig = Config::get('mail.mailers.smtp');
            
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => env('MAIL_HOST', 'smtp.gmail.com'),
                'port' => env('MAIL_PORT', 587),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'timeout' => null,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]);

            Mail::purge('smtp');

            Mail::raw($content, function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject)
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            });

            Config::set('mail.mailers.smtp', $originalConfig);
            Mail::purge('smtp');

            return ['success' => true];

        } catch (\Exception $e) {
            if (isset($originalConfig)) {
                Config::set('mail.mailers.smtp', $originalConfig);
                Mail::purge('smtp');
            }
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
