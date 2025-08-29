<?php

namespace App\Http\Controllers;

use App\Services\CuestionarioService;
use Illuminate\Http\Request;

class CuestionarioController extends Controller
{
    protected $service;

    public function __construct(CuestionarioService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $cuestionarios = $this->service->getAllCuestionarios();
        return view('cuestionarios.index', compact('cuestionarios'));
    }

   public function create()
    {
        $data = $this->service->getDataForCreateView();
        return view('cuestionarios.crear', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'preguntas' => 'required|json'
        ]);

        $this->service->createCuestionario($request->all());

        return redirect()->route('cuestionarios.index')
            ->with('success', 'Cuestionario y preguntas guardados exitosamente.');
    }

    public function edit($id)
    {
        $data = $this->service->getDataForEditView($id);
        return view('cuestionarios.editar', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'preguntas' => 'array',
            'nuevas_preguntas' => 'nullable|array',
            'tipo_tramite_multinota' => 'nullable|array'
        ]);

        $this->service->updateCuestionario($id, $request->all());

        return redirect()->route('cuestionarios.index')
            ->with('success', 'Cuestionario y preguntas actualizados exitosamente.');
    }

    public function activar($id)
    {
        $this->service->toggleCuestionarioStatus($id, true);
        return redirect()->route('cuestionarios.index')
            ->with('success', 'Cuestionario activado exitosamente.');
    }

    public function desactivar($id)
    {
        $this->service->toggleCuestionarioStatus($id, false);
        return redirect()->route('cuestionarios.index')
            ->with('success', 'Cuestionario desactivado exitosamente.');
    }
}