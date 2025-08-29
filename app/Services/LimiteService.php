<?php

namespace App\Services;

use App\Repositories\LimiteRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LimiteService
{
    protected $limiteRepository;

    public function __construct(LimiteRepository $limiteRepository)
    {
        $this->limiteRepository = $limiteRepository;
    }

    public function obtenerTodosLosGruposConUsuarios()
    {
        return $this->limiteRepository->obtenerGruposConUsuarios();
    }

    public function validarYActualizarLimites(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'limites' => 'required|array',
            'limites.*.id_usuario_interno' => 'required|integer|exists:usuario_interno,id_usuario_interno',
            'limites.*.limite' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Validación fallida',
                'errors' => $validator->errors()
            ];
        }

        $resultados = [];
        foreach ($request->limites as $limiteData) {
            $actualizado = $this->limiteRepository->actualizarLimiteUsuario(
                $limiteData['id_usuario_interno'],
                $limiteData['limite']
            );

            if ($actualizado) {
                Log::info("Límite guardado exitosamente para usuario ID: " . $limiteData['id_usuario_interno']);
                $resultados[] = [
                    'id_usuario_interno' => $limiteData['id_usuario_interno'],
                    'success' => true
                ];
            } else {
                $resultados[] = [
                    'id_usuario_interno' => $limiteData['id_usuario_interno'],
                    'success' => false,
                    'message' => 'Error al actualizar el límite'
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'Proceso de actualización completado',
            'results' => $resultados
        ];
    }
}