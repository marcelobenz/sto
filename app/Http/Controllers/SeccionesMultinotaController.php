<?php

namespace App\Http\Controllers;

use App\Models\SeccionMultinota;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;

class SeccionesMultinotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        // Obtiene todas las categorias activas de la base de datos
        //$categorias = Categoria::all();

        // Obtiene las categorias con paginación
        // $categorias = Categoria::paginate(10); // Cambia el 10 por el número de elementos que quieres por página

        if ($request->ajax()) {
            $data = SeccionMultinota::select('seccion.*')
            ->where('seccion.activo', true)
            ->where('seccion.temporal', false)
            ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('secciones-multinota.index');
    }
}