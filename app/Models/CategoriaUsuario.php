<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaUsuario extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'categoria_usuario';

    // Define the primary key
    protected $primaryKey = 'id_categoria_usuario';

    public $incrementing = true;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_sistema',
        'fecha_update',
        'codigo',
        'descripcion',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
