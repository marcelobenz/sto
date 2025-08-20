<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TramiteService;

class TramiteController extends Controller
{
    protected $tramiteService;

    public function __construct(TramiteService $tramiteService)
    {
        $this->tramiteService = $tramiteService;
    }

    public function index()
    {
        return view('tramites.index', [
            'tituloPagina' => 'Todos los Trámites',
            'soloIniciados' => false
        ]);
    }

    public function getTramitesData(Request $request)
    {
        $soloIniciados = $request->get('soloIniciados') === 'true';
        return response()->json($this->tramiteService->getTramitesDataForDataTable($request->all(), $soloIniciados));
    }

    public function show($idTramite)
    {
        $data = $this->tramiteService->getTramiteDetails($idTramite);
        return view('tramites.detalle', array_merge($data, ['idTramite' => $idTramite]));
    }

    public function darDeBaja(Request $request)
    {
        $success = $this->tramiteService->darDeBajaTramite($request->input('idTramite'));
        return response()->json(['success' => $success]);
    }

    public function cambiarPrioridad(Request $request)
    {
        $request->validate([
            'id_tramite' => 'required|exists:multinota,id_tramite',
            'id_prioridad' => 'required|exists:prioridad,id_prioridad',
        ]);

        $success = $this->tramiteService->cambiarPrioridad(
            $request->id_tramite,
            $request->id_prioridad
        );

        if ($success) {
            return redirect()->back()->with('success', 'Prioridad actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'Error al cambiar prioridad.');
    }

    public function tomarTramite(Request $request)
    {
        $success = $this->tramiteService->tomarTramite($request->input('idTramite'));
        return response()->json(['success' => $success]);
    }

  public function getPosiblesEstados(Request $request)
{
    $idTramite = $request->input('idTramite');
    $estados = $this->tramiteService->getPosiblesEstados($idTramite);

    return response()->json([
        'success' => true,
        'estados' => $estados
    ]);
}

public function avanzarEstado(Request $request)
{
    $idTramite = $request->input('idTramite');
    $idEstadoNuevo = $request->input('idEstadoNuevo');

    $success = $this->tramiteService->avanzarEstado($idTramite, $idEstadoNuevo);

    return response()->json([
        'success' => (bool)$success,
        'message' => $success ? 'Estado actualizado' : 'No se pudo avanzar el estado'
    ]);
}



    public function enCurso()
    {
        return view('tramites.index', [
            'tituloPagina' => 'Trámites en Curso',
            'soloIniciados' => true
        ]);
    }
}