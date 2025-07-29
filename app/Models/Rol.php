<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'rol';

    // Si tu tabla tiene una columna primary key diferente a 'id', define aquí
    protected $primaryKey = 'id_rol';

    // Desactiva las marcas de tiempo si no usas created_at y updated_at
    public $timestamps = false;

    // Define los atributos que se pueden asignar de forma masiva
    protected $fillable = [
        'nombre',
        'clave',
        'fecha_sistema',
    ];

    // Define los atributos que deben ser tratados como fechas (si necesitas convertir automáticamente la fecha_sistema)
    protected $dates = ['fecha_sistema'];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'rol_permiso', 'id_rol', 'id_permiso');
    }
}
