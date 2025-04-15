<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTramite extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'estado_tramite';

    // Define the primary key
    protected $primaryKey = 'id_estado_tramite';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_sistema', 'nombre', 'tipo', 'puede_rechazar', 'puede_pedir_documentacion', 'puede_elegir_camino', 'estado_tiene_expediente'
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
