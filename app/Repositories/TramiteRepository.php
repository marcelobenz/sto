<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TramiteRepository
{
    public function getTramitesData(array $params, bool $soloIniciados)
    {
        $query = DB::table('multinota as m')
            ->join('tipo_tramite_multinota as tt', 'm.id_tipo_tramite_multinota', '=', 'tt.id_tipo_tramite_multinota')
            ->join('contribuyente_multinota as cm', 'm.cuit_contribuyente', '=', 'cm.cuit')
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
                'm.cuit_contribuyente',
                'm.flag_cancelado', 
                'm.flag_rechazado',
                DB::raw("CONCAT(cm.nombre, ' ', cm.apellido) as contribuyente"),
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as usuario_interno")
            )
            ->where('te.activo', 1);

        if ($soloIniciados) {
            $query->where('m.flag_cancelado', '!=', 1)
                ->where('m.flag_rechazado', '!=', 1)
                ->where('e.nombre', 'Iniciado');
        }

        if (!empty($params['searchValue'])) {
            $query->where(function ($q) use ($params) {
                $q->where('m.id_tramite', 'like', "%{$params['searchValue']}%")
                  ->orWhere('m.cuenta', 'like', "%{$params['searchValue']}%")
                  ->orWhere('c.nombre', 'like', "%{$params['searchValue']}%")
                  ->orWhere('tt.nombre', 'like', "%{$params['searchValue']}%")
                  ->orWhere('e.nombre', 'like', "%{$params['searchValue']}%")
                  ->orWhere(DB::raw("CONCAT(cm.nombre, ' ', cm.apellido)"), 'like', "%{$params['searchValue']}%")
                  ->orWhere(DB::raw("CONCAT(u.nombre, ' ', u.apellido)"), 'like', "%{$params['searchValue']}%");
            });
        }

        $query->orderBy($params['columnName'], $params['columnSortOrder']);

        $totalFiltered = $query->count();

        $data = $query->skip($params['start'])->take($params['length'])->get();

        return [
            'data' => $this->formatTramitesData($data),
            'totalFiltered' => $totalFiltered,
            'totalData' => $this->getTotalTramitesCount()
        ];
    }

    protected function formatTramitesData($data)
    {
        return collect($data)->map(function ($item) {
            $item = (array) $item;
        
            if ($item['flag_cancelado'] == 1) {
                $item['estado'] = "Dado de Baja";
            } elseif ($item['flag_rechazado'] == 1) {
                $item['estado'] = "Rechazado";
            } elseif ($item['estado'] === "A Finalizar") {
                $item['estado'] = "Finalizado";
            }
            
            unset($item['flag_cancelado']);
            unset($item['flag_rechazado']);

            return (object) $item;
        });
    }

    protected function getTotalTramitesCount()
    {
        return DB::table('multinota as m')
            ->join('tramite_estado_tramite as te', 'm.id_tramite', '=', 'te.id_tramite')
            ->where('te.activo', 1)
            ->count();
    }

    public function getTramiteDetails($idTramite)
    {
        $detalleTramite = DB::table('multinota_seccion_valor as ms')
            ->join('seccion as s', 'ms.id_seccion', '=', 's.id_seccion')
            ->join('campo as c', 'ms.id_campo', '=', 'c.id_campo')
            ->select('ms.id_multinota_seccion_valor', 's.titulo', 'c.nombre', 'ms.valor')
            ->where('ms.id_tramite', $idTramite)
            ->orderBy('ms.id_multinota_seccion_valor', 'asc')
            ->get();

        $tramiteInfo = DB::table('multinota as m')
            ->join('tipo_tramite_multinota as ttm', 'm.id_tipo_tramite_multinota', '=', 'ttm.id_tipo_tramite_multinota')
            ->leftJoin('tramite_estado_tramite as tet', function($join) {
                $join->on('tet.id_tramite', '=', 'm.id_tramite')
                     ->where('tet.activo', 1);
            })
            ->leftJoin('usuario_interno as ui', 'ui.id_usuario_interno', '=', 'tet.id_usuario_interno')
            ->leftJoin('estado_tramite as et', 'et.id_estado_tramite', '=', 'tet.id_estado_tramite')
            ->leftJoin('prioridad as p', 'm.id_prioridad', '=', 'p.id_prioridad')
            ->select(
                'ttm.nombre',
                'm.fecha_alta',
                'ui.legajo as legajo',
                'ui.nombre as nombre_usuario',
                'ui.apellido as apellido_usuario',
                'et.nombre as estado_actual',
                'm.flag_cancelado',
                'm.flag_rechazado',
                'p.nombre as prioridad',
                'tet.id_estado_tramite as id_estado_tramite'
            )
            ->where('m.id_tramite', $idTramite)
            ->first();

            //dd( $tramiteInfo);

        if ($tramiteInfo) {
            $tramiteInfo = $this->formatTramiteInfo($tramiteInfo);
        }

        $historialTramite = DB::table('historial_tramite as h')
            ->join('evento as e', 'h.id_evento', '=', 'e.id_evento')
            ->join('usuario_interno as u', 'h.id_usuario_interno_asignado', '=', 'u.id_usuario_interno')
            ->selectRaw('COALESCE(e.descripcion, e.desc_contrib) AS descripcion, e.fecha_alta, e.clave, u.legajo, CONCAT(u.nombre, u.apellido) as usuario')
            ->where('h.id_tramite', $idTramite)
            ->orderBy('e.fecha_alta', 'desc')
            ->get();

        $tramiteArchivo = DB::table('archivo as a')
            ->join('tramite_archivo as ta', 'a.id_archivo', '=','ta.id_archivo')
            ->select('a.id_archivo', 'a.fecha_alta', 'a.nombre', 'a.descripcion', 'a.path_archivo')
            ->where('ta.id_tramite', $idTramite)
            ->orderBy('a.descripcion')
            ->get();

        $prioridades = DB::table('prioridad')->orderBy('id_prioridad')->get();

        return compact('detalleTramite', 'tramiteInfo', 'historialTramite', 'tramiteArchivo', 'prioridades');
    }

    protected function formatTramiteInfo($tramiteInfo)
    {
        if ($tramiteInfo->flag_cancelado == 1) {
            $tramiteInfo->estado_actual = 'Dado de Baja';
        } elseif ($tramiteInfo->flag_rechazado == 1) {
            $tramiteInfo->estado_actual = 'Rechazado';
        } elseif ($tramiteInfo->estado_actual === 'A Finalizar') {
            $tramiteInfo->estado_actual = 'Finalizado';
        }

        unset($tramiteInfo->flag_cancelado);
        unset($tramiteInfo->flag_rechazado);

        
        return $tramiteInfo;
    }

    public function darDeBajaTramite($idTramite, $idUsuario)
    {
        DB::beginTransaction();

        try {
            $affected = DB::table('multinota')
                ->where('id_tramite', $idTramite)
                ->update([
                    'flag_cancelado' => 1,
                    'flag_ingreso' => 1,
                    'fecha_modificacion' => now()
                ]);

            $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => 'Se dio de baja el trámite',
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 14,
                'clave' => 'CANCELAR'
            ]);

            DB::table('historial_tramite')->insert([
                'fecha' => now(),
                'id_tramite' => $idTramite,
                'id_evento' => $idEvento,
                'id_usuario_interno_asignado' => $idUsuario
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al dar de baja trámite: ' . $e->getMessage());
            return false;
        }
    }

    public function cambiarPrioridadTramite($idTramite, $idPrioridad, $idUsuario)
    {
        DB::beginTransaction();

        try {
            DB::table('multinota')
                ->where('id_tramite', $idTramite)
                ->update([
                    'id_prioridad' => $idPrioridad,
                ]);

            $prioridad = DB::table('prioridad')->where('id_prioridad', $idPrioridad)->first();
            $descripcionEvento = 'Se cambió la prioridad del trámite a: ' . ($prioridad->nombre ?? 'Desconocida');

            $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => $descripcionEvento,
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 4,
                'clave' => 'CAMBIO_PRIORIDAD'
            ]);

            DB::table('historial_tramite')->insert([
                'fecha' => now(),
                'id_tramite' => $idTramite,
                'id_evento' => $idEvento,
                'id_usuario_interno_asignado' => $idUsuario
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cambiar prioridad: ' . $e->getMessage());
            return false;
        }
    }

    public function tomarTramite($idTramite, $idUsuario)
    {
        DB::beginTransaction();

        try {
            $affected = DB::table('tramite_estado_tramite')
                ->where('id_tramite', $idTramite)
                ->where('activo', 1)
                ->update([
                    'id_usuario_interno' => $idUsuario,
                    'fecha_sistema' => now()
                ]);

            $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => 'Se reasignó el trámite',
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 3,
                'clave' => 'ASIGNACIÓN'
            ]);

            DB::table('historial_tramite')->insert([
                'fecha' => now(),
                'id_tramite' => $idTramite,
                'id_evento' => $idEvento,
                'id_usuario_interno_asignado' => $idUsuario
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al tomar trámite: ' . $e->getMessage());
            return false;
        }
    }



  public function getUltimoEstadoTramite($idTramite)
{
    return DB::table('tramite_estado_tramite as tet')
        ->where('tet.id_tramite', $idTramite)
        ->where('tet.activo', 1)
        ->orderByDesc('tet.fecha_sistema')
        ->first();
}

   
 public function getSiguienteEstado($idEstadoActual)
{
    return DB::table('configuracion_estado_tramite as cet')
        ->where('cet.id_estado_tramite', $idEstadoActual)
        ->where('cet.activo', 1)
        ->value('cet.id_proximo_estado'); 
}

public function getPosiblesEstados($idTramite)
{
    $estadoActual = DB::table('tramite_estado_tramite')
        ->where('id_tramite', $idTramite)
        ->orderByDesc('id_tramite_estado_tramite') 
        ->select('id_estado_tramite')
        ->first();

    if (!$estadoActual) {
        return collect(); 
    }

    return DB::table('configuracion_estado_tramite')
        ->join('estado_tramite', 'estado_tramite.id_estado_tramite', '=', 'configuracion_estado_tramite.id_proximo_estado')
        ->where('configuracion_estado_tramite.id_estado_tramite', $estadoActual->id_estado_tramite)
        ->select('estado_tramite.id_estado_tramite', 'estado_tramite.nombre as nombre_estado')
        ->get();
}


    
  public function crearEstadoTramite($idTramite, $idEstadoTramite, $idUsuarioAsignado, $idUsuarioEjecutor,$idUsuarioRecomendado)
{
    return DB::transaction(function () use ($idTramite, $idEstadoTramite, $idUsuarioAsignado, $idUsuarioEjecutor,$idUsuarioRecomendado) {
        $estadoActual = DB::table('tramite_estado_tramite as tet')
            ->join('estado_tramite as et', 'tet.id_estado_tramite', '=', 'et.id_estado_tramite')
            ->where('tet.id_tramite', $idTramite)
            ->where('tet.activo', 1)
            ->first();

        if (!$estadoActual) {
            return false;
        }

        if ($estadoActual->id_estado_tramite == $idEstadoTramite) {
            return false;
        }

        $estadoSiguiente = DB::table('estado_tramite')
            ->where('id_estado_tramite', $idEstadoTramite)
            ->first();

        if (!$estadoSiguiente) {
            return false;
        }

        DB::table('tramite_estado_tramite')
            ->where('id_tramite', $idTramite)
            ->where('activo', 1)
            ->update([
                'activo' => 0,
                'completo' => 1,
                'fecha_sistema' => now()
            ]);

        
        $nuevoEstadoCreado = DB::table('tramite_estado_tramite')->insert([
            'id_tramite' => $idTramite,
            'id_estado_tramite' => $idEstadoTramite,
            'id_usuario_interno' => $idUsuarioRecomendado, 
            'fecha_sistema' => now(),
            'activo' => 1,
            'completo' => 0,
            'reiniciado' => 0,
            'espera_documentacion' => 0
        ]);

        
        $descripcionEvento = 'Se avanzó el estado del trámite de "' . 
                            ($estadoActual->nombre ?? 'Desconocido') . 
                            '" a "' . 
                            ($estadoSiguiente->nombre ?? 'Desconocido') . '"';

        $idEvento = DB::table('evento')->insertGetId([
            'descripcion' => $descripcionEvento,
            'fecha_alta' => now(),
            'fecha_modificacion' => now(),
            'id_tipo_evento' => 1,
            'clave' => 'CAMBIO_ESTADO'
        ]);

        DB::table('historial_tramite')->insert([
            'fecha' => now(),
            'id_tramite' => $idTramite,
            'id_evento' => $idEvento,
            'id_usuario_interno_asignado' => $idUsuarioEjecutor
        ]);

        return $nuevoEstadoCreado;
    });
}
}