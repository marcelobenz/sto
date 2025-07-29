<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuestionario extends Model
{
    use HasFactory;

    protected $table = 'cuestionario';

    protected $primaryKey = 'id_cuestionario';

    public $timestamps = false;

    protected $fillable = [
        'fecha_sistema',
        'flag_baja',
        'titulo',
        'descripcion',
    ];

    public function preguntas()
    {
        return $this->hasMany(Pregunta::class, 'id_cuestionario');
    }
}
