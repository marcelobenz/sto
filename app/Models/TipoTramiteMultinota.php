<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoTramiteMultinota extends Model {
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'tipo_tramite_multinota';

    // Define the primary key
    protected $primaryKey = 'id_tipo_tramite_multinota';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'nombre', 'codigo', 'id_categoria', 'publico', 'nivel', 'muestra_mensaje', 'lleva_expediente', 
        'baja_logica', 'fecha_alta', 'fecha_ultima_actualizacion', 'codigo_crm', 'lleva_documentacion',
        'id_multinota_servicio'
    ];

    protected $casts = [
        'fecha_ultima_actualizacion' => 'datetime',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;


    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }
}
