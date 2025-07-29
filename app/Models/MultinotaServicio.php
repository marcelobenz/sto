<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultinotaServicio extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'multinota_servicios';

    // Define the primary key
    protected $primaryKey = 'id_multinota_servicio';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'nombre', 'url',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
