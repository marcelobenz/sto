<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoInterno extends Model
{
    use HasFactory;

    protected $table = 'grupo_interno';

    protected $primaryKey = 'id_grupo_interno';

    protected $fillable = ['descripcion'];

    public function usuarios()
    {
        return $this->hasMany(UsuarioInterno::class, 'id_grupo_interno', 'id_grupo_interno');
    }

    public function oficina()
    {
        return $this->belongsTo(Oficina::class, 'id_oficina');
    }
}
