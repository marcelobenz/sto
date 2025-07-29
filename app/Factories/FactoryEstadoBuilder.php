<?php

namespace App\Factories;

use App\Builders\EstadoBuilder;
use App\Enums\TipoEstadoEnum;
use App\Validadores\ValidadorAsignables;
use App\Validadores\ValidadorBifurcaciones;
use App\Validadores\ValidadorEstadosAnteriores;
use App\Validadores\ValidadorEstadosEspecificos;
use App\Validadores\ValidadorEstadosPosteriores;
use App\Validadores\ValidadorFinalizar;
use App\Validadores\ValidadorInterface;
use App\Validadores\ValidadorTipoEstado;

class FactoryEstadoBuilder
{
    public static function getEnCreacion(): EstadoBuilder
    {
        return (new EstadoBuilder(TipoEstadoEnum::EN_CREACION->nombre()))
            ->setearTipoEstado(TipoEstadoEnum::EN_CREACION)
            ->setearValidadores(self::generarValidadores());
    }

    public static function getIniciado(): EstadoBuilder
    {
        return (new EstadoBuilder(TipoEstadoEnum::INICIADO->nombre()))
            ->setearTipoEstado(TipoEstadoEnum::INICIADO)
            ->setearValidadores(self::generarValidadores());
    }

    public static function getEnAnalisis(): EstadoBuilder
    {
        return (new EstadoBuilder('En Análisis'))
            ->setearTipoEstado(TipoEstadoEnum::PERSONALIZADO)
            ->setearValidadores(self::generarValidadores());
    }

    public static function getEnAprobacion(): EstadoBuilder
    {
        return (new EstadoBuilder('En Aprobación'))
            ->setearTipoEstado(TipoEstadoEnum::PERSONALIZADO)
            ->setearValidadores(self::generarValidadores());
    }

    public static function getAFinalizar(): EstadoBuilder
    {
        return (new EstadoBuilder(TipoEstadoEnum::A_FINALIZAR->nombre()))
            ->setearTipoEstado(TipoEstadoEnum::A_FINALIZAR)
            ->setearValidadores(self::generarValidadoresAFinalizar());
    }

    public static function getExpediente(): EstadoBuilder
    {
        return (new EstadoBuilder(TipoEstadoEnum::EXPEDIENTE->nombre()))
            ->setearTipoEstado(TipoEstadoEnum::EXPEDIENTE)
            ->setearValidadores(self::generarValidadoresExpediente());
    }

    public static function getFinalizacion(): EstadoBuilder
    {
        return (new EstadoBuilder(TipoEstadoEnum::DE_FINALIZACION->nombre()))
            ->setearTipoEstado(TipoEstadoEnum::DE_FINALIZACION)
            ->setearValidadores(self::generarValidadoresDeFinalizacion());
    }

    /** @return ValidadorInterface[] */
    public static function generarValidadores(): array
    {
        return [
            new ValidadorTipoEstado,
            new ValidadorEstadosAnteriores,
            new ValidadorEstadosPosteriores,
            new ValidadorAsignables,
            new ValidadorBifurcaciones,
            // new ValidadorVolverAAnteriores(), // if you implement it later
            new ValidadorFinalizar,
        ];
    }

    /** @return ValidadorInterface[] */
    public static function generarValidadoresExpediente(): array
    {
        return array_merge(self::generarValidadores(), [
            new ValidadorEstadosEspecificos,
        ]);
    }

    /** @return ValidadorInterface[] */
    public static function generarValidadoresDeFinalizacion(): array
    {
        return array_merge(self::generarValidadores(), [
            new ValidadorEstadosEspecificos,
        ]);
    }

    /** @return ValidadorInterface[] */
    public static function generarValidadoresAFinalizar(): array
    {
        return [
            new ValidadorTipoEstado,
            new ValidadorEstadosAnteriores,
            new ValidadorEstadosPosteriores,
            new ValidadorAsignables,
        ];
    }

    /** @return ValidadorInterface[] */
    public static function obtenerValidadoresPorTipo(TipoEstadoEnum $tipo): array
    {
        return match ($tipo) {
            $tipo === TipoEstadoEnum::DE_FINALIZACION => self::generarValidadoresDeFinalizacion(),
            $tipo === TipoEstadoEnum::EXPEDIENTE => self::generarValidadoresExpediente(),
            $tipo === TipoEstadoEnum::A_FINALIZAR => self::generarValidadoresAFinalizar(),
            default => self::generarValidadores(),
        };
    }
}
