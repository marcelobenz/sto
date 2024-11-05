<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametroMail extends Model
{
    use HasFactory;

    protected $table = 'parametro_mail';

    protected $primaryKey = 'id_parametro_mail';

    public $timestamps = false;

    protected $fillable = [
        'host',
        'fecha_sistema',
        'puerto',
        'usuario',
        'clave',
    ];
}
