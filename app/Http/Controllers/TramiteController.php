<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;
use Log;

class TramiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('tramite as t')
                ->join('multinota as m', 't.id_tramite', '=', 'm.id_tramite')
                ->join('tramite_estado_tramite as tet', 'tet.id_tramite', '=', 't.id_tramite')
                ->join('tipo_tramite_multinota as ttm', 'ttm.id_tipo_tramite_multinota', '=', 'm.id_tipo_tramite_multinota')
                ->join('categoria as c', 'ttm.id_categoria', '=', 'c.id_categoria')
                ->leftJoin('usuario_interno as u', 'u.id_usuario_interno', '=', 'tet.id_usuario_interno')
                ->select(
                    't.id_tramite', 
                    't.fecha_alta', 
                    't.fecha_modificacion', 
                    't.correo', 
                    't.cuit_contribuyente', 
                    'c.nombre as nombre_categoria'
                )
                ->distinct()
                ->orderBy('t.id_tramite', 'DESC')
                ->get();

            // Log para depuración
            Log::info('Consulta SQL ejecutada');
            Log::info($data);

            if ($data->isEmpty()) {
                Log::info('No se encontraron registros');
            } else {
                Log::info('Registros encontrados', ['count' => $data->count()]);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('tramites.index');
    }
}
