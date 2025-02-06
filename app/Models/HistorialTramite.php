<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialTramite extends Model
{
    // Nombre de la tabla
    protected $table = 'historial_tramite';

    // Llave primaria
    protected $primaryKey = 'id_historial_tramite';

    // Indicamos que no se usarán timestamps automáticos (`created_at` y `updated_at`)
    public $timestamps = false;

    // Asignación masiva
    protected $fillable = [
        'fecha',
        'mensaje',
        'id_tramite',
        'id_evento',
        'id_estado_actual',
        'id_estado_anterior',
        'id_estado_tramite',
        'id_usuario_administrador',
        'id_usuario_asignado',
        'id_usuario_interno_administrador',
        'id_usuario_interno_asignado',
    ];
}
