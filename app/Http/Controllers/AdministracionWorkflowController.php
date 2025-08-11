<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdministrarWorkflowService;
use App\Models\GrupoInterno;
use DataTables;

class AdministracionWorkflowController extends Controller
{
    protected $workflowService;

    public function __construct(AdministrarWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->workflowService->obtenerDatosParaDataTable();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->filterColumn('categoria', function($query, $keyword) {
                    $query->whereRaw('LOWER(categoria.nombre) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                })
                ->filterColumn('nombre_tipo_tramite', function($query, $keyword) {
                    $query->whereRaw('LOWER(tipo_tramite_multinota.nombre) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                })
                ->make(true);
        }

        return view('estados.index');
    }

    public function crear($id)
    {
        $data = $this->workflowService->prepararDatosParaCrear($id);
        $grupos = GrupoInterno::with('usuarios')->get();

        return view('estados.crear', array_merge($data, compact('grupos')));
    }

    public function editar($id)
    {
        $data = $this->workflowService->prepararDatosParaEditar($id);
        $grupos = GrupoInterno::with('usuarios')->get();

        return view('estados.editar', array_merge($data, compact('grupos')));
    }

    public function borrador($id)
    {
        $data = $this->workflowService->prepararDatosParaEditar($id, 0, 0);
        $grupos = GrupoInterno::with('usuarios')->get();

        return view('estados.borrador', array_merge($data, compact('grupos')));
    }

    public function guardar(Request $request, $id)
    {
        $configuraciones = $request->input('configuraciones');

        if (!$configuraciones || !is_array($configuraciones)) {
            return response()->json(['success' => false, 'message' => 'Datos inválidos']);
        }

        $resultado = $this->workflowService->guardarWorkflow($id, $configuraciones);

        return response()->json([
            'success' => $resultado,
            'message' => $resultado 
                ? 'Workflow guardado correctamente.' 
                : 'Error al guardar el workflow.'
        ]);
    }

    public function guardarEdicion(Request $request, $id)
    {
        $configuraciones = $request->input('configuraciones');

        if (!$configuraciones || !is_array($configuraciones)) {
            return response()->json(['success' => false, 'message' => 'Datos inválidos: configuraciones no es un array']);
        }

        $resultado = $this->workflowService->guardarWorkflow($id, $configuraciones);

        return response()->json([
            'success' => $resultado,
            'message' => $resultado 
                ? 'Workflow actualizado correctamente.' 
                : 'Error al guardar el workflow.'
        ]);
    }

    public function publicarBorrador(Request $request, $id)
    {
        $configuraciones = $request->input('configuraciones');

        if (!$configuraciones || !is_array($configuraciones)) {
            return response()->json(['success' => false, 'message' => 'Datos inválidos: configuraciones no es un array']);
        }

        $resultado = $this->workflowService->publicarBorrador($id, $configuraciones);

        return response()->json([
            'success' => $resultado,
            'message' => $resultado 
                ? 'Workflow publicado correctamente.' 
                : 'Error al publicar el workflow.'
        ]);
    }

    public function guardarBorrador(Request $request, $id)
    {
        $configuraciones = $request->input('configuraciones');

        if (!$configuraciones || !is_array($configuraciones)) {
            return response()->json(['success' => false, 'message' => 'Datos inválidos: configuraciones no es un array']);
        }

        $resultado = $this->workflowService->guardarBorrador($id, $configuraciones);

        return response()->json([
            'success' => $resultado,
            'message' => $resultado 
                ? 'Borrador guardado correctamente.' 
                : 'Error al guardar el borrador.'
        ]);
    }
}