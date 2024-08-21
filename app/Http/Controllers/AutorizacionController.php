<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\UsuarioView;
use App\Models\RequestLoginEmpleado;
use App\Models\IngresanteBuilder;
use App\Models\Ingresante;

class AutorizacionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $this->obtenerUsuario();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    private function obtenerUsuario(): UsuarioView {
        try {
            if(Session::get('EXPIRES_IN') !== 0) {
                $this->obtenerTokenTrasSesionExpirada();
            }
    
            //Llamado servicio usuario CVSA
            Http::asForm()->post(env('SERVICIO_TOKEN_CVSA'), [
                'grant_type' => 'refresh_token',
                'refresh_token' => Session::get('REFRESH_TOKEN'),
                'client_id' => env('OAUTH_ID'),
                'client_secret' => env('OAUTH_SECRET'),
                'scope' => '',
            ]);
    
            $access_token = Session::get('ACCESS_TOKEN');
    
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$access_token}"
            ])->get(env('SERVICIO_USUARIO_CVSA')); 
    
            $res = $response->json();
    
            if($res['cuil'] === null || $res['cuil'] === "") {
                //throw new RuntimeAlert("Faltan datos del usuario");
            }
    
            $requestView = new RequestLoginEmpleado();
		    $requestView->setUsuario(new UsuarioView($res['apellido'], "?", "N", "?", "?", "?", $res['cuil'], $res['email'], $res['name'], "?", "?"));

		    return $requestView->getUsuario();
        } catch (\Throwable $th) {
            //throw new ExcepcionControladaError("No es posible recuperar el usuario", e);
            error_log($th);
        }
    }

    private function obtenerTokenTrasSesionExpirada() {
        $response = Http::asForm()->post(env('SERVICIO_TOKEN_CVSA'), [
            'grant_type' => 'refresh_token',
            'refresh_token' => Session::get('REFRESH_TOKEN'),
            'client_id' => env('OAUTH_ID'),
            'client_secret' => env('OAUTH_SECRET'),
            'scope' => '',
        ]);

        $res = $response->json();

        config(['ACCESS_TOKEN'=> $res['access_token']]);
        config(['REFRESH_TOKEN'=> $res['refresh_token']]);
        config(['EXPIRES_IN'=> $res['expires_in']]);
         
        return $res;
    }
}
