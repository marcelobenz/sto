<?php

namespace App\DTOs;

class PersonaJuridicaDTO {
    private string $cuit;
    private string $razonSocial;
    private string $tipoSociedad;
    private string $correo;
    private string $direccion;
    
    public function __construct(
        private bool $debeCargarRepresentante = true,
        private bool $debePersistirseConTramite = false,
        private bool $edicionBloqueada = false,
        private bool $puedeIniciarTramite = false
    ) {}
    
    public function getTitular(): string {
        return $this->razonSocial;
    }

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

    public function getCorreo(): string {
        return $this->correo;
    }

    public function setCorreo(string $correo): void {
        $this->correo = $correo;
    }

    public function getDireccion(): string {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): void {
        $this->direccion = $direccion;
    }

    public function getDebeCargarRepresentante(): bool {
        return $this->debeCargarRepresentante;
    }

    public function getDebePersistirseConTramite(): bool {
        return $this->debePersistirseConTramite;
    }
    
    public function getEdicionBloqueada(): bool {
        return $this->edicionBloqueada;
    }
    
    public function getPuedeIniciarTramite(): bool {
        return $this->puedeIniciarTramite;
    }
}
