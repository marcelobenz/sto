<?php

namespace App\Repositories;

use App\Models\Cuestionario;
use App\Models\Pregunta;
use App\Models\ConfiguracionEstadoTramite;
use App\Models\EstadoTramite;
use App\Models\TipoTramiteMultinota;
use App\Models\CuestionarioEstadoTramite;
use Illuminate\Support\Facades\DB;

class CuestionarioRepository
{
    public function getAll()
    {
        return Cuestionario::all();
    }

    public function findWithPreguntas($id)
    {
        return Cuestionario::with('preguntas')->findOrFail($id);
    }

    public function create(array $data)
    {
        $cuestionario = new Cuestionario();
        $cuestionario->fecha_sistema = now();
        $cuestionario->flag_baja = 0;
        $cuestionario->titulo = $data['titulo'];
        $cuestionario->descripcion = $data['descripcion'] ?? null;
        $cuestionario->save();

        return $cuestionario;
    }

    public function update($id, array $data)
    {
        $cuestionario = Cuestionario::findOrFail($id);
        $cuestionario->titulo = $data['titulo'];
        $cuestionario->descripcion = $data['descripcion'] ?? null;
        $cuestionario->save();

        return $cuestionario;
    }

    public function toggleStatus($id, $status)
    {
        $cuestionario = Cuestionario::findOrFail($id);
        $cuestionario->flag_baja = $status;
        $cuestionario->save();

        return $cuestionario;
    }

    public function getConfiguracionesActivas()
    {
        return ConfiguracionEstadoTramite::where('activo', 1)->get();
    }

    public function getEstadosAgrupados()
    {
        $configuraciones = $this->getConfiguracionesActivas();
        $agrupado = [];

        foreach ($configuraciones as $conf) {
            $tipo = $conf->id_tipo_tramite_multinota;
            $estado = EstadoTramite::find($conf->id_estado_tramite);

            if (!$estado) continue;

            $agrupado[$tipo][] = [
                'id_estado_tramite' => $estado->id_estado_tramite,
                'nombre_estado' => $estado->nombre,
            ];
        }

        return $agrupado;
    }

    public function getTiposTramite(array $tiposIds)
    {
        return TipoTramiteMultinota::whereIn('id_tipo_tramite_multinota', $tiposIds)
            ->pluck('nombre', 'id_tipo_tramite_multinota');
    }

    public function getEstadosSeleccionados($cuestionarioId)
    {
        return DB::table('cuestionario_estado_tramite')
            ->where('id_cuestionario', $cuestionarioId)
            ->pluck('id_estado_tramite')
            ->toArray();
    }

    public function syncEstadosTramite($cuestionarioId, array $estadosData)
    {
        CuestionarioEstadoTramite::where('id_cuestionario', $cuestionarioId)->delete();

        foreach ($estadosData as $tipoId => $estadosSeleccionados) {
            foreach ($estadosSeleccionados as $id_estado_tramite) {
                CuestionarioEstadoTramite::create([
                    'id_cuestionario' => $cuestionarioId,
                    'id_estado_tramite' => $id_estado_tramite,
                    'fecha_sistema' => now()
                ]);
            }
        }
    }
}