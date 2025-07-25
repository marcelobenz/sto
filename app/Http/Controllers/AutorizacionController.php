<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Builders\IngresanteBuilder;
use App\Models\Ingresante;
use App\Models\RequestLoginEmpleado;
use App\Models\UsuarioView;
use \Exception;

class AutorizacionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $this->obtenerUsuario();
            $ingresanteBuilder = new IngresanteBuilder();
            $ingresanteBuilder->setCuit($user->getCuit());
            $ingresante = $ingresanteBuilder->build();
            $ingresante->ingresar();
            return Redirect::to('/bandeja-personal');
        } catch (\Throwable $th) {
            throw new Exception("Error al recuperar el usuario de base", $th);

            //Actualizar codigo de acuerdo al catch comentado de abajo
        }

        /* 
        } catch (NullPointerException e) { 
        	try {
            	UsuarioInterno usuario = new UsuarioInterno();
                usuario.setLegajo(this.obtenerUsuario().getCuit());
				usuario.setCuit(this.obtenerUsuario().getCuit());
				usuario.setDni(this.obtenerUsuario().getCuit().substring(2, (this.obtenerUsuario().getCuit().length() - 1)));
	            usuario.setCorreoMunicipal(this.obtenerUsuario().getMail());
	            usuario.setNombre(this.obtenerUsuario().getNombre());
	            usuario.setApellido(this.obtenerUsuario().getApellido());
	            
	            OficinaInterna oficina = new OficinaInterna(1, "001", "EXPLOTACIONES");
	            usuario.setOficina(oficina.getNombre());
	        	
	        	new UsuarioInternoController().crearUsuarioDefault(usuario, oficina);
	        	
	        	new AsignableATramiteController().retieveAllFromDB();
	        	
	        	Ingresante ingresante = new IngresanteBuilder().setCuit(this.obtenerUsuario().getCuit()).build();
	            ingresante.ingresar();
	            this.setAnalizando(false);
	            this.redireccionar(Navegacion.bandeja_personal);
			} catch (Exception e1) {
				this.setMensaje(e.getMessage());
	            VTULogger.getLogger().error(e.getMessage(), e);
			}
    	} catch (RuntimeAlert e) {
            this.setMensaje(e.getMessage());
        } catch (Exception e) {
            this.setMensaje(e.getMessage());
            VTULogger.getLogger().error(e.getMessage(), e);
        } */
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
    
            if($res['cuit'] === null || $res['cuit'] === "") {
                throw new Exception("Faltan datos del usuario");
            }
    
            $requestView = new RequestLoginEmpleado();
		    $requestView->setUsuario(new UsuarioView($res['apellido'], "?", "N", "?", "?", "?", $res['cuit'], $res['email'], $res['name'], "?", "?"));

		    return $requestView->getUsuario();
        } catch (\Throwable $th) {
            throw new Exception("No es posible recuperar el usuario", $th);
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
