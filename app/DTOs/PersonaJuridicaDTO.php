<?php

namespace App\DTOs;

use App\DTOs\DomicilioDTO;

class PersonaJuridicaDTO {
    private string $cuit;
    private string $razonSocial;
    private string $tipoSociedad;
    private DomicilioDTO $domicilio;

    public function getCuit(): string {
        return $this->cuit;
    }

    public function setCuit(string $cuit): void {
        $this->cuit = $cuit;
    }

    public function getRazonSocial(): string {
        return $this->razonSocial;
    }

    public function setRazonSocial(string $razonSocial): void {
        $this->razonSocial = $razonSocial;
    }

    public function getTipoSociedad(): string {
        return $this->tipoSociedad;
    }

    public function setTipoSociedad(string $tipoSociedad): void {
        $this->tipoSociedad = $tipoSociedad;
    }

    public function getDomicilio(): DomicilioDTO {
        return $this->domicilio;
    }

    public function setDomicilio(DomicilioDTO $domicilio): void {
        $this->domicilio = $domicilio;
    }
}
