<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model {
  use HasFactory;

  protected $table = 'tipo_evento';
  
  protected $primaryKey = 'id_tipo_evento';

  public $timestamps = true;

  protected $fillable = [
    'nombre',
    'fecha_alta',
    'clave',
    'mensaje',
    'mensaje_contribuyente',
    'muestra_contribuyente'
  ];
}
