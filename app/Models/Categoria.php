<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'categoria';

    // Define the primary key
    protected $primaryKey = 'id_categoria';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_alta',
        'id_padre',
        'flag_activo',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
