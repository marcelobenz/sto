<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionEstadoTramite extends Model {
    use HasFactory;

    
    protected $table = 'configuracion_estado_tramite';

    
    protected $primaryKey = 'id_configuracion_estado_tramite';

    
    public $incrementing = false;


    protected $keyType = 'int';

    
    protected $fillable = [
        'fecha_sistema', 'id_estado_tramite', 'version', 'publico', 'id_tipo_tramite_multinota', 'activo'
    ];

    protected $casts = [
        'fecha_ultima_actualizacion' => 'datetime',
    ];

    
    public $timestamps = false;


      public function estadoTramite()
    {
        return $this->belongsTo(EstadoTramite::class, 'id_estado_tramite');
    }


      public function tipoTramiteMultinota()
    {
        return $this->belongsTo(TipoTramiteMultinota::class, 'id_tipo_tramite_multinota');
    }

}
