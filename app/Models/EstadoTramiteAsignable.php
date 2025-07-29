<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTramiteAsignable extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'estado_tramite_asignable';

    // Define the primary key
    protected $primaryKey = 'id_estado_tramite_asignable';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_sistema', 'id_estado_tramite', 'id_grupo_interno', 'id_usuario_interno',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
