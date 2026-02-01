<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class CustomMailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->extend('mail.manager', function (MailManager $manager) {
            $manager->extend('smtp', function ($config) {
                $transport = new EsmtpTransport(
                    $config['host'],
                    $config['port'],
                    $config['encryption'] ?? null
                );

                if (isset($config['username']) && isset($config['password'])) {
                    $transport->setUsername($config['username']);
                    $transport->setPassword($config['password']);
                }

                // Handle SSL verification options
                if (isset($config['verify_peer']) && $config['verify_peer'] === false) {
                    $transport->setStreamOptions([
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true,
                        ],
                    ]);
                }

                return $transport;
            });

            return $manager;
        });
    }
}
