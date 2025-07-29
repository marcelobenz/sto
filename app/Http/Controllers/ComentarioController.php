<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\HistorialTramite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComentarioController extends Controller
{
    /**
     * Guarda un comentario y lo registra en EVENTO e HISTORIAL_TRAMITE
     */
    public function guardarComentario(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'mensaje' => 'required|string|max:1000',
            'id_tramite' => 'required|integer',
        ]);

        // Buscar el id_tipo_evento correspondiente a la clave COMENTARIO
        $id_tipo_evento = DB::table('tipo_evento')
            ->where('clave', 'COMENTARIO')
            ->where('id_tipo_evento', 5)
            ->value('id_tipo_evento');

        if (! $id_tipo_evento) {
            return back()->with('error', 'Error: Tipo de evento no encontrado.');
        }

        // Crear un nuevo evento en la tabla EVENTO
        $evento = Evento::create([
            'fecha_alta' => Carbon::now(),
            'fecha_modificacion' => Carbon::now(),
            'id_tipo_evento' => $id_tipo_evento,
            'clave' => 'COMENTARIO',
            'desc_contrib' => $request->mensaje,
        ]);

        // Crear un registro en la tabla HISTORIAL_TRAMITE
        HistorialTramite::create([
            'mensaje' => $request->mensaje,
            'id_tramite' => $request->id_tramite,
            'id_evento' => $evento->id_evento, // Usar el id_evento del registro recién creado
            /* 'id_usuario_administador' => auth()->user()->id, */
            'id_usuario_administador' => auth()->check() ? auth()->user()->id : '152',
            'id_usuario_interno_asignado' => 107, // Puedes modificarlo según corresponda
        ]);

        return back()->with('success', 'Comentario agregado correctamente.');
    }
}
