<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use DB;

class TramiteController extends Controller
{
    /**
     * Muestra la vista principal.
     */
    public function index()
    {

        return view('tramites.index', [
            'tituloPagina' => 'Todos los Trámites',
            'soloIniciados' => false
        ]);
    }

    /**
     * Proporciona los datos para el DataTable.
     */
    public function getTramitesData(Request $request)
    {
        $columnIndex = $request->get('order')[0]['column']; // Índice de la columna a ordenar
        $columnName = $request->get('columns')[$columnIndex]['data']; // Nombre de la columna
        $columnSortOrder = $request->get('order')[0]['dir']; // Orden (asc o desc)
        $searchValue = $request->get('search')['value']; // Valor de búsqueda
        $soloIniciados = $request->get('soloIniciados') === 'true';
        $idUsuarioSesion = $request->get('id_usuario_sesion');
        $soloAsignados = $request->get('soloAsignados') === 'true';

        Log::debug('soloIniciados recibido:', ['soloIniciados' => $soloIniciados]);

        // Construir la consulta base
        $query = DB::table('multinota as m')
            ->join('tramite as t', 'm.id_tramite', '=', 't.id_tramite')
            ->join('tipo_tramite_multinota as tt', 'm.id_tipo_tramite_multinota', '=', 'tt.id_tipo_tramite_multinota')
            ->join('contribuyente_externo as ce', 't.id_tramite', '=', 'ce.id_tramite')
            ->join('tramite_estado_tramite as te', 'm.id_tramite', '=', 'te.id_tramite')
            ->join('usuario_interno as u', 'te.id_usuario_interno', '=', 'u.id_usuario_interno')
            ->join('categoria as c', 'tt.id_categoria', '=', 'c.id_categoria')
            ->join('estado_tramite as e', 'te.id_estado_tramite', '=', 'e.id_estado_tramite')
            ->select(
                'm.id_tramite as id_tramite',
                'm.cuenta',
                'c.nombre as nombre_categoria',
                'tt.nombre as tipo_tramite',
                'e.nombre as estado',
                'm.fecha_alta',
                't.cuit_contribuyente',
                't.flag_cancelado', 
                't.flag_rechazado',
                DB::raw("CONCAT(ce.nombre, ' ', ce.apellido) as contribuyente"),
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as usuario_interno")
            )
            ->where('te.activo', 1);
        if ($soloIniciados) {
            $query->where('t.flag_cancelado', '!=', 1)
            ->where('t.flag_rechazado', '!=', 1)
            ->where('e.nombre', 'Iniciado');
        }
        if ($soloAsignados && $idUsuarioSesion) {
            $query->where('u.id_usuario_interno', $idUsuarioSesion);
        }
                
        // Filtro de búsqueda
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('m.id_tramite', 'like', "%{$searchValue}%")
                  ->orWhere('m.cuenta', 'like', "%{$searchValue}%")
                  ->orWhere('c.nombre', 'like', "%{$searchValue}%")
                  ->orWhere('tt.nombre', 'like', "%{$searchValue}%")
                  ->orWhere('e.nombre', 'like', "%{$searchValue}%")
                  ->orWhere(DB::raw("CONCAT(ce.nombre, ' ', ce.apellido)"), 'like', "%{$searchValue}%")
                  ->orWhere(DB::raw("CONCAT(u.nombre, ' ', u.apellido)"), 'like', "%{$searchValue}%");
            });
        }
    
        // Ordenar por columna seleccionada
        $query->orderBy($columnName, $columnSortOrder);
    
        // Total de registros después del filtro
        $totalFiltered = $query->count();
    
        // Paginación
        $data = $query->skip($request->get('start'))->take($request->get('length'))->get();

        // Reemplazar "A finalizar" con "Finalizado" en la columna estado
        $data = collect($data)->map(function ($item) {
            $item = (array) $item;
        
            if ($item['flag_cancelado'] == 1) {
                $item['estado'] = "Dado de Baja";
            } elseif ($item['flag_rechazado'] == 1) {
                $item['estado'] = "Rechazado";
            }elseif ($item['estado'] === "A Finalizar") {
                $item['estado'] = "Finalizado";
            }
            unset($item['flag_cancelado']);
            unset($item['flag_rechazado']);

            return (object) $item;
        });

        // Total de registros sin filtro
        $totalData = DB::table('multinota as m')
            ->join('tramite_estado_tramite as te', 'm.id_tramite', '=', 'te.id_tramite')
            ->where('te.activo', 1)
            ->count();
    
        // Respuesta en formato JSON
        return response()->json([
            "draw" => intval($request->get('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }

    public function show($idTramite)
    {
        $idEstadoTramite = DB::table('tramite_estado_tramite')
            ->where('id_tramite', $idTramite)
            ->where('activo', 1)
            ->value('id_estado_tramite');
        
        $usuariosAsignables = DB::table('estado_tramite_asignable')
            ->where('id_estado_tramite', $idEstadoTramite)
            ->get();
        
        $idsUsuarios = $usuariosAsignables
            ->whereNotNull('id_usuario_interno')
            ->pluck('id_usuario_interno')
            ->toArray();
        
        $idsGrupos = $usuariosAsignables
            ->whereNull('id_usuario_interno')
            ->pluck('id_grupo_interno')
            ->unique()
            ->toArray();
        
        $usuarios = DB::table('usuario_interno')
            ->where(function ($query) use ($idsUsuarios, $idsGrupos) {
                $query->whereIn('id_usuario_interno', $idsUsuarios);
                if (!empty($idsGrupos)) {
                    $query->orWhereIn('id_grupo_interno', $idsGrupos);
                }
            })
            ->orderBy('apellido')
            ->get();
    
        $detalleTramite = DB::table('multinota_seccion_valor as ms')
            ->join('seccion as s', 'ms.id_seccion', '=', 's.id_seccion')
            ->join('campo as c', 'ms.id_campo', '=', 'c.id_campo')
            ->select('ms.id_multinota_seccion_valor', 's.titulo', 'c.nombre', 'ms.valor')
            ->where('ms.id_tramite', $idTramite)
            ->orderBy('ms.id_multinota_seccion_valor', 'asc')
            ->get();

        $tramiteInfo = DB::table('multinota as m')
            ->join('tramite as t', 'm.id_tramite', '=', 't.id_tramite') // 👈 Agregá esta línea
            ->join('tipo_tramite_multinota as ttm', 'm.id_tipo_tramite_multinota', '=', 'ttm.id_tipo_tramite_multinota')
            ->leftJoin('tramite_estado_tramite as tet', function($join) {
                $join->on('tet.id_tramite', '=', 'm.id_tramite')
                     ->where('tet.activo', 1);
            })
            ->leftJoin('usuario_interno as ui', 'ui.id_usuario_interno', '=', 'tet.id_usuario_interno')
            ->leftJoin('estado_tramite as et', 'et.id_estado_tramite', '=', 'tet.id_estado_tramite')
            ->leftJoin('prioridad as p', 't.id_prioridad', '=', 'p.id_prioridad')
            ->select(
                'ttm.nombre',
                'm.fecha_alta',
                'ui.nombre as nombre_usuario',
                'ui.apellido as apellido_usuario',
                'et.nombre as estado_actual',
                't.flag_cancelado',
                't.flag_rechazado',
                'p.nombre as prioridad',
                'ui.id_usuario_interno as id_asignado'
            )
            ->where('m.id_tramite', $idTramite)
            ->first();
        
 
            if ($tramiteInfo) {
                if ($tramiteInfo->flag_cancelado == 1) {
                    $tramiteInfo->estado_actual = 'Dado de Baja';
                } elseif ($tramiteInfo->flag_rechazado == 1) {
                    $tramiteInfo->estado_actual = 'Rechazado';
                } elseif ($tramiteInfo->estado_actual === 'A Finalizar') {
                    $tramiteInfo->estado_actual = 'Finalizado';
                }
            
                unset($tramiteInfo->flag_cancelado);
                unset($tramiteInfo->flag_rechazado);
            }
            
        $historialTramite = DB::table('historial_tramite as h')
            ->join('evento as e', 'h.id_evento', '=', 'e.id_evento')
            ->join('usuario_interno as u', 'h.id_usuario_interno_asignado', '=', 'u.id_usuario_interno')
            ->leftJoin('estado_tramite as est', 'est.id_estado_tramite', '=', 'h.id_estado_tramite')
            ->selectRaw('
                COALESCE(e.descripcion, e.desc_contrib) AS descripcion,
                e.fecha_alta,
                e.clave,
                u.legajo,
                CONCAT(u.nombre, " ", u.apellido) as usuario,
                est.nombre as nombre_estado
            ')
            ->where('h.id_tramite', $idTramite)
            ->orderBy('e.fecha_alta', 'desc') // Ordenar por fecha de forma descendente
            ->get();

        $tramiteArchivo = DB::table('archivo as a')
            ->join('tramite_archivo as ta', 'a.id_archivo', '=','ta.id_archivo')
            ->select('a.id_archivo', 'a.fecha_alta', 'a.nombre', 'a.descripcion', 'a.path_archivo')
            ->where('ta.id_tramite', $idTramite)
            ->orderBy('a.descripcion')
            ->get();

        $prioridades = DB::table('prioridad')->orderBy('id_prioridad')->get();

        return view('tramites.detalle', compact('detalleTramite', 'idTramite', 'tramiteInfo', 'historialTramite', 'tramiteArchivo', 'prioridades', 'usuarios'));
    }

    public function darDeBaja(Request $request)
    {
        try {
            Log::debug('Inicio de la función darDeBaja');
            Log::debug('Contenido del Request:', $request->all());
    
            $idTramite = $request->input('idTramite');
            Log::debug('ID del trámite recibido: ' . $idTramite);
    
            DB::beginTransaction(); // 1️⃣ Iniciar transacción
            Log::debug('Transacción iniciada');
    
            // 2️⃣ Actualizar el trámite
            $affected = DB::table('tramite')
                ->where('id_tramite', $idTramite)
                ->update([
                    'flag_cancelado' => 1,
                    'flag_ingreso' => 1,
                    'fecha_modificacion' => now()
                ]);
            Log::debug("Trámite actualizado. Registros afectados: " . $affected);
    
            // 3️⃣ Insertar el evento
            $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => 'Se dio de baja el trámite',
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 14,
                'clave' => 'CANCELAR'
            ]);
            Log::debug("Evento insertado con ID: " . $idEvento);
    
            // 4️⃣ Registrar en historial_tramite
            $idHistorial = DB::table('historial_tramite')->insertGetId([
                'fecha' => now(),
                'id_tramite' => $idTramite,
                'id_evento' => $idEvento,
                'id_usuario_interno_asignado' => 107
            ], 'id_historial_tramite'); // <- especificar el nombre de la PK autoincremental
            
            Log::debug("Historial de trámite registrado con ID: " . $idHistorial);
           
            
            // Inmediatamente consultar
            $ultimoHistorial = DB::table('historial_tramite')
                ->where('id_tramite', $idTramite)
                ->orderBy('id_historial_tramite', 'desc')
                ->first();
            
            Log::debug("Último historial tras insert:", (array) $ultimoHistorial);

            DB::commit(); // 5️⃣ Confirmar cambios
            Log::debug("Transacción confirmada");
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack(); // 6️⃣ Revertir cambios en caso de error
            Log::error('Error en darDeBaja: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cambiarPrioridad(Request $request)
    {
        $request->validate([
            'id_tramite' => 'required|exists:tramite,id_tramite',
            'id_prioridad' => 'required|exists:prioridad,id_prioridad',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar prioridad
            DB::table('tramite')
                ->where('id_tramite', $request->id_tramite)
                ->update([
                    'id_prioridad' => $request->id_prioridad,
                    'fecha_modificacion' => now()
                ]);

            // Registrar evento
            $prioridad = DB::table('prioridad')->where('id_prioridad', $request->id_prioridad)->first();
            $descripcionEvento = 'Se cambió la prioridad del trámite a: ' . ($prioridad->nombre ?? 'Desconocida');

            $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => $descripcionEvento,
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 4, // Asigná un tipo de evento específico
                'clave' => 'CAMBIO_PRIORIDAD'
            ]);

            // Insertar en historial
            DB::table('historial_tramite')->insert([
                'fecha' => now(),
                'id_tramite' => $request->id_tramite,
                'id_evento' => $idEvento,
                'id_usuario_interno_asignado' => auth()->user()->id_usuario_interno ?? 107
            ]);

            // Registrar log en storage/logs/laravel.log
            Log::info('Historial creado', [
                'tramite' => $request->id_tramite,
                'evento' => $idEvento,
                'usuario' => auth()->user()->id_usuario_interno ?? 107,
                'timestamp' => now()->toDateTimeString()
            ]);
            DB::commit();

            return redirect()->back()->with('success', 'Prioridad actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cambiar prioridad: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar prioridad.');
        }
    }

    public function tomarTramite(Request $request)
    {
        try{
            $idTramite = $request->input('idTramite');
            $usuario = Session::get('usuario_interno');

            // ✅ 1. Verificar si ya está asignado al mismo usuario
            $asignado = DB::table('tramite_estado_tramite')
            ->where('id_tramite', $idTramite)
            ->where('activo', 1)
            ->value('id_usuario_interno');

            if ($asignado == $usuario->id_usuario_interno) {
                return response()->json(['success' => false,'message' => 'El trámite ya está tomado por este usuario.']);
            }

            // ✅ 2. Verificar si el usuario puede tomar el trámite en este estado
            $idEstadoTramite = DB::table('tramite_estado_tramite')
                ->where('id_tramite', $idTramite)
                ->where('activo', 1)
                ->value('id_estado_tramite');

            $esAsignable = DB::table('estado_tramite_asignable')
                ->where('id_estado_tramite', $idEstadoTramite)
                ->where('id_usuario_interno', $usuario->id_usuario_interno)
                ->exists();

            if (!$esAsignable) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para tomar el trámite en este estado.'
                ]);
            }

            DB::beginTransaction();
            // 2️⃣ Actualizar el trámite
            $affected = DB::table('tramite_estado_tramite')
                ->where('id_tramite', $idTramite)
                ->where('activo', 1)
                ->update([
                    'id_usuario_interno' => $usuario->id_usuario_interno,
                    'fecha_sistema' => now()
                ]);

            // 3️⃣ Insertar el evento
            $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => 'Se tomó el trámite',
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 22,
                'clave' => 'TRAMITE_TOMADO'
            ]);
            Log::debug("Evento insertado con ID: " . $idEvento);
    
            // 4️⃣ Registrar en historial_tramite
            $idHistorial = DB::table('historial_tramite')->insertGetId([
                'fecha' => now(),
                'id_tramite' => $idTramite,
                'id_evento' => $idEvento,
                'id_usuario_interno_asignado' => $usuario->id_usuario_interno
            ], 'id_historial_tramite'); // <- especificar el nombre de la PK autoincremental
            
            Log::debug("Historial de trámite registrado con ID: " . $idHistorial);
            DB::commit(); // 5️⃣ Confirmar cambios
            Log::debug("Transacción confirmada");
    
            return response()->json(['success' => true]);
        }catch (\Exception $e) {
            DB::rollBack(); // 6️⃣ Revertir cambios en caso de error
            Log::error('Error al tomar trámite: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

    }

    public function enCurso()
    {
        return view('tramites.index', [
            'soloIniciados' => true,
            'tituloPagina' => 'Trámites en Curso'
        ]);
    }
    
    public function reasignar(Request $request)
    {
        try {
            $idTramite = $request->input('idTramite');
            $idUsuario = $request->input('id_usuario_interno');

            DB::beginTransaction();
    
            DB::table('tramite_estado_tramite')
                ->where('id_tramite', $idTramite)
                ->where('activo', 1)
                ->update([
                    'id_usuario_interno' => $idUsuario,
                    'fecha_sistema' => now()
                ]);
    
            $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => 'Se reasignó el trámite a otro usuario',
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 3,
                'clave' => 'REASIGNACIÓN'
            ]);
    
            DB::table('historial_tramite')->insert([
                'fecha' => now(),
                'id_tramite' => $idTramite,
                'id_evento' => $idEvento,
                'id_usuario_interno_asignado' => $idUsuario
            ]);
    
            DB::commit();
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en reasignarTramite: ' . $e->getMessage());
            return response()->json(['success' => false]);
        }
    }

    public function bandejaPersonal()
    {
        return view('tramites.index', [
            'tituloPagina' => 'Bandeja Personal',
            'soloIniciados' => false,
            'soloAsignados' => true,
            'id_usuario_sesion' => Session::get('usuario_interno')->id_usuario_interno ?? null
        ]);
    }

    public function getUsuariosAsignables($idTramite)
    {
        $idEstadoTramite = DB::table('tramite_estado_tramite')
            ->where('id_tramite', $idTramite)
            ->where('activo', 1)
            ->value('id_estado_tramite');

        $asignables = DB::table('estado_tramite_asignable')
            ->where('id_estado_tramite', $idEstadoTramite)
            ->get();

        $idsUsuarios = $asignables->whereNotNull('id_usuario_interno')->pluck('id_usuario_interno')->toArray();
        $idsGrupos = $asignables->whereNull('id_usuario_interno')->pluck('id_grupo_interno')->unique()->toArray();

        $usuarios = DB::table('usuario_interno')
            ->where(function ($query) use ($idsUsuarios, $idsGrupos) {
                $query->whereIn('id_usuario_interno', $idsUsuarios);
                if (!empty($idsGrupos)) {
                    $query->orWhereIn('id_grupo_interno', $idsGrupos);
                }
            })
            ->orderBy('apellido')
            ->get();

        return response()->json($usuarios);
    }

    public function completarEstado(Request $request)
    {
        try {
            $idTramite = $request->input('idTramite');
            $idEstadoElegido = $request->input('id_estado_tramite_siguiente'); // si viene desde modal
            $usuario = Session::get('usuario_interno');
    
            // 1️⃣ Obtener estado actual activo
            $estadoActual = DB::table('tramite_estado_tramite')
                ->where('id_tramite', $idTramite)
                ->where('activo', 1)
                ->first();
    
            if (!$estadoActual) {
                return response()->json(['success' => false, 'message' => 'No se encontró un estado activo para el trámite.']);
            }
    
            if ($estadoActual->id_usuario_interno != $usuario->id_usuario_interno) {
                return response()->json(['success' => false, 'message' => 'No tiene permiso para completar este trámite.']);
            }
    
            // 2️⃣ Obtener id_tipo_tramite_multinota desde tabla multinota
            $tipoTramite = DB::table('multinota')
                ->where('id_tramite', $idTramite)
                ->value('id_tipo_tramite_multinota');
    
            if (!$tipoTramite) {
                return response()->json(['success' => false, 'message' => 'No se encontró el tipo de trámite.']);
            }
    
            // 3️⃣ Verificar si el estado permite elegir camino
            $estado = DB::table('estado_tramite')
                ->where('id_estado_tramite', $estadoActual->id_estado_tramite)
                ->first();
    
            if (!$estado) {
                return response()->json(['success' => false, 'message' => 'No se encontró información del estado actual.']);
            }
    
            $estadoSiguiente = null;
    
            if ($estado->puede_elegir_camino) {
                // 🔄 Obtener posibles caminos desde configuracion_estado_tramite
                $caminos = DB::table('configuracion_estado_tramite as c')
                    ->join('estado_tramite as e', 'e.id_estado_tramite', '=', 'c.id_proximo_estado')
                    ->where('c.id_tipo_tramite_multinota', $tipoTramite)
                    ->where('c.id_estado_tramite', $estadoActual->id_estado_tramite)
                    ->where('c.activo', 1)
                    ->select('c.id_proximo_estado as id', 'e.nombre as nombre')
                    ->get();
    
                if (!$idEstadoElegido) {
                    return response()->json([
                        'success' => true,
                        'requires_selection' => true,
                        'siguientes_estados' => $caminos
                    ]);
                }
    
                if (!$caminos->pluck('id')->contains($idEstadoElegido)) {
                    return response()->json(['success' => false, 'message' => 'El estado seleccionado no es válido para este trámite.']);
                }
    
                $estadoSiguiente = $idEstadoElegido;
            } else {
                // 🔁 Solo un camino posible
                $estadoDestino = DB::table('configuracion_estado_tramite')
                    ->where('id_tipo_tramite_multinota', $tipoTramite)
                    ->where('id_estado_tramite', $estadoActual->id_estado_tramite)
                    ->where('activo', 1)
                    ->value('id_proximo_estado');
    
                if (!$estadoDestino) {
                    return response()->json(['success' => false, 'message' => 'Este estado no tiene un estado siguiente configurado.']);
                }
    
                $estadoSiguiente = $estadoDestino;
            }
    
            // 💾 Iniciar transacción
            DB::beginTransaction();
    
            // 4️⃣ Marcar estado actual como completo e inactivo
            DB::table('tramite_estado_tramite')
                ->where('id_tramite_estado_tramite', $estadoActual->id_tramite_estado_tramite)
                ->update(['completo' => 1, 'activo' => 0]);
    
            // 5️⃣ Evento de finalización
            $idEventoCompletar = DB::table('evento')->insertGetId([
                'id_tipo_evento' => 12,
                'descripcion' => "Se completó el estado\n{$estadoActual->id_estado_tramite}",
                'clave' => 'COMPLETAR_ESTADO',
                'fecha_alta' => now(),
                'fecha_modificacion' => now()
            ]);
    
            DB::table('historial_tramite')->insert([
                'id_estado_tramite' => $estadoActual->id_estado_tramite,
                'id_usuario_interno_asignado' => $usuario->id_usuario_interno,
                'id_evento' => $idEventoCompletar,
                'id_tramite' => $idTramite,
                'id_usuario_interno_administrador' => $usuario->id_usuario_interno,
                'fecha' => now()
            ]);
    
            // 6️⃣ Crear nuevo estado
            DB::table('tramite_estado_tramite')->insert([
                'id_tramite' => $idTramite,
                'id_estado_tramite' => $estadoSiguiente,
                'id_usuario_interno' => $usuario->id_usuario_interno,
                'activo' => 1,
                'completo' => 0,
                'reiniciado' => 0,
                'espera_documentacion' => 0,
                'fecha_sistema' => now(),
                'id_tramite_estado_tramite_anterior' => $estadoActual->id_tramite_estado_tramite
            ]);
    
            // 7️⃣ Evento de avance
            $idEventoAvance = DB::table('evento')->insertGetId([
                'id_tipo_evento' => 11,
                'descripcion' => "Se avanzó al estado\n{$estadoSiguiente}",
                'clave' => 'AVANCE_ESTADO',
                'fecha_alta' => now(),
                'fecha_modificacion' => now()
            ]);
    
            DB::table('historial_tramite')->insert([
                'id_estado_tramite' => $estadoSiguiente,
                'id_usuario_interno_asignado' => $usuario->id_usuario_interno,
                'id_evento' => $idEventoAvance,
                'id_tramite' => $idTramite,
                'id_usuario_interno_administrador' => $usuario->id_usuario_interno,
                'fecha' => now()
            ]);
    
            DB::commit();
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al completar estado: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }
        
}
