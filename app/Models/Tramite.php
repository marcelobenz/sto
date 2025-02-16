<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'tramite';

    // Define the primary key
    protected $primaryKey = 'id_tramite';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha_alta',
        'fecha_modificacion',
        'id_usuario',
        'id_prioridad',
        'id_estado',
        'id_tipo_tramite',
        'id_solicitante',
        'id_requisito',
        'convenio',
        'correo',
        'cuit_contribuyente',
        'flag_ingreso',
        'id_usuario_interno',
        'flag_rechazado',
        'flag_cancelado',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;

    /**
     * Relación con Archivo
     * Un trámite puede tener múltiples archivos.
     */
    public function archivos()
    {
        return $this->belongsToMany(Archivo::class, 'tramite_archivo', 'id_tramite', 'id_archivo')
                    ->withPivot('fecha_alta')
                    ->as('relacion');
    }

    public function estados()
    {
        //return $this->hasMany(TramiteEstadoTramite::class, 'id_tramite', 'id_tramite');
        return $this->belongsTo(Estado::class, 'id_estado_tramite', 'id_estado_tramite');

    }
    
    public function estadoActual()
    {
        return $this->hasOne(TramiteEstadoTramite::class, 'id_tramite', 'id_tramite')
                    ->latestOfMany('fecha_sistema') // 📌 Ordena por la fecha más reciente
                    ->with('estado');
    }
    
    
    
        

}
