<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use App\Services\UsuarioService;

class UsuarioController extends Controller
{
    protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }

    public function index(Request $request)
    {
        $usuarioInterno = Session::get('usuario_interno');
      /*  if(!$usuarioInterno->hasPermission('CONFIGURAR_USUARIOS')){
            return redirect()->route('navbar');
        }*/

        if ($request->ajax()) {
            $data = $this->usuarioService->listarUsuarios();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('usuarios.index');
    }

    public function create()
    {
        $roles = $this->usuarioService->obtenerRoles();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'legajo' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'correo_municipal' => 'required|email',
            'cuit' => 'required|string|max:255',
            'dni' => 'required|string|max:255',
            'id_rol' => 'required|exists:rol,id_rol',
        ]);

        $this->usuarioService->crearUsuario($validatedData);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function obtenerPermisosPorRol($id_rol)
    {
        $rol = $this->usuarioService->permisosPorRol($id_rol);

        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        return response()->json($rol->permisos);
    }

    public function edit($id)
    {
        $usuario = $this->usuarioService->obtenerUsuario($id);
        $roles = $this->usuarioService->obtenerRoles();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'legajo' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'correo_municipal' => 'required|email|max:255',
            'dni' => 'required|numeric',
            'id_rol' => 'required|exists:rol,id_rol',
        ]);

        $this->usuarioService->actualizarUsuario($id, $request->only([
            'legajo', 'nombre', 'apellido', 'correo_municipal', 'dni', 'id_rol'
        ]));

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function perfil()
    {
        $usuario = Session::get('usuario_interno');
        return view('usuarios.perfil', compact('usuario'));
    }

    public function actualizarPerfil(Request $request)
    {
        $this->usuarioService->actualizarUsuario($request->id_usuario_interno, $request->only([
            'legajo', 'nombre', 'apellido', 'cuit', 'correo_municipal', 'dni'
        ]));

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function setUsuarioInterno()
    {
        $usuarioInterno = $this->usuarioService->obtenerUsuarioPorLegajo(20299575085);

        if ($usuarioInterno) {
            Session::put('usuario_interno', $usuarioInterno);
            session(['isExterno' => false]);
        }

        return redirect('/dashboard');
    }

    public function clearSession()
    {
        Session::forget('usuario_interno');
        return redirect('/');
    }
}
