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
            
            // Get credentials from config (works with cached config in production)
            $mailHost = config('mail.mailers.smtp.host', 'smtp.gmail.com');
            $mailPort = config('mail.mailers.smtp.port', 587);
            $mailEncryption = config('mail.mailers.smtp.encryption', 'tls');
            $mailUsername = config('mail.mailers.smtp.username');
            $mailPassword = config('mail.mailers.smtp.password');
            
            // Configure SMTP with TLS and disable SSL verification
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $mailHost,
                'port' => $mailPort,
                'encryption' => $mailEncryption,
                'username' => $mailUsername,
                'password' => $mailPassword,
                'timeout' => null,
                'local_domain' => config('mail.mailers.smtp.local_domain'),
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]);

            // Reconnect mail manager with new config
            Mail::purge('smtp');

            // Send the email
            Mail::raw("Your Login OTP is: {$otp}", function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
                
                // Only set from if configured
                $fromAddress = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
                $fromName = config('mail.from.name') ?: env('MAIL_FROM_NAME', 'Laravel');
                
                if ($fromAddress) {
                    $message->from($fromAddress, $fromName);
                }
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
            
            // Get credentials from config
            $mailHost = config('mail.mailers.smtp.host', 'smtp.gmail.com');
            $mailPort = config('mail.mailers.smtp.port', 587);
            $mailEncryption = config('mail.mailers.smtp.encryption', 'tls');
            $mailUsername = config('mail.mailers.smtp.username');
            $mailPassword = config('mail.mailers.smtp.password');
            
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $mailHost,
                'port' => $mailPort,
                'encryption' => $mailEncryption,
                'username' => $mailUsername,
                'password' => $mailPassword,
                'timeout' => null,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]);

            Mail::purge('smtp');

            Mail::raw($content, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
                
                // Only set from if configured
                $fromAddress = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
                $fromName = config('mail.from.name') ?: env('MAIL_FROM_NAME', 'Laravel');
                
                if ($fromAddress) {
                    $message->from($fromAddress, $fromName);
                }
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
