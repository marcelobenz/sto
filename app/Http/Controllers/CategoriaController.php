<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtiene todas las categorias activas de la base de datos
        // $categorias = Categoria::all();

        // Obtiene las categorias con paginación
        // $categorias = Categoria::paginate(10); // Cambia el 10 por el número de elementos que quieres por página

        if ($request->ajax()) {
            $data = Categoria::leftJoin('categoria as parent', 'categoria.id_padre', '=', 'parent.id_categoria')
                ->select('categoria.*', 'parent.nombre as parent_nombre')
                ->where('categoria.flag_activo', 1)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        // Retorna la vista con las categorias
        // return view('categorias.index', compact('categorias'));
        return view('categorias.index');
    }

    public function desactivar(Request $request, $id)
    {
        $categoria = Categoria::find($id);
        if ($categoria) {
            $categoria->flag_activo = 0;
            $categoria->save();

            return response()->json(['message' => 'Categoría desactivada correctamente.'], 200);
        } else {
            return response()->json(['message' => 'Categoría no encontrada.'], 404);
        }
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoriasActivas = Categoria::where('flag_activo', 1)->get();

        return view('categorias.edit', compact('categoria', 'categoriasActivas'));
    }

    public function update(Request $request, $id)
    {
        // Validar los datos del request
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_padre' => 'nullable|integer',
            'flag_activo' => 'required|boolean',
        ]);

        // Encontrar la categoría por ID
        $categoria = Categoria::find($id);

        if (! $categoria) {
            return redirect()->route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        // Actualizar los campos de la categoría
        $categoria->nombre = $request->nombre;
        $categoria->id_padre = $request->id_padre;
        $categoria->flag_activo = $request->flag_activo;

        // Guardar los cambios en la base de datos
        $categoria->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function create()
    {
        $categoriasActivas = Categoria::where('flag_activo', 1)->get();

        return view('categorias.create', compact('categoriasActivas'));
    }

    // Guarda la nueva categoría en la base de datos
    public function store(Request $request)
    {
        // Validar los datos del request
        $request->validate([
            'nombre' => 'required|string|max:255',
            'id_padre' => 'nullable|integer',
            'flag_activo' => 'required|boolean',
        ]);

        $categoria = new Categoria;
        $categoria->nombre = $request->input('nombre');
        $categoria->id_padre = $request->input('id_padre');
        $categoria->flag_activo = $request->input('flag_activo');
        $categoria->fecha_alta = now(); // Asigna la fecha actual

        $categoria->save();

        // Redirigir con un mensaje de éxito
        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }
}
