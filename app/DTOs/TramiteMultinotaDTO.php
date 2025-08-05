<?php

namespace App\DTOs;

class TramiteMultinotaDTO {
    public function __construct(
        public string $paginaResumen = 'detalle-multinota',
        public string $paginaEdicion = 'editar-multinota',
        public string $claseTramite = 'TramiteMultinota',
        public bool $llevaEstadoTramite = true,
        public bool $llevaEstadoFinalizacion = false,
    ) {}
}