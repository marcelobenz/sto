<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ingresante;

class IngresanteBuilder extends Model
{
    private String $cuil;

    public function build() {
        if($this->cuil === null || $this->cuil === "") {
            //throw new RuntimeAlert("El usuario no posee cuil");
        }

        return new Ingresante($this->cuil);
    }

    public function setCuil(String $cuil) {
		$this->cuil = $cuil;
	}

    public function getCuil() {
		return $this->cuil;
	}
}
