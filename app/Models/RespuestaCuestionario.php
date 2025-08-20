<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespuestaCuestionario extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'respuesta_cuestionario';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_respuesta_cuestionario';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_pregunta_cuestionario',
        'flag_valor',
        'detalle',
        'id_tramite',
        'id_estado_tramite',
        'fecha_sistema'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fecha_sistema' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Obtener la pregunta asociada a esta respuesta.
     */
    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta_cuestionario', 'id_pregunta_cuestionario');
    }

    /**
     * Obtener el trámite asociado a esta respuesta.
     */
    public function mutlinota()
    {
        return $this->belongsTo(Multinota::class, 'id_tramite', 'id_tramite');
    }

    /**
     * Obtener el estado de trámite asociado a esta respuesta.
     */
    public function estadoTramite()
    {
        return $this->belongsTo(EstadoTramite::class, 'id_estado_tramite', 'id_estado_tramite');
    }

    /**
     * Scope para obtener respuestas por trámite.
     */
    public function scopePorTramite($query, $idTramite)
    {
        return $query->where('id_tramite', $idTramite);
    }

    /**
     * Scope para obtener respuestas por estado de trámite.
     */
    public function scopePorEstado($query, $idEstadoTramite)
    {
        return $query->where('id_estado_tramite', $idEstadoTramite);
    }

    /**
     * Scope para obtener respuestas por pregunta.
     */
    public function scopePorPregunta($query, $idPregunta)
    {
        return $query->where('id_pregunta_cuestionario', $idPregunta);
    }

}