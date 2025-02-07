<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TramiteArchivo extends Model
{
    use HasFactory;

    protected $table = 'tramite_archivo'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'id'; // Llave primaria (ajústala si es diferente en tu DB)

    public $timestamps = false; // Si la tabla no tiene `created_at` y `updated_at`

    protected $fillable = [
        'id_tramite',
        'nombre',
        'descripcion',
        'ruta', // Ruta del archivo en almacenamiento
        'fecha_alta'
    ];

    /**
     * Relación con la tabla Tramite
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }
}
