<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AutorizacionController extends Controller
{
    public function index(Request $request)
    {
        $this->obtenerUsuario();
        //retorna de obtenerUsuario()
        //Se obtiene usuario de base, si es incorrecto se lanza excepcion "El usuario ingresado es incorrecto" y si no esta activo
        //"El usuario ingresado no esta activo"
        //Si es correcto retorna y se redirecciona a "bandeja_personal"
    }

    private function obtenerUsuario() {
        if(Session::get('EXPIRES_IN') !== 0) {
            $this->obtenerTokenTrasSesionExpirada();
        }

        //Llamado servicio usuario CVSA
        //Validaciones, getUsuario, excepciones "Faltan datos del usuario" y "No es posible recuperar el usuario"
        //return
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
