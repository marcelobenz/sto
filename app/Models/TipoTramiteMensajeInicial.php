<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoTramiteMensajeInicial extends Model {
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'tipo_tramite_mensaje_inicial';

    // Define the primary key
    protected $primaryKey = 'id_tipo_tramite_mensaje_inicial';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'id_tipo_tramite_multinota', 'id_mensaje_inicial', 'fecha_alta'
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
