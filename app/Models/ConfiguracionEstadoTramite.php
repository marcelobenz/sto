<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionEstadoTramite extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'configuracion_estado_tramite';

    // Define the primary key
    protected $primaryKey = 'id_configuracion_estado_tramite';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_sistema', 'id_estado_tramite', 'id_proximo_estado', 'version', 'publico', 'id_tipo_tramite', 'id_tipo_tramite_multinota', 'activo', 'id_estado_tramite_anterior',
    ];

    protected $casts = [
        'fecha_ultima_actualizacion' => 'datetime',
    ];

    public function estadoTramite()
    {
        return $this->belongsTo(EstadoTramite::class, 'id_estado_tramite');
    }

    public function tipoTramiteMultinota()
    {
        return $this->belongsTo(TipoTramiteMultinota::class, 'id_tipo_tramite_multinota');
    }

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
