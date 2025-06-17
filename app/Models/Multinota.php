<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multinota extends Model {
  use HasFactory;

  // Define the table name if it's not the plural of the model name
  protected $table = 'multinota';

  // Define the primary key
  protected $primaryKey = 'id_tramite';

  public $incrementing = true;

  // Define the key type
  protected $keyType = 'int';

  // Define fillable attributes for mass assignment
  protected $fillable = [
    'cuenta',
    'id_tipo_tramite_multinota',
    'id_mensaje_inicial',
    'id_contribuyente_multinota',
    'informacion_adicional',
    'fecha_alta',
    'id_prioridad',
    'id_solicitante',
    'id_usuario_interno',
    'r_caracter',
    'correo',
    'cuit_contribuyente',
    'flag_rechazado',
    'flag_cancelado'
  ];

  // Disable timestamps if you are managing them manually
  public $timestamps = false;
}
