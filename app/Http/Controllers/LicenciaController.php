<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LicenciaService;

class LicenciaController extends Controller
{
    protected $licenciaService;

    public function __construct(LicenciaService $licenciaService)
    {
        $this->licenciaService = $licenciaService;
    }

    public function crearLicencia($id_usuario_interno)
    {
        $datos = $this->licenciaService->obtenerDatosParaFormulario($id_usuario_interno);
        return view('licencias.edit', [
            'usuario' => $datos['usuario'],
            'historialLicencias' => $datos['historial']
        ]);
    }

    public function guardarLicencia(Request $request, $id_usuario_interno)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'motivo' => 'required|string|max:255',
        ]);

        $this->licenciaService->guardarLicencia($id_usuario_interno, $request->only([
            'motivo', 'fecha_inicio', 'fecha_fin'
        ]));

        return redirect()->route('licencias.crear', $id_usuario_interno)
                         ->with('success', 'Licencia creada exitosamente');
    }
}
