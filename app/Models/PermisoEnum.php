<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

enum PermisoEnum: string
{
	case VER_REPORTES = "VER_REPORTES";
    case TOMAR_TRAMITE = "TOMAR_TRAMITE";
    case REASIGNAR_TRAMITE = "REASIGNAR_TRAMITE";
    case DAR_DE_BAJA_TRAMITE = "DAR_DE_BAJA_TRAMITE";
    case VER_TRAMITES_CANCELADOS = "VER_TRAMITES_CANCELADOS";
    case VER_TODOS_LOS_TRAMITES = "VER_TODOS_LOS_TRAMITES";
    case CONFIGURAR_ESTADOS = "CONFIGURAR_ESTADOS";
    case CONFIGURAR_NIVELES = "CONFIGURAR_NIVELES";
    case CONFIGURAR_LIMITES = "CONFIGURAR_LIMITES";
    case CONFIGURAR_USUARIOS = "CONFIGURAR_USUARIOS";
    case CONFIGURAR_MULTINOTA = "CONFIGURAR_MULTINOTA";
    case CONFIGURAR_SECCIONES = "CONFIGURAR_SECCIONES";
    case CONFIGURAR_REQUISITOS = "CONFIGURAR_REQUISITOS";
    case CONFIGURAR_CATEGORIAS = "CONFIGURAR_CATEGORIAS";
    case CONFIGURAR_PRIORIDADES = "CONFIGURAR_PRIORIDADES";
    case CONFIGURAR_CONTRIBUYENTES = "CONFIGURAR_CONTRIBUYENTES";
    case CONFIGURAR_CUESTIONARIO = "CONFIGURAR_CUESTIONARIO";

    public static function fromName(string $name): string
    {
        foreach (self::cases() as $status) {
            if( $name === $status->name ){
                return $status->value;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum " . self::class );
    }
}
