<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultinotaTipoCuenta extends Model {
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'multinota_tipo_cuenta';

    // Define the primary key
    protected $primaryKey = 'id_multinota_tipo_cuenta';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_sistema', 'id_tipo_tramite_multinota', 'cod_tipo_cuenta'
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}