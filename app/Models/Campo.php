<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campo extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'campo';

    // Define the primary key
    protected $primaryKey = 'id_campo';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'nombre', 'texto', 'valor', 'tipo', 'dimension', 'orden', 'fecha_alta', 'fecha_baja', 'id_seccion', 'obligatorio', 'mascara', 'limite_minimo', 'limite_maximo',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
