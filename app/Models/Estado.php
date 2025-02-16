<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'estado_tramite'; // Asegúrate de que este es el nombre correcto de la tabla
    protected $primaryKey = 'id_estado_tramite';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'tipo',
        'puede_rechazar',
        'puede_pedir_documentacion',
        'puede_elegir_camino',
        'estado_tiene_expediente',
    ];
}
