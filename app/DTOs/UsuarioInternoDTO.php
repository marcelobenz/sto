<?php

namespace App\DTOs;

use App\Enums\EstadoUsuarioInternoEnum;
use App\Interfaces\AsignableATramite;
use App\Models\CategoriaUsuario;
use App\Models\GrupoInterno;
use App\Models\Oficina;
use App\Models\UsuarioInterno;

class UsuarioInternoDTO implements AsignableATramite
{
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
        public readonly ?Oficina $oficina,
        public readonly ?array $permisos = [],
        public readonly ?CategoriaUsuario $categoria = null,
        public readonly ?bool $flag_menu = false,
        public readonly ?int $limite = null
    ) {}

    public function getDescripcion(): string
    {
        return "{$this->legajo} - {$this->nombre} {$this->apellido}";
    }

    public function puedeAgregarAResumen(): bool
    {
        return true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsuarios(): array
    {
        return [$this];
    }

    public function getUsuarioQuePuedaSeguir(array $asignados): ?UsuarioInternoDTO
    {
        foreach ($asignados as $usuario) {
            if ($usuario instanceof self && $usuario->id === $this->id) {
                return $usuario;
            }
        }

        return null;
    }

    public function obtenerUsuarios(Oficina $oficina, GrupoInterno $grupo): array
    {
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
            ),
        ];
    }

    public static function desdeModelo(UsuarioInterno $modelo): self
    {
        return new self(
            id: $modelo->id_usuario_interno,
            nombre: $modelo->nombre,
            apellido: $modelo->apellido,
            correo: $modelo->correo,
            correo_municipal: $modelo->correo_municipal,
            cuit: $modelo->cuit,
            dni: $modelo->dni,
            estado_nombre: EstadoUsuarioInternoEnum::fromId($modelo->estado)?->descripcion(),
            legajo: $modelo->legajo,
            oficina: $modelo->grupoInterno?->oficina,
            permisos: $modelo->rol?->permisos?->pluck('permiso_clave')->all() ?? [],
            categoria: $modelo->categoria,
            flag_menu: $modelo->flag_menu,
            limite: $modelo->limite
        );
    }
}
