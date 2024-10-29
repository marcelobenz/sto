<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    use HasFactory;

    protected $table = 'pregunta';
    protected $primaryKey = 'id_pregunta';
    public $timestamps = false;

    protected $fillable = [
        'id_cuestionario',
        'fecha_sistema',
        'descripcion',
        'flag_detalle_si',
        'flag_detalle_no',
        'flag_finalizacion_si',
        'flag_rechazo_no',
        'flag_baja'
    ];

   
    public function cuestionario()
    {
        return $this->belongsTo(Cuestionario::class, 'id_cuestionario');
    }
}
