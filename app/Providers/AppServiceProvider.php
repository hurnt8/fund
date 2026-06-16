<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Mail::extend('smtp-no-verify', function (array $config) {
            $transport = new EsmtpTransport(
                $config['host'] ?? 'localhost',
                (int) ($config['port'] ?? 587),
                false
            );
            $transport->setUsername($config['username'] ?? '');
            $transport->setPassword($config['password'] ?? '');

            $stream = $transport->getStream();
            if ($stream instanceof SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ],
                ]);
            }

            return $transport;
        });
    }
}
