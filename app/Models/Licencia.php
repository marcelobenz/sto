<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    use HasFactory;

    protected $table = 'licencia';
    protected $primaryKey = 'id_licencia';

    protected $fillable = [
        'motivo',
        'fecha_inicio',
        'fecha_fin',
        'id_usuario_interno',
    ];

    public $timestamps = false; // Si no tienes campos de timestamps en la tabla
}
