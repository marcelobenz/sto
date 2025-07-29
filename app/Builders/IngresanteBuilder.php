<?php

namespace App\Builders;

use App\Models\Ingresante;
use Illuminate\Database\Eloquent\Model;

class IngresanteBuilder extends Model
{
    private string $cuit;

    public function build()
    {
        if ($this->cuit === null || $this->cuit === '') {
            throw new Exception('El usuario no posee cuit');
        }

        return new Ingresante($this->cuit);
    }

    public function setCuit(string $cuit)
    {
        $this->cuit = $cuit;
    }

    public function getCuit()
    {
        return $this->cuit;
    }
}
