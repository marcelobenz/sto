<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdministrarWorkflowRepository
{
    public function obtenerTramitesParaDataTable()
    {
        return DB::table('tipo_tramite_multinota')
            ->join('categoria', 'tipo_tramite_multinota.id_categoria', '=', 'categoria.id_categoria')
            ->leftJoin('configuracion_estado_tramite', 'tipo_tramite_multinota.id_tipo_tramite_multinota', '=', 'configuracion_estado_tramite.id_tipo_tramite_multinota')
            ->where('tipo_tramite_multinota.baja_logica', 0)
            ->select([
                'tipo_tramite_multinota.id_tipo_tramite_multinota',
                'categoria.nombre as categoria',
                'tipo_tramite_multinota.nombre as nombre_tipo_tramite',
                DB::raw('IF(configuracion_estado_tramite.id_tipo_tramite_multinota IS NULL, 0, 1) as existe_configuracion'),
                DB::raw("IF(EXISTS (
                    SELECT 1 FROM configuracion_estado_tramite cet
                    WHERE cet.id_tipo_tramite_multinota = tipo_tramite_multinota.id_tipo_tramite_multinota
                    AND cet.publico = 0
                ), 1, 0) as existe_borrador")
            ])
            ->groupBy('tipo_tramite_multinota.id_tipo_tramite_multinota', 'categoria.nombre', 'tipo_tramite_multinota.nombre');
    }

    public function obtenerTipoTramite($id)
    {
        return DB::table('tipo_tramite_multinota')
            ->where('id_tipo_tramite_multinota', $id)
            ->first();
    }

    public function obtenerEstadosConfigurados($idTipoTramite, $publico = 1, $activo = 1)
    {
        return DB::table('estado_tramite as et')
            ->join('configuracion_estado_tramite as cet', 'et.id_estado_tramite', '=', 'cet.id_estado_tramite')
            ->where('cet.id_tipo_tramite_multinota', $idTipoTramite)
            ->where('cet.publico', $publico)
            ->where('cet.activo', $activo)
            ->select('et.id_estado_tramite', 'et.nombre', 'et.tipo', 'et.puede_rechazar', 'et.puede_pedir_documentacion', 'et.estado_tiene_expediente')
            ->distinct()
            ->get();
    }

    public function obtenerEstadosPosteriores($idTipoTramite, $publico = 1)
    {
        return DB::table('configuracion_estado_tramite as cet')
            ->join('estado_tramite as et', 'cet.id_proximo_estado', '=', 'et.id_estado_tramite')
            ->where('cet.id_tipo_tramite_multinota', $idTipoTramite)
            ->where('cet.publico', $publico)
            ->whereNotNull('cet.id_proximo_estado')
            ->select('cet.id_estado_tramite', 'et.nombre as nombre_posterior')
            ->get();
    }

    public function obtenerAsignaciones($idsEstados)
    {
        return DB::table('estado_tramite_asignable')
            ->whereIn('id_estado_tramite', $idsEstados)
            ->get();
    }

    public function crearNuevosEstados($estados)
    {
        $mapaEstados = [];
        $now = now();

        foreach ($estados as $nombre => $config) {
            $id = DB::table('estado_tramite')->insertGetId([
                'fecha_sistema' => $now,
                'nombre' => $nombre,
                'tipo' => strtoupper(str_replace(' ', '_', $nombre)),
                'puede_rechazar' => $config['puede_rechazar'] ?? 0,
                'puede_pedir_documentacion' => $config['puede_pedir_documentacion'] ?? 0,
                'puede_elegir_camino' => 0,
                'estado_tiene_expediente' => $config['estado_tiene_expediente'] ?? 0,
            ]);
            $mapaEstados[$nombre] = $id;
        }

        return $mapaEstados;
    }

    public function crearConfiguraciones($configuraciones, $version, $idTipoTramite, $publico = 1, $activo = 1)
    {
        $now = now();
        $transiciones = [];

        foreach ($configuraciones as $config) {
            if (empty($config['posteriores'])) {
                $transiciones[] = [
                    'fecha_sistema' => $now,
                    'id_estado_tramite' => $config['id_estado'],
                    'id_proximo_estado' => null,
                    'version' => $version,
                    'publico' => $publico,
                    'id_tipo_tramite_multinota' => $idTipoTramite,
                    'activo' => $activo
                ];
            } else {
                foreach ($config['posteriores'] as $posterior) {
                    $transiciones[] = [
                        'fecha_sistema' => $now,
                        'id_estado_tramite' => $config['id_estado'],
                        'id_proximo_estado' => $posterior['id'],
                        'version' => $version,
                        'publico' => $publico,
                        'id_tipo_tramite_multinota' => $idTipoTramite,
                        'activo' => $activo
                    ];
                }
            }
        }

        DB::table('configuracion_estado_tramite')->insert($transiciones);
    }

    public function crearAsignaciones($asignaciones)
    {
        if (!empty($asignaciones)) {
            DB::table('estado_tramite_asignable')->insert($asignaciones);
        }
    }

    public function desactivarConfiguracionesAnteriores($idTipoTramite)
    {
        DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $idTipoTramite)
            ->where('activo', 1)
            ->update(['activo' => 0]);
    }

    public function eliminarBorradores($idTipoTramite)
    {
        DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $idTipoTramite)
            ->where('publico', 0)
            ->delete();
    }

    public function obtenerVersionesExistentes($idTipoTramite)
    {
        return DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $idTipoTramite)
            ->select('version', 'publico', DB::raw('MAX(fecha_sistema) as fecha_maxima'))
            ->groupBy('version', 'publico')
            ->orderBy('fecha_maxima', 'desc')
            ->get();
    }

    public function eliminarVersionesAntiguas($idTipoTramite, $versionesAEliminar)
    {
        DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $idTipoTramite)
            ->where('publico', 1)
            ->whereIn('version', $versionesAEliminar)
            ->delete();
    }

    public function actualizarTramitesActivos($idTipoTramite, $mapaEstados)
    {
        $tramitesActivos = DB::table('tramite_estado_tramite as tet')
            ->join('multinota as m', 'tet.id_tramite', '=', 'm.id_tramite')
            ->where('m.id_tipo_tramite_multinota', $idTipoTramite)
            ->where('tet.activo', 1)
            ->exists();

        if (!$tramitesActivos) return;

        $tramites = DB::table('tramite_estado_tramite as tet')
            ->join('multinota as m', 'tet.id_tramite', '=', 'm.id_tramite')
            ->join('estado_tramite as et', 'tet.id_estado_tramite', '=', 'et.id_estado_tramite')
            ->where('m.id_tipo_tramite_multinota', $idTipoTramite)
            ->where('tet.activo', 1)
            ->where('m.flag_rechazado', 0)
            ->where('m.flag_cancelado', 0)
            ->where(function ($query) {
                $query->where('et.nombre', '!=', 'A Finalizar')
                    ->orWhere('tet.completo', '!=', 1);
            })
            ->select('tet.*', 'et.nombre')
            ->get();

        foreach ($tramites as $tramite) {
            if (isset($mapaEstados[$tramite->nombre])) {
                DB::table('tramite_estado_tramite')
                    ->where('id_estado_tramite', $tramite->id_estado_tramite)
                    ->update(['id_estado_tramite' => $mapaEstados[$tramite->nombre]]);
            }
        }
    }
}