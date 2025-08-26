<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\ContribuyenteMultinota;
use Illuminate\Http\Request;
use App\Models\Direccion;

class IngresoExternoRepository
{
    public function obtenerTramitesPorCuit($cuit)
    {
        return DB::table('multinota as m')
            ->join('tramite_estado_tramite as tet', 'tet.id_tramite', '=', 'm.id_tramite')
            ->join('tipo_tramite_multinota as ttm', 'ttm.id_tipo_tramite_multinota', '=', 'm.id_tipo_tramite_multinota')
            ->join('categoria as c', 'ttm.id_categoria', '=', 'c.id_categoria')
            ->leftJoin('usuario_interno as u', 'u.id_usuario_interno', '=', 'tet.id_usuario_interno')
            ->select(
                'm.id_tramite',
                'm.fecha_alta',
                'm.correo',
                'm.cuit_contribuyente',
                'c.nombre as nombre_categoria'
            )
            ->where('m.cuit_contribuyente', $cuit)
            ->distinct()
            ->orderBy('m.id_tramite', 'DESC');
    }


    public function findByCuit(string $cuit): ?ContribuyenteMultinota
    {
        return ContribuyenteMultinota::where('cuit', $cuit)->first();
    }


    public function crearDireccion(array $datos): Direccion
    {
        return Direccion::create([
            'calle'         => $datos['calle'],
            'numero'        => $datos['numero'],
            'codigo_postal' => $datos['codigo_postal'] ?? null,
            'provincia'     => $datos['provincia'] ?? null,
            'localidad'     => $datos['localidad'] ?? null,
            'pais'          => $datos['pais'] ?? null,
            'latitud'       => $datos['latitude'] ?? null,
            'longitud'      => $datos['longitude'] ?? null,
            'fecha_alta'    => now(),
        ]);
    }

    public function crearUsuario(array $datos, int $idDireccion): ContribuyenteMultinota
    {
        return ContribuyenteMultinota::create([
            'cuit'              => $datos['cuit'],
            'correo'            => $datos['correo'],
            'nombre'            => $datos['nombre'],
            'apellido'          => $datos['apellido'],
            'telefono1'         => $datos['telefono_1'] ?? null,
            'telefono2'         => $datos['telefono_2'] ?? null,
            'clave'             => bcrypt($datos['clave']),
            'id_direccion'      => $idDireccion,
        ]);
    }
}
