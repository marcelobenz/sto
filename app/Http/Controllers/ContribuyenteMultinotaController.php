<?php

namespace App\Http\Controllers;

use App\Services\ContribuyenteMultinotaService;
use Illuminate\Http\Request;

class ContribuyenteMultinotaController extends Controller
{
    protected $service;

    public function __construct(ContribuyenteMultinotaService $service)
    {
        $this->service = $service;
    }
    
    public function index()
    {
        return view('contribuyentes.index'); 
    }

    public function showChangePasswordForm()
    {
        return view('externo.cambiar-clave');
    }

    public function perfil()
    {
        $contribuyente = $this->service->getContribuyenteFromSession();

        if (!$contribuyente) {
            return redirect()->route('login-externo')->withErrors(['error' => 'Debes iniciar sesión para acceder al perfil.']);
        }

        return view('externo.perfil-externo', ['usuario' => $contribuyente]);
    }
   
    public function buscar(Request $request)
    {
        $request->validate([
            'cuit' => 'required|digits:11', 
        ]);

        $result = $this->service->buscarPorCuit($request->cuit);
        
        if (!$result) {
            return back()->with('error', 'No se encontró un contribuyente con ese CUIT.');
        }

        return view('contribuyentes.index', $result); 
    }

    public function actualizarCorreo(Request $request, $id)
    {
        $request->validate([
            'correo' => 'required|email'
        ]);

        $success = $this->service->actualizarCorreo($id, $request->input('correo'));

        return $success 
            ? redirect()->back()->with('success', 'Correo actualizado correctamente')
            : redirect()->back()->with('error', 'Error al actualizar el correo');
    }

    public function restablecerClave($id)
    {
        $result = $this->service->restablecerClave($id);
        
        return $result['success']
            ? redirect()->back()->with('success', "La nueva contraseña es: {$result['password']}")
            : redirect()->back()->with('error', 'Error al restablecer la contraseña');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed', 
        ]);

        $contribuyente = $this->service->getContribuyenteFromSession();
        
        if (!$contribuyente) {
            return back()->withErrors(['error' => 'Usuario no autenticado.']);
        }

        $success = $this->service->cambiarClave(
            $request->current_password,
            $request->new_password,
            $contribuyente
        );

        if (!$success) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Limpiar clave de la sesión
        $contribuyente->clave = null;
        $this->service->storeContribuyenteInSession($contribuyente);

        return redirect()->route('bandeja-usuario-externo')->with('success', 'Contraseña cambiada con éxito.');
    }

    public function actualizarPerfil(Request $request)
    {
        $request->validate([
            'cuit' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono1' => 'nullable|string|max:20',
            'telefono2' => 'nullable|string|max:20',
            'correo' => 'required|email',
        ]);

        $success = $this->service->actualizarPerfil($request->all());
        
        if ($success) {
            $contribuyente = $this->service->getContribuyenteFromSession();
            $this->service->storeContribuyenteInSession($contribuyente);
        }

        return $success
            ? redirect()->route('bandeja-usuario-externo')->with('success', 'Perfil actualizado correctamente.')
            : redirect()->back()->with('error', 'Error al actualizar el perfil');
    }
}