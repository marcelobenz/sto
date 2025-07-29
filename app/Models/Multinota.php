<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multinota extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'multinota';

    // Define the primary key
    protected $primaryKey = 'id_tramite';

    public $incrementing = true;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'cuenta',
        'id_tipo_tramite_multinota',
        'id_mensaje_inicial',
        'id_contribuyente_multinota',
        'informacion_adicional',
        'fecha_alta',
        'id_prioridad',
        'id_solicitante',
        'id_usuario_interno',
        'r_caracter',
        'correo',
        'cuit_contribuyente',
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

    // Tipo Trámite Multinota
    public function tipoTramiteMultinota()
    {
        return $this->belongsTo(TipoTramiteMultinota::class, 'id_tipo_tramite_multinota');
    }

    // Mensaje Inicial
    public function mensajeInicial()
    {
        return $this->belongsTo(MensajeInicial::class, 'id_mensaje_inicial');
    }

    // Contribuyente Multinota
    public function contribuyente()
    {
        return $this->belongsTo(ContribuyenteMultinota::class, 'id_contribuyente_multinota');
    }

    // Prioridad
    public function prioridad()
    {
        return $this->belongsTo(Prioridad::class, 'id_prioridad');
    }

    // Solicitante
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class, 'id_solicitante');
    }

    // Usuario Interno
    public function usuarioInterno()
    {
        return $this->belongsTo(UsuarioInterno::class, 'id_usuario_interno');
    }
}
