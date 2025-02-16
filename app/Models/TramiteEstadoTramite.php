<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Estado;

class TramiteEstadoTramite extends Model
{
    use HasFactory;

    protected $table = 'tramite_estado_tramite'; // Nombre exacto de la tabla
    protected $primaryKey = 'id_tramite_estado_tramite'; // Clave primaria

    public $timestamps = false; // No usamos timestamps

    protected $fillable = [
        'id_tramite',
        'id_estado_tramite',
        'id_tramite_estado_tramite_anterior',
        'id_estado_tramite_siguiente',
        'id_usuario_interno',
        'fecha_sistema',
        'activo',
        'completo',
        'reiniciado',
        'espera_documentacion'
    ];


    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado_tramite', 'id_estado_tramite');
    }

    


}
