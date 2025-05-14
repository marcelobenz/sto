<?php

namespace App\DTOs;

use App\DTOs\TipoCaracterDTO;
use App\DTOs\DocumentoDTO;
use App\DTOs\CodigoAreaDTO;
use App\DTOs\DomicilioDTO;

class RepresentanteDTO {
    private TipoCaracterDTO $tipoCaracter;
    private string $nombre;
    private string $apellido;
    private DocumentoDTO $documento;
    private CodigoAreaDTO $codigoArea;
    private string $telefono;
    private string $correo;
    private string $correoRepetido;
    private DomicilioDTO $domicilio;

    public function __construct(
        TipoCaracterDTO $tipoCaracter,
        string $nombre,
        string $apellido,
        DocumentoDTO $documento,
        string $telefono,
        string $correo,
        string $correoRepetido,
        DomicilioDTO $domicilio
    ) {
        $this->tipoCaracter = $tipoCaracter;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->documento = $documento;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->correoRepetido = $correoRepetido;
        $this->domicilio = $domicilio;
    }

    public function getTipoCaracter(): TipoCaracterDTO {
        return $this->tipoCaracter;
    }

    public function setTipoCaracter(TipoCaracterDTO $tipoCaracter): void {
        $this->tipoCaracter = $tipoCaracter;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getApellido(): string {
        return $this->apellido;
    }

    public function setApellido(string $apellido): void {
        $this->apellido = $apellido;
    }

    public function getDocumento(): DocumentoDTO {
        return $this->documento;
    }

    public function setDocumento(DocumentoDTO $documento): void {
        $this->documento = $documento;
    }

    public function getCodigoArea(): CodigoAreaDTO {
        return $this->codigoArea;
    }

    public function setCodigoArea(CodigoAreaDTO $codigoArea): void {
        $this->codigoArea = $codigoArea;
    }

    public function getTelefono(): string {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void {
        $this->telefono = $telefono;
    }

    public function getCorreo(): string {
        return $this->correo;
    }

    public function setCorreo(string $correo): void {
        $this->correo = $correo;
    }

    public function getCorreoRepetido(): string {
        return $this->correoRepetido;
    }

    public function setCorreoRepetido(string $correoRepetido): void {
        $this->correoRepetido = $correoRepetido;
    }

    public function getDireccion(): DomicilioDTO {
        return $this->direccion;
    }

    public function setDireccion(DomicilioDTO $direccion): void {
        $this->direccion = $direccion;
    }

    public function getTelefonoSinMascara(): string {
        $telefono = $this->telefono ?? '';

        if (empty($telefono)) {
            return '';
        }

        $telefono = preg_replace('/\(\d+\)/', '', $telefono);
        $telefono = str_replace(['(', ')', '-'], '', $telefono);

        return trim($telefono);
    }
	
	public function getAreaTelefono(): string {
        $telefono = $this->telefono ?? '';

        if (empty($telefono)) {
            return '';
        }

        if (preg_match('/\((\d+)\)/', $telefono, $matches)) {
            return $matches[1];
        }

        return '';
    }
}
