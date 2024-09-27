<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use Illuminate\Http\Request;

class RolController extends Controller
{
    // Mostrar la lista de roles
    public function index()
    {
        $roles = Rol::all();
        return view('roles.index', compact('roles'));
    }

    // Mostrar el formulario para editar un rol
    public function edit($id)
    {
        $rol = Rol::findOrFail($id);
        $permisos = Permiso::all(); // Obtener todos los permisos
        $permisosAsignados = $rol->permisos->pluck('id_permiso')->toArray(); // Permisos asignados al rol

        return view('roles.edit', compact('rol', 'permisos', 'permisosAsignados'));
    }

    // Actualizar un rol en el almacenamiento
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'permisos' => 'array',
        ]);

        $rol = Rol::findOrFail($id);
        $rol->nombre = $request->input('nombre');
        $rol->save();

        // Actualizar permisos
        $rol->permisos()->sync($request->input('permisos', [])); // Sincronizar permisos

        return redirect()->route('roles.index')->with('success', 'Rol actualizado con éxito.');
    }


    public function create()
    {
        $permisos = Permiso::all(); // Obtener todos los permisos para asignar al rol
        return view('roles.create', compact('permisos'));
    }

    // Almacenar un nuevo rol
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:rol,nombre',
            'permisos' => 'array',
        ]);

        $rol = new Rol();
        $rol->nombre = $request->input('nombre');
        
        // Formatear el nombre para el campo clave
        $rol->clave = strtolower(str_replace(' ', '_', trim($rol->nombre)));

        $rol->save();

        // Asignar permisos al rol
        $rol->permisos()->sync($request->input('permisos', [])); // Sincronizar permisos

        return redirect()->route('roles.index')->with('success', 'Rol creado con éxito.');
    }
}
