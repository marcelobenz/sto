<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ingresante;

class IngresanteBuilder extends Model
{
    private String $cuit;

    public function build() {
        if($this->cuit === null || $this->cuit === "") {
            throw new Exception("El usuario no posee cuit");
        }

        return new Ingresante($this->cuit);
    }

    public function setCuit(String $cuit) {
		$this->cuit = $cuit;
	}

    public function getCuit() {
		return $this->cuit;
	}
}
