<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TramiteArchivo extends Model
{
    use HasFactory;

    protected $table = 'tramite_archivo'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'id_relacion'; // Llave primaria (ajústala si es diferente en tu DB)

    public $timestamps = false; // Si la tabla no tiene `created_at` y `updated_at`

    protected $fillable = [
        'id_tramite',
        'id_archivo',
        'fecha_alta',
    ];

    /**
     * Relación con la tabla Tramite
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }

    /**
     * Relación con Archivo
     */
    public function archivo()
    {
        return $this->belongsTo(Archivo::class, 'id_archivo');
    }
}
