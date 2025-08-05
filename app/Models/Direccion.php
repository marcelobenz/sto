<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direccion';
    protected $primaryKey = 'id_direccion';

    protected $fillable = [
        'calle',
        'numero',
        'codigo_postal',
        'provincia',
        'localidad',
        'pais',
        'latitud',
        'longitud',
        'fecha_alta',
        'piso',
        'departamento'
    ];

     public $timestamps = false;


  public function contribuyentes()
{
    return $this->hasMany(ContribuyenteMultinota::class, 'id_direccion');
}

}
