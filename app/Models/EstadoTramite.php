<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoTramite extends Model {
    use HasFactory;

    
    protected $table = 'estado_tramite';

    
    protected $primaryKey = 'id_estado_tramite';

    
    public $incrementing = false;


    protected $keyType = 'int';

    
    protected $fillable = [
        'fecha_sistema', 'nombre', 'tipo', 'puede_rechazar', 'puede_pedir_documentacion', 'estado_tiene_expediente'
    ];


    
    public $timestamps = false;

}
