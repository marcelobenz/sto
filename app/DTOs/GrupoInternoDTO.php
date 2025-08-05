<?php

namespace App\DTOs;

use App\Contracts\AsignableATramite;
use App\Models\OficinaInterna;
use App\DTOs\UsuarioInternoDTO;
use App\DTOs\UsuarioCabecera;

class GrupoInternoDTO implements AsignableATramite {
  public function __construct(
    public readonly int $id,
    public readonly string $nombre,
    public readonly ?string $codigo = null,
    /** @var UsuarioInternoDTO[] */
    public readonly array $usuarios = []
  ) {}

  public function getDescripcion(): string {
    return $this->nombre;
  }

  public function puedeAgregarAResumen(): bool {
    return false;
  }

  public function getId(): int {
    return $this->id;
  }

  /**
   * @return UsuarioInternoDTO[]
   */
  public function getUsuarios(): array {
    return $this->usuarios;
  }

  public function getUsuarioQuePuedaSeguir(array $asignados): ?UsuarioInternoDTO {
    foreach ($this->getUsuarios() as $usuarioDelGrupo) {
      $usuario = $usuarioDelGrupo->getUsuarioQuePuedaSeguir($asignados);
      if ($usuario !== null) {
        return $usuario;
      }
    }

    return null;
  }

  /**
   * @return UsuarioCabecera[]
   */
  public function obtenerUsuarios(OficinaInterna $oficina): array {
    $cabeceras = [];

    foreach ($this->usuarios as $usuario) {
      $cabeceras = array_merge(
        $cabeceras,
        $usuario->obtenerUsuarios($oficina, $this)
      );
    }

    return $cabeceras;
  }

  public static function desdeModelo(\App\Models\GrupoInterno $modelo): self {
    return new self(
      id: $modelo->id,
      nombre: $modelo->nombre,
      codigo: $modelo->codigo,
      usuarios: collect($modelo->usuarios)->map(
        fn ($usuario) => UsuarioInternoDTO::desdeModelo($usuario)
      )->all()
    );
  }
}