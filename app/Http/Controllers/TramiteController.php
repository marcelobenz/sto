<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TramiteService;
use App\Models\Multinota;
use App\Models\TramiteEstadoTramite;
use App\Models\RespuestaCuestionario;
use Illuminate\Support\Facades\DB;

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

public function guardarCuestionario(Request $request)
{
    try {
        \Log::debug('guardarCuestionario: inicio', [
            'id_tramite' => $request->input('id_tramite'),
            'respuestas' => $request->input('respuestas', []),
            'detalles' => $request->input('detalles', [])
        ]);

        $idTramite = $request->input('id_tramite');
        $respuestas = $request->input('respuestas', []);
        $detalles = $request->input('detalles', []);
        
        // Obtener información del trámite
        $tramite = Multinota::findOrFail($idTramite);
        \Log::debug('Trámite encontrado', ['tramite' => $tramite]);

        $estadoTramite = DB::table('tramite_estado_tramite')
            ->join('multinota', 'tramite_estado_tramite.id_tramite', '=', 'multinota.id_tramite')
            ->where('multinota.id_tramite', $idTramite)
            ->where('tramite_estado_tramite.activo', 1)
            ->select('tramite_estado_tramite.id_estado_tramite')
            ->first();

        \Log::debug('Estado del trámite obtenido', ['estadoTramite' => $estadoTramite]);
        
        if (!$estadoTramite) {
            \Log::warning('No se encontró estado activo para el trámite', ['id_tramite' => $idTramite]);
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un estado activo para este trámite'
            ], 400);
        }

        $idEstadoTramite = $estadoTramite->id_estado_tramite;
        \Log::debug('ID Estado tramite', ['id_estado_tramite' => $idEstadoTramite]);
        
        foreach ($respuestas as $idPregunta => $respuesta) {
            \Log::debug('Procesando respuesta', [
                'idPregunta' => $idPregunta,
                'respuesta' => $respuesta,
                'detalle' => $detalles[$idPregunta] ?? null
            ]);
            
            // Buscar si ya existe una respuesta para esta pregunta
            $respuestaExistente = RespuestaCuestionario::where('id_tramite', $idTramite)
                ->where('id_pregunta_cuestionario', $idPregunta)
                ->first();
            
            // Obtener el detalle si existe
            $detalle = isset($detalles[$idPregunta]) ? $detalles[$idPregunta] : null;
            
            if ($respuestaExistente) {
                \Log::debug('Respuesta existente encontrada, actualizando', ['idPregunta' => $idPregunta]);
                // Actualizar respuesta existente
                $respuestaExistente->update([
                    'flag_valor' => $respuesta,
                    'detalle' => $detalle,
                    'id_estado_tramite' => $idEstadoTramite,
                    'fecha_sistema' => now()
                ]);
            } else {
                \Log::debug('No existe respuesta previa, creando nueva', ['idPregunta' => $idPregunta]);
                // Crear nueva respuesta
                RespuestaCuestionario::create([
                    'id_tramite' => $idTramite,
                    'id_pregunta_cuestionario' => $idPregunta,
                    'id_estado_tramite' => $idEstadoTramite,
                    'flag_valor' => $respuesta,
                    'detalle' => $detalle,
                    'fecha_sistema' => now()
                ]);
            }
        }
        
        \Log::debug('Todas las respuestas procesadas correctamente');
        return response()->json([
            'success' => true,
            'message' => 'Respuestas guardadas correctamente'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error al guardar las respuestas del cuestionario', [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Error al guardar las respuestas: ' . $e->getMessage()
        ], 500);
    }
}


    public function enCurso()
    {
        return view('tramites.index', [
            'tituloPagina' => 'Trámites en Curso',
            'soloIniciados' => true
        ]);
    }
}