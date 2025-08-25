<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TramiteService;
use App\Models\Multinota;
use App\Models\TramiteEstadoTramite;
use App\Models\RespuestaCuestionario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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

    // Si idEstadoNuevo es null o vacío, pasamos null al service
    $idEstadoNuevo = !empty($idEstadoNuevo) ? $idEstadoNuevo : null;
    
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
        
        // Variables para controlar las acciones a realizar
        $debeRechazar = false;
        $debeFinalizar = false;
        
        foreach ($respuestas as $idPregunta => $respuesta) {
            \Log::debug('Procesando respuesta', [
                'idPregunta' => $idPregunta,
                'respuesta' => $respuesta,
                'detalle' => $detalles[$idPregunta] ?? null
            ]);
            
            // Verificar flags de la pregunta
            $pregunta = DB::table('pregunta')
                ->where('id_pregunta', $idPregunta)
                ->select('flag_rechazo_no', 'flag_finalizacion_si')
                ->first();
            
            if ($pregunta) {
                // Verificar si se debe rechazar (respuesta NO y flag_rechazo_no = 1)
                if ($pregunta->flag_rechazo_no == 1 && $respuesta == 0) {
                    \Log::debug('Pregunta con flag_rechazo_no = 1 y respuesta NO detectada', [
                        'idPregunta' => $idPregunta,
                        'flag_rechazo_no' => $pregunta->flag_rechazo_no,
                        'respuesta' => $respuesta
                    ]);
                    $debeRechazar = true;
                }
                
                // Verificar si se debe finalizar (respuesta SI y flag_finalizacion_si = 1)
                if ($pregunta->flag_finalizacion_si == 1 && $respuesta == 1) {
                    \Log::debug('Pregunta con flag_finalizacion_si = 1 y respuesta SI detectada', [
                        'idPregunta' => $idPregunta,
                        'flag_finalizacion_si' => $pregunta->flag_finalizacion_si,
                        'respuesta' => $respuesta
                    ]);
                    $debeFinalizar = true;
                }
            }
            
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
        
        // Procesar acciones después de guardar todas las respuestas
        $mensaje = 'Respuestas guardadas correctamente';
        
        // Si se debe rechazar el trámite, actualizar flag_rechazado
        if ($debeRechazar) {
            \Log::debug('Actualizando flag_rechazado del trámite a 1', ['id_tramite' => $idTramite]);
            $tramite->update(['flag_rechazado' => 1]);
            $mensaje .= '. El trámite ha sido marcado como rechazado.';
        }
        
        // Si se debe finalizar el trámite, avanzar al estado A_FINALIZAR
        if ($debeFinalizar) {
            \Log::debug('Procesando finalización del trámite', ['id_tramite' => $idTramite]);
            
            // Obtener el id_tipo_tramite_multinota del trámite
            $tipoTramite = DB::table('multinota')
                ->where('id_tramite', $idTramite)
                ->select('id_tipo_tramite_multinota')
                ->first();
            
            if ($tipoTramite) {
                \Log::debug('Tipo de trámite obtenido', ['id_tipo_tramite' => $tipoTramite->id_tipo_tramite_multinota]);
                
                // Buscar el estado final (el que tiene id_proximo_estado = null)
                $estadoFinal = DB::table('configuracion_estado_tramite')
                    ->where('id_tipo_tramite_multinota', $tipoTramite->id_tipo_tramite_multinota)
                    ->where('activo', 1)
                    ->whereNull('id_proximo_estado')
                    ->select('id_estado_tramite')
                    ->first();
                
                if ($estadoFinal) {
                    \Log::debug('Estado final encontrado', ['id_estado_final' => $estadoFinal->id_estado_tramite]);
                    
                    try {
                        // Desactivar el estado actual del trámite
                        DB::table('tramite_estado_tramite')
                            ->where('id_tramite', $idTramite)
                            ->where('activo', 1)
                            ->update(['activo' => 0]);
                        
                        // Agregar el nuevo estado final con completo = 1
                        DB::table('tramite_estado_tramite')->insert([
                            'id_tramite' => $idTramite,
                            'id_estado_tramite' => $estadoFinal->id_estado_tramite,
                            'fecha_sistema' => now(),
                            'activo' => 1,
                            'completo' => 1,
                            'id_usuario_interno' => auth()->user()->legajo ?? null,
                        ]);
                        
                        \Log::debug('Trámite movido al estado final', [
                            'id_tramite' => $idTramite,
                            'id_estado_final' => $estadoFinal->id_estado_tramite
                        ]);
                        
                        $mensaje .= '. El trámite ha sido movido al estado de finalización.';
                        
                    } catch (\Exception $e) {
                        \Log::error('Error al actualizar estado del trámite', [
                            'id_tramite' => $idTramite,
                            'error' => $e->getMessage()
                        ]);
                        // No fallar completamente, solo registrar el error
                        $mensaje .= '. Error al actualizar el estado del trámite.';
                    }
                } else {
                    \Log::warning('No se encontró estado final para el tipo de trámite', [
                        'id_tipo_tramite' => $tipoTramite->id_tipo_tramite_multinota
                    ]);
                    $mensaje .= '. No se pudo determinar el estado final del trámite.';
                }
            } else {
                \Log::error('No se pudo obtener el tipo de trámite', ['id_tramite' => $idTramite]);
                $mensaje .= '. Error al obtener información del tipo de trámite.';
            }
        }
        
        \Log::debug('Todas las respuestas procesadas correctamente', [
            'tramite_rechazado' => $debeRechazar,
            'tramite_finalizado' => $debeFinalizar
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'tramite_rechazado' => $debeRechazar,
            'tramite_finalizado' => $debeFinalizar
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



public function pedirDocumentacion(Request $request)
{
    try {
        $idTramite = $request->input('idTramite');
        
        // Buscar el estado actual del trámite (activo=1)
        $tramiteEstado = DB::table('tramite_estado_tramite')
            ->where('id_tramite', $idTramite)
            ->where('activo', 1)
            ->first();
            
        if (!$tramiteEstado) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un estado activo para este trámite'
            ]);
        }
        
        // Actualizar el flag espera_documentacion
        DB::table('tramite_estado_tramite')
            ->where('id_tramite_estado_tramite', $tramiteEstado->id_tramite_estado_tramite)
            ->update(['espera_documentacion' => 1]);
            

        $descripcionEvento = 'Se solicita documentación';

        $idUsuarioEjecutor = Session::get('usuario_interno')->id_usuario_interno;

        $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => $descripcionEvento,
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 1,
                'clave' => 'PEDIR_DOCUMENTACION'
            ]);

            DB::table('historial_tramite')->insert([
                'fecha' => now(),
                'id_tramite' => $idTramite,
                'id_evento' => $idEvento,
                'id_usuario_interno_asignado' => $idUsuarioEjecutor
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Se ha solicitado documentación adicional correctamente'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al solicitar documentación: ' . $e->getMessage()
        ]);
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