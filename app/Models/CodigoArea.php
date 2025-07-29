<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodigoArea extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural of the model name
    protected $table = 'codigo_area';

    // Define the primary key
    protected $primaryKey = 'id_codigo_area';

    // Disable auto-incrementing as we are using mediumIncrements
    public $incrementing = false;

    // Define the key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'localidad', 'provincia', 'codigo',
    ];

    // Disable timestamps if you are managing them manually
    public $timestamps = false;
}
