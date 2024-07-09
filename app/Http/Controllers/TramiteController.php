<?php

namespace App\Http\Controllers;

use App\Models\Tramite;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TramiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtiene todos los trámites de la base de datos
        //$tramites = Tramite::all();

        // Obtiene los trámites con paginación
        // $tramites = Tramite::paginate(10); // Cambia el 10 por el número de elementos que quieres por página

        if ($request->ajax()) {
            $data = Tramite::select('id_tramite','fecha_alta','fecha_modificacion','correo',
                    'cuit_contribuyente');
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        // Retorna la vista con los trámites
        //return view('tramites.index', compact('tramites'));
        return view('tramites.index');

    }
}
