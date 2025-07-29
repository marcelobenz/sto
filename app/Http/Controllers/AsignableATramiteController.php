<?php

namespace App\Http\Controllers;

use App\DTOs\EstadoTramiteDTO;
use App\DTOs\UsuarioInternoDTO;
use App\Services\LicenciaService;
use App\Services\TramiteService;
use Illuminate\Support\Collection;

class AsignableATramiteController
{
    public function __construct(
        protected LicenciaService $licenciaService,
        protected TramiteService $tramiteService,
    ) {}

    /**
     * Devuelve el usuario que menos trámites abiertos tiene,
     * entre todos los "asignables" de un estado.
     */
    public function recomendado(EstadoTramiteDTO $estado): ?UsuarioInternoDTO
    {
        /** @var Collection<int, UsuarioInternoDTO> $usuarios */
        $usuarios = collect();

        // 1) juntar todos los usuarios de cada asignable (usuarios sueltos o grupos)
        foreach ($estado->getAsignables() as $asignable) {
            $usuarios = $usuarios->merge($asignable->getUsuarios());
        }

        // 2) descartar usuarios de licencia
        $usuarios = $usuarios->filter(fn (UsuarioInternoDTO $u) => ! $this->licenciaService->estaDeLicencia($u));

        if ($usuarios->isEmpty()) {
            return null; // nadie disponible
        }

        // 3) armar mapa ID → cantidad trámites, teniendo en cuenta límite
        $usuariosConCarga = $usuarios->mapWithKeys(function (UsuarioInternoDTO $u) {
            $cantidad = $this->tramiteService->cantidadDeTramites($u->id);

            // aplicar penalidad si excede límite
            if (! is_null($u->limite) && $cantidad > $u->limite) {
                $cantidad = 99999;
            }

            return [$u->id => $cantidad];
        });

        // 4) obtener el ID con menos trámites
        $idMenosCarga = $usuariosConCarga->sort()->keys()->first();

        // 5) devolver el DTO completo del usuario elegido
        return $usuarios->firstWhere('id', $idMenosCarga);
    }
}
