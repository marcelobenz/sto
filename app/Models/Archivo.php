<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use HasFactory;

    protected $table = 'archivo'; // Nombre de la tabla en la base de datos

    protected $primaryKey = 'id_archivo'; // Llave primaria (ajústala si es diferente en tu DB)

    public $timestamps = false; // Si la tabla no tiene `created_at` y `updated_at`

    protected $fillable = [
        'nombre',
        'tipo_contenido',
        'path_archivo',
        'fecha_alta',
        'descripcion'
    ];

    /**
     * Relación con TramiteArchivo
     * Un archivo puede estar asociado a múltiples trámites.
     */
    public function tramites()
    {
        return $this->belongsToMany(Tramite::class, 'tramite_archivo', 'id_archivo', 'id_tramite')
                    ->withPivot('fecha_alta')
                    ->as('relacion');
    }
    
}
