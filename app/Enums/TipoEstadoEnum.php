<?php

namespace App\Enums;

enum TipoEstadoEnum: string
{
    case PERSONALIZADO = 'Personalizado';
    case EN_CREACION = 'En Creación';
    case INICIADO = 'Iniciado';
    case A_FINALIZAR = 'A Finalizar';
    case DE_FINALIZACION = 'Finalización';
    case EXPEDIENTE = 'Expediente';

    public function aceptaPosteriores(): bool
    {
        return match ($this) {
            self::PERSONALIZADO => true,
            self::EN_CREACION => false,
            self::INICIADO => true,
            self::A_FINALIZAR => false,
            self::DE_FINALIZACION => true,
            self::EXPEDIENTE => true
        };
    }

    public function aceptaAnteriores(): bool
    {
        return match ($this) {
            self::PERSONALIZADO => true,
            self::EN_CREACION => false,
            self::INICIADO => false,
            self::A_FINALIZAR => true,
            self::DE_FINALIZACION => true,
            self::EXPEDIENTE => true
        };
    }

    public function puedeElegirCamino(): bool
    {
        return match ($this) {
            self::PERSONALIZADO => true,
            self::EN_CREACION => false,
            self::INICIADO => true,
            self::A_FINALIZAR => false,
            self::DE_FINALIZACION => true,
            self::EXPEDIENTE => true
        };
    }

    public function puedeAplicarRestricciones(): bool
    {
        return match ($this) {
            self::PERSONALIZADO => true,
            self::EN_CREACION => false,
            self::INICIADO => true,
            self::A_FINALIZAR => true,
            self::DE_FINALIZACION => true,
            self::EXPEDIENTE => true
        };
    }

    public function nombre(): string
    {
        return $this->value;
    }

    public static function fromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }
}
