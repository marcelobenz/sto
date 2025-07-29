<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionEstadoTramite;
use Illuminate\Support\Facades\Session;

class EstadoTramiteController extends Controller
{
    public function tienePermiso($multinota)
    {
        $usuarioInterno = Session::get('usuario_interno');

        $results = ConfiguracionEstadoTramite::query()
            ->select('configuracion_estado_tramite.*')
            ->join('estado_tramite_asignable as eta', 'eta.id_estado_tramite', '=', 'configuracion_estado_tramite.id_estado_tramite')
            ->join('estado_tramite as et', 'et.id_estado_tramite', '=', 'eta.id_estado_tramite')
            ->leftJoin('grupo_interno as gi', 'gi.id_grupo_interno', '=', 'eta.id_grupo_interno')
            ->leftJoin('usuario_interno as ui', function ($join) {
                $join->on('ui.id_grupo_interno', '=', 'gi.id_grupo_interno')
                    ->orOn('ui.id_usuario_interno', '=', 'eta.id_usuario_interno');
            })
            ->where('configuracion_estado_tramite.id_tipo_tramite_multinota', (int) $multinota)
            ->where('et.tipo', 'EN_CREACION')
            ->where('configuracion_estado_tramite.activo', 1)
            ->where('configuracion_estado_tramite.publico', 1)
            ->where('ui.id_usuario_interno', $usuarioInterno->id_usuario_interno)
            ->get();

        if (count($results) == 0) {
            // Return a vista "No autorizado"
            return view('no-autorizado.index');
        } else {
            // Return a vista "Buscar usuario"
            return view('buscar-usuario.index', compact('multinota'));
        }
    }
}
