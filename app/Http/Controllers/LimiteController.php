<?php

namespace App\Http\Controllers;

use App\Services\LimiteService;
use Illuminate\Http\Request;

class LimiteController extends Controller
{
    protected $limiteService;

    public function __construct(LimiteService $limiteService)
    {
        $this->limiteService = $limiteService;
    }

    public function index()
    {
        $grupos = $this->limiteService->obtenerTodosLosGruposConUsuarios();
        return view('limites.index', compact('grupos'));
    }

    public function guardarLimite(Request $request)
    {
        $resultado = $this->limiteService->validarYActualizarLimites($request);
        
        if (!$resultado['success']) {
            return response()->json([
                'message' => $resultado['message'],
                'errors' => $resultado['errors'] ?? null
            ], 400);
        }

        return response()->json([
            'message' => $resultado['message'],
            'results' => $resultado['results']
        ]);
    }
}