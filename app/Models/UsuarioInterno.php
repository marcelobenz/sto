<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuarioInterno extends Model
{
    use HasFactory;

    protected $table = 'usuario_interno';
    protected $primaryKey = 'id_usuario_interno'; // Si la clave primaria no es 'id'

    public $timestamps = false;

    protected $fillable = [
        'legajo',
        'cuit',
        'dni',
        'correo_municipal',
        'id_categoria_usuario',
        'id_grupo_interno',
        'estado',
        'nombre',
        'apellido',
        'limite',
        'id_rol',
    ];

    public function grupoInterno()
    {
        return $this->belongsTo(GrupoInterno::class, 'id_grupo_interno');
    }


    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }


    public function hasPermission($permission)
    {
        
        if (!$this->relationLoaded('rol')) {
            $this->load('rol.permisos');
        }

        // Verifica si el rol tiene permisos y si contiene el permiso requerido
        return $this->rol && $this->rol->permisos->contains('permiso_clave', $permission);
    }
}