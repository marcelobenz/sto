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
                'p.nombre as prioridad'
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
            ->selectRaw('COALESCE(e.descripcion, e.desc_contrib) AS descripcion, e.fecha_alta, e.clave, u.legajo, CONCAT(u.nombre, u.apellido) as usuario')
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

        return view('tramites.detalle', compact('detalleTramite', 'idTramite', 'tramiteInfo', 'historialTramite', 'tramiteArchivo', 'prioridades'));
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
                'descripcion' => 'Se reasignó el trámite',
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 3,
                'clave' => 'ASIGNACIÓN'
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
            'tituloPagina' => 'Trámites en Curso',
            'soloIniciados' => true
        ]);
    }
    
    
}
