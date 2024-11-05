<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContribuyenteMultinota extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'contribuyente_multinota';

    protected $primaryKey = 'id_contribuyente_multinota';


}
