<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    // Nombre de la tabla
    protected $table = 'evento';

    // Llave primaria
    protected $primaryKey = 'id_evento';

    // Indicamos que no se usarán timestamps automáticos (`created_at` y `updated_at`)
    public $timestamps = false;

    public $incrementing = true;

    // Define the key type
    protected $keyType = 'int';

    // Asignación masiva
    protected $fillable = [
        'descripcion',
        'desc_contrib',
        'fecha_alta',
        'fecha_modificacion',
        'id_tipo_evento',
        'clave',
    ];
}
