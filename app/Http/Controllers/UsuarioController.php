<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use App\Models\UsuarioInterno;
use App\Models\Rol;
use DB;
use Log;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $usuarioInterno = Session::get('usuario_interno');
        if(!$usuarioInterno->hasPermission('CONFIGURAR_USUARIOS')){
            return redirect()->route('navbar');
        }
        // Realizar la consulta usando Eloquent
        if ($request->ajax()) {
            $data = DB::table('usuario_interno as ui')
            ->join('grupo_interno as gi', 'ui.id_grupo_interno', '=', 'gi.id_grupo_interno')
            ->join('oficina as o', 'gi.id_oficina', '=', 'o.id_oficina')
            ->select(
                'ui.id_usuario_interno',
                'ui.legajo',
                'ui.nombre',
                'ui.apellido',
                'ui.correo_municipal',
                'gi.descripcion as grupo_descripcion',
                'o.descripcion as oficina_descripcion'
            )
            ->get();

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

        // Pasar los resultados a la vista
        return view('usuarios.index');
    }




    public function create()
    {
        $roles = Rol::all();  // Obtiene todos los roles
        return view('usuarios.create', compact('roles'));  // Pasa los roles a la vista
    }
    





    public function store(Request $request)
    {
        // Valida los datos del formulario
        $validatedData = $request->validate([
            'legajo' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'correo_municipal' => 'required|email',
            'cuit' => 'required|string|max:255',
            'dni' => 'required|string|max:255',
            'id_rol' => 'required|exists:rol,id_rol',  // Asegúrate de que el rol existe
        ]);
    
        // Crea un nuevo usuario con los datos validados
        $usuario = new UsuarioInterno($validatedData);
        $usuario->legajo = $request->input('legajo');
        $usuario->cuit = $request->input('cuit');
        $usuario->dni = $request->input('dni');
        $usuario->correo_municipal = $request->input('correo_municipal');
        $usuario->id_categoria_usuario = 23;
        $usuario->id_grupo_interno = 2;
        $usuario->estado = 1;
        $usuario->nombre = $request->input('nombre');
        $usuario->apellido = $request->input('apellido');
        $usuario->id_rol = $request->input('id_rol');  // Asignar el rol seleccionado
        $usuario->save();
    
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function obtenerPermisosPorRol($id_rol)
    {
        // Asume que el modelo Rol tiene una relación 'permisos'
        $rol = Rol::with('permisos')->find($id_rol);

        if (!$rol) {
            return response()->json(['error' => 'Rol no encontrado'], 404);
        }

        return response()->json($rol->permisos);
    }

    public function edit($id)
    {
        $usuario = UsuarioInterno::findOrFail($id);
        $roles = Rol::all(); 
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

        $usuario = UsuarioInterno::findOrFail($id);
        $usuario->update([
            'legajo' => $request->legajo,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo_municipal' => $request->correo_municipal,
            'dni' => $request->dni,
            'id_rol' => $request->id_rol,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente');
    }

    public function setUsuarioInterno()
    {
    
        $usuarioInterno = UsuarioInterno::with('rol.permisos')
                            ->where('legajo', 20299575085) 
                            ->first();


        if ($usuarioInterno) {
            Session::put('usuario_interno', $usuarioInterno); // Guardar en la sesión
        }

        // Redirigir a la página principal del sistema
        return redirect('/dashboard');
    }

    public function clearSession()
    {
        Session::forget('usuario_interno'); // Limpiar la sesión
        return redirect('/');
    }

}
