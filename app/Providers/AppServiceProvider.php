<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ParametroMail;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\NavbarController;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $configuracionMail = ParametroMail::where('activo', 1)->first();

        if ($configuracionMail) {
            
            config([
                'mail.mailers.smtp.host' => $configuracionMail->host,
                'mail.mailers.smtp.port' => $configuracionMail->puerto,
                'mail.mailers.smtp.username' => $configuracionMail->usuario,
                'mail.mailers.smtp.password' => $configuracionMail->clave,
                'mail.mailers.smtp.encryption' => 'tls', 
                'mail.from.address' => 'correo@ejemplo.com', 
                'mail.from.name' => 'Cognisys',
            ]);
        }

        View::composer('*', function ($view) {
            $data = app(NavbarController::class)->cargarItemsFormularios();
    
            $view->with($data);
        });
    }
}
