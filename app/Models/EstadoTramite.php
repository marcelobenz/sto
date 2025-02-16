<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTramite extends Model
{
    use HasFactory;

    protected $table = 'estado_tramite'; // Nombre exacto de la tabla en la BD
    protected $primaryKey = 'id_estado_tramite'; // Clave primaria de la tabla

    public $timestamps = false; // No usamos timestamps en la tabla

    protected $fillable = [
        'nombre',
        'tipo',
        'puede_rechazar',
        'puede_pedir_documentacion',
        'puede_elegir_camino',
        'estado_tiene_expediente',
    ];
}
