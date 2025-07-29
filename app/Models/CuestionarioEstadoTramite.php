<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuestionarioEstadoTramite extends Model
{
    use HasFactory;

    protected $table = 'cuestionario_estado_tramite';

    protected $primaryKey = 'id_cuestionario_estado';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'fecha_sistema',
        'id_cuestionario',
        'id_estado_tramite',
    ];

    public function cuestionario()
    {
        return $this->belongsTo(Cuestionario::class, 'id_cuestionario');
    }

    public function estadoTramite()
    {
        return $this->belongsTo(EstadoTramite::class, 'id_estado_tramite');
    }
}
