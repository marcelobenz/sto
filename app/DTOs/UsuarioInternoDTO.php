<?php

namespace App\DTO;

use App\Interfaces\AsignableATramite;
use App\Models\OficinaInterna;
use App\Models\GrupoInterno;
use App\DTO\UsuarioCabecera;

class UsuarioInternoDTO implements AsignableATramite {
  public function __construct(
    public readonly ?int $id,
    public readonly ?string $nombre,
    public readonly ?string $apellido,
    public readonly ?string $correo,
    public readonly ?string $correo_municipal,
    public readonly ?string $cuit,
    public readonly ?string $dni,
    public readonly ?string $estado_nombre,
    public readonly ?string $legajo,
    public readonly ?string $oficina,
    public readonly array $permisos = [],
    public readonly ?string $categoria = null,
    public readonly bool $flag_menu = false,
    public readonly ?int $limite = null,
    public readonly ?OficinaInterna $oficina_interna = null
  ) {}

  public function getDescripcion(): string {
    return "{$this->legajo} - {$this->nombre} {$this->apellido}";
  }

  public function puedeAgregarAResumen(): bool {
    return true;
  }

  public function getId(): int {
    return $this->id;
  }

  public function getUsuarios(): array {
    return [$this];
  }

  public function getUsuarioQuePuedaSeguir(array $asignados): ?UsuarioInternoDTO {
    foreach ($asignados as $usuario) {
      if ($usuario instanceof self && $usuario->id === $this->id) {
        return $usuario;
      }
    }
    return null;
  }

  public function obtenerUsuarios(OficinaInterna $oficina, GrupoInterno $grupo): array {
    $correo = $this->correo_municipal ?? $this->correo;

    return [
      new UsuarioCabecera(
        id: $this->id,
        nombre: $this->nombre,
        apellido: $this->apellido,
        correo: $correo,
        legajo: $this->legajo,
        oficina: $oficina,
        grupo: $grupo
      )
    ];
  }

  public static function desdeModelo(\App\Models\UsuarioInterno $modelo): self {
    return new self(
      id: $modelo->id,
      nombre: $modelo->nombre,
      apellido: $modelo->apellido,
      correo: $modelo->correo,
      correo_municipal: $modelo->correo_municipal,
      cuit: $modelo->cuit,
      dni: $modelo->dni,
      estado_nombre: $modelo->estado_nombre,
      legajo: $modelo->legajo,
      oficina: $modelo->oficina,
      permisos: $modelo->permisos->all() ?? [],
      categoria: optional($modelo->categoria)->nombre,
      flag_menu: $modelo->flag_menu,
      limite: $modelo->limite,
      oficina_interna: $modelo->oficinaInterna
    );
  }
}