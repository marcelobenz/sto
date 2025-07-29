<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'notificacion';

    // Define the primary key
    protected $primaryKey = 'id_notificacion';

    public $incrementing = true;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'fecha',
        'id_historial_tramite',
        'visto',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
