<?php
namespace App\Services;

use App\Repositories\IngresoExternoRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class IngresoExternoService
{
    protected $ingresoExternoRepository;

    public function __construct(IngresoExternoRepository $ingresoExternoRepository)
    {
        $this->ingresoExternoRepository = $ingresoExternoRepository;
    }

    public function obtenerTramitesParaCuit($cuit)
    {
        $query = $this->ingresoExternoRepository->obtenerTramitesPorCuit($cuit);

        Log::info('Consulta SQL ejecutada: ' . $query->toSql());

        $registros = $query->get();

        if ($registros->isEmpty()) {
            Log::info('No se encontraron registros');
        } else {
            Log::info('Registros encontrados', ['count' => $registros->count()]);
        }

        return $registros;
    }


    public function login(Request $request)
    {
        $cuit = $request->input('cuit');
        $clave = $request->input('clave');

        $usuario = $this->ingresoExternoRepository->findByCuit($cuit);

        if ($usuario && Hash::check($clave, $usuario->clave)) {
            return $usuario;
        }

        return null;
    }


    public function registrarUsuario(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $direccion = $this->ingresoExternoRepository->crearDireccion($request->all());
            $usuario = $this->ingresoExternoRepository->crearUsuario($request->all(), $direccion->id_direccion);

            session(['contribuyente_multinota' => $usuario]);
            session(['isExterno' => true]);

            return $usuario;
        });
    }
}
