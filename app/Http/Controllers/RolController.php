<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RolService;

class RolController extends Controller
{
    protected $rolService;

    public function __construct(RolService $rolService)
    {
        $this->rolService = $rolService;
    }

    public function index()
    {
        $roles = $this->rolService->listarRoles();
        return view('roles.index', compact('roles'));
    }

    public function edit($id)
    {
        $datos = $this->rolService->obtenerDatosEdicion($id);
        return view('roles.edit', $datos);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'permisos' => 'array',
        ]);

        $this->rolService->actualizarRol($id, $request->only(['nombre', 'permisos']));

        return redirect()->route('roles.index')->with('success', 'Rol actualizado con éxito.');
    }

    public function create()
    {
        $permisos = $this->rolService->obtenerDatosCreacion();
        return view('roles.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:rol,nombre',
            'permisos' => 'array',
        ]);

        $this->rolService->crearRol($request->only(['nombre', 'permisos']));

        return redirect()->route('roles.index')->with('success', 'Rol creado con éxito.');
    }
}
