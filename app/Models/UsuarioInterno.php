<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsuarioInterno extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'usuario_interno';

    // Define the primary key
    protected $primaryKey = 'id_usuario_interno';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_sistema',
        'fecha_update',
        'legajo',
        'cuit',
        'dni',
        'correo_municipal',
        'correo',
        'estado',
        'nombre',
        'apellido',
        'id_categoria_usuario',
        'id_grupo_interno',
        'flag_menu',
        'limite',
        'id_rol',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;

    public static function getUsuarioPorCuil($cuil) {
        $res = DB::table("usuario_interno")
        ->where("cuit", "=", $cuil)
        ->get();

        $usuario = $res->toArray()[0];

        return $usuario;
    }
    
    public function grupoInterno() {
        return $this->belongsTo(GrupoInterno::class, 'id_grupo_interno');
    }


    public function rol() {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }


    public function hasPermission($permission) {
        
        if (!$this->relationLoaded('rol')) {
            $this->load('rol.permisos');
        }

        // Verifica si el rol tiene permisos y si contiene el permiso requerido
        return $this->rol && $this->rol->permisos->contains('permiso_clave', $permission);
    }
}
