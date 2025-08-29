<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContribuyenteMultinota extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'contribuyente_multinota';
    protected $primaryKey = 'id_contribuyente_multinota';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'cuit',
        'correo',
        'nombre',
        'apellido',
        'telefono1',
        'telefono2',
        'clave',
        'id_direccion',
        'fecha_alta',
    ];


    public function direccion()
{
    return $this->belongsTo(Direccion::class, 'id_direccion');
}

}
