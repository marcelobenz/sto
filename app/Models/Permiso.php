<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permiso';
    
    protected $primaryKey = 'id_permiso';

    public $timestamps = true;

    protected $fillable = [
        'permiso',
        'permiso_clave',
        'fecha_sistema',
    ];
}
