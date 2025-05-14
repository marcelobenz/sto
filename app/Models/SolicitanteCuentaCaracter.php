<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitanteCuentaCaracter extends Model {
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'solicitante_cuenta_caracter';

    // Define the primary key
    protected $primaryKey = 'id_solicitante_cuenta_caracter';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'cuenta', 'fecha_alta', 'r_caracter', 'id_solicitante', 'id_tipo_tramite', 'dominio', 'id_tipo_tramite_multinota'
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
