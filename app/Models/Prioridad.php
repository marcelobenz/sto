<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prioridad extends Model {
  use HasFactory;

  // Define the table name if it's not the plural of the model name
  protected $table = 'prioridad';

  // Define the primary key
  protected $primaryKey = 'id_prioridad';

  public $incrementing = true;

  // Define the key type
  protected $keyType = 'int';

  // Define fillable attributes for mass assignment
  protected $fillable = [
    'nombre',
    'peso',
    'fecha_alta'
  ];

  // Disable timestamps if you are managing them manually
  public $timestamps = false;
}
