<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitante extends Model {
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'solicitante';

    // Define the primary key
    protected $primaryKey = 'id_solicitante';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'nombre', 'documento', 'telefono', 'correo', 'fecha_alta', 'fecha_modificacion',
        'id_tipo_documento', 'apellido', 'id_direccion'
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
