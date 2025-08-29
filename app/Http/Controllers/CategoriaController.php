<?php

namespace App\Http\Controllers;

use App\Services\CategoriaService;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CategoriaController extends Controller
{
    protected $categoriaService;

    public function __construct(CategoriaService $categoriaService)
    {
        $this->categoriaService = $categoriaService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->categoriaService->getAllCategoriesForDataTables();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('categorias.index');
    }

    public function desactivar(Request $request, $id)
    {
        $result = $this->categoriaService->deactivateCategory($id);
        
        if ($result) {
            return response()->json(['message' => 'Categoría desactivada correctamente.'], 200);
        }
        
        return response()->json(['message' => 'Categoría no encontrada.'], 404);
    }

    public function edit($id)
    {
        $categoria = $this->categoriaService->findOrFailCategory($id);
        $categoriasActivas = $this->categoriaService->getActiveCategories();
        return view('categorias.edit', compact('categoria', 'categoriasActivas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_padre' => 'nullable|integer',
            'flag_activo' => 'required|boolean',
        ]);

        $result = $this->categoriaService->updateCategory($id, $request->all());

        if (!$result) {
            return redirect()->route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function create()
    {
        $categoriasActivas = $this->categoriaService->getActiveCategories();
        return view('categorias.create', compact('categoriasActivas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_padre' => 'nullable|integer',
            'flag_activo' => 'required|boolean',
        ]);

        $this->categoriaService->createCategory($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }
}