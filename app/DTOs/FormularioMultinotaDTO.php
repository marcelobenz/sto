<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Collection;

use App\Models\SeccionMultinota;

class FormularioMultinotaDTO {
    public string $nombre;
    public string $categoria;
    public array $cuentas;
    public string $fechaActual;
    public bool $llevaMensaje;
    public array $pasosFormulario;
    public bool $puedeCompletar;
    public array $tiposCuenta;
    public Collection $secciones;

    public function __construct($multinota, $secciones, $cuentas, $pasos, $llevaMensaje) {
        $this->nombre = strtoupper($multinota->nombre);
        $this->categoria = $multinota->categoria->nombre;
        $this->secciones = $secciones;
        $this->cuentas = $cuentas;
        $this->fechaActual = Carbon::now()->format('d/m/Y');
        $this->llevaMensaje = $llevaMensaje;
        $this->pasosFormulario = $pasos;
        $this->puedeCompletar = !empty($cuentas);
        $this->tiposCuenta = $multinota->tiposDeCuenta ?? [];
    }

    public function getOrdenActual(): int {
        foreach ($this->pasosFormulario as $paso) {
            if (!$paso['completado']) {
                return $paso['orden'];
            }
        }

        return count($this->pasosFormulario); // fallback to last
    }

    public function dimensionBarraProgreso(): float {
        $total = count($this->pasosFormulario ?? 0);
        $actual = $this->getOrdenActual();

        if ($total === 0) {
            return 0;
        }

        return (($actual * 100.0) - 50.0) / $total;
    }

    public function estilosPaso($orden): string {
        if ($this->getOrdenActual() === $orden)
            return "active";
        else if ($this->getOrdenActual() > $orden)
            return "activated";
        else
            return "";
    }

    public function dimensionPaso(): float {
        $total = count($this->pasosFormulario ?? 0);

        if ($total === 0) {
            return 0;
        }

        return 100.0 / $total;
    }
}
