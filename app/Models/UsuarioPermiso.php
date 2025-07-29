<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UsuarioPermiso extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'usuario_permiso';

    // Define the primary key
    protected $primaryKey = 'id_usuario_permiso';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_sistema',
        'id_usuario_interno',
        'permiso',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;

    public static function getPermisosPorId($id)
    {
        $res = DB::table('usuario_permiso')
            ->where('id_usuario_interno', '=', $id)
            ->get();

        $permisos = $res->toArray();

        return $permisos;
    }
}
