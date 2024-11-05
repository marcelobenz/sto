<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ParametroMail;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $mailConfig = ParametroMail::first(); 

        if ($mailConfig) {
            Config::set('mail.mailer', 'smtp');
            Config::set('mail.host', $mailConfig->host);
            Config::set('mail.port', $mailConfig->puerto);
            Config::set('mail.username', $mailConfig->usuario);
            Config::set('mail.password', $mailConfig->clave);
            Config::set('mail.encryption', 'tls');
            Config::set('mail.from.address', $mailConfig->usuario);
            Config::set('mail.from.name', $mailConfig->usuario);
        }
    }
}
