<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

enum Permiso: string
{
	case VER_REPORTES = "VER REPORTES";
    case TOMAR_TRAMITE = "TOMAR TRAMITE";
    case REASIGNAR_TRAMITE = "REASIGNAR TRAMITE";
    case DAR_DE_BAJA_TRAMITE = "DAR DE BAJA TRAMITE";
    case VER_TRAMITES_CANCELADOS = "VER TRAMITES CANCELADOS";
    case VER_TODOS_LOS_TRAMITES = "VER TODOS LOS TRAMITES";
    case CONFIGURAR_ESTADOS = "CONFIGURAR ESTADOS";
    case CONFIGURAR_NIVELES = "CONFIGURAR NIVELES";
    case CONFIGURAR_LIMITES = "CONFIGURAR LIMITES";
    case CONFIGURAR_USUARIOS = "CONFIGURAR USUARIOS";
    case CONFIGURAR_MULTINOTA = "CONFIGURAR MULTINOTA";
    case CONFIGURAR_SECCIONES = "CONFIGURAR SECCIONES";
    case CONFIGURAR_REQUISITOS = "CONFIGURAR REQUISITOS";
    case CONFIGURAR_CATEGORIAS = "CONFIGURAR CATEGORIAS";
    case CONFIGURAR_PRIORIDADES = "CONFIGURAR PRIORIDADES";
    case CONFIGURAR_CONTRIBUYENTES = "CONFIGURAR CONTRIBUYENTES";
    case CONFIGURAR_CUESTIONARIO = "CONFIGURAR CUESTIONARIO";
  
    
}
