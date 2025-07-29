<?php

namespace App\Enums;

enum TipoCaracterEnum: int
{
    case APODERADO = 33;
    case AUTORIZADO = 5;
    case DIRECTOR = 16;
    case GERENTE = 17;
    case PRESIDENTE = 14;
    case RESPONSABLE_DE_PAGO = 2;
    case SOCIO_GERENTE = 19;
    case TITULAR = 1;
    case VICEPRESIDENTE = 15;

    public function descripcion(): string
    {
        return match ($this) {
            self::APODERADO => 'APODERADO',
            self::AUTORIZADO => 'AUTORIZADO',
            self::DIRECTOR => 'DIRECTOR',
            self::GERENTE => 'GERENTE',
            self::PRESIDENTE => 'PRESIDENTE',
            self::RESPONSABLE_DE_PAGO => 'RESPONSABLE DE PAGO',
            self::SOCIO_GERENTE => 'SOCIO GERENTE',
            self::TITULAR => 'TITULAR',
            self::VICEPRESIDENTE => 'VICEPRESIDENTE',
        };
    }

    public static function fromDescripcion(string $nombre): ?self
    {
        foreach (self::cases() as $case) {
            if (strcasecmp($case->descripcion(), $nombre) === 0) {
                return $case;
            }
        }

        return null;
    }
}
