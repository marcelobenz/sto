<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class TramiteController extends Controller
{
    /**
     * Muestra la vista principal.
     */
    public function index()
    {
        return view('tramites.index');
    }

    /**
     * Proporciona los datos para el DataTable.
     */
    public function getTramitesData(Request $request)
    {
        $columnIndex = $request->get('order')[0]['column']; // Índice de la columna a ordenar
        $columnName = $request->get('columns')[$columnIndex]['data']; // Nombre de la columna
        $columnSortOrder = $request->get('order')[0]['dir']; // Orden (asc o desc)
        $searchValue = $request->get('search')['value']; // Valor de búsqueda
    
        // Construir la consulta base
        $query = DB::table('multinota as m')
            ->join('tramite as t', 'm.id_tramite', '=', 't.id_tramite')
            ->join('tipo_tramite_multinota as tt', 'm.id_tipo_tramite_multinota', '=', 'tt.id_tipo_tramite_multinota')
            ->join('contribuyente_externo as ce', 't.id_tramite', '=', 'ce.id_tramite')
            ->join('tramite_estado_tramite as te', 'm.id_tramite', '=', 'te.id_tramite')
            ->join('usuario_interno as u', 'te.id_usuario_interno', '=', 'u.id_usuario_interno')
            ->join('categoria as c', 'tt.id_categoria', '=', 'c.id_categoria')
            ->join('estado_tramite as e', 'te.id_estado_tramite', '=', 'e.id_estado_tramite')
            ->select(
                'm.id_tramite',
                'm.cuenta',
                'c.nombre as nombre_categoria',
                'tt.nombre as tipo_tramite',
                'e.nombre as estado',
                'm.fecha_alta',
                't.cuit_contribuyente',
                DB::raw("CONCAT(ce.nombre, ' ', ce.apellido) as contribuyente"),
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as usuario_interno")
            )
            ->where('te.activo', 1);
    
        // Filtro de búsqueda
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('m.id_tramite', 'like', "%{$searchValue}%")
                  ->orWhere('m.cuenta', 'like', "%{$searchValue}%")
                  ->orWhere('c.nombre', 'like', "%{$searchValue}%")
                  ->orWhere('tt.nombre', 'like', "%{$searchValue}%")
                  ->orWhere('e.nombre', 'like', "%{$searchValue}%")
                  ->orWhere(DB::raw("CONCAT(ce.nombre, ' ', ce.apellido)"), 'like', "%{$searchValue}%")
                  ->orWhere(DB::raw("CONCAT(u.nombre, ' ', u.apellido)"), 'like', "%{$searchValue}%");
            });
        }
    
        // Ordenar por columna seleccionada
        $query->orderBy($columnName, $columnSortOrder);
    
        // Total de registros después del filtro
        $totalFiltered = $query->count();
    
        // Paginación
        $data = $query->skip($request->get('start'))->take($request->get('length'))->get();

        // Reemplazar "A finalizar" con "Finalizado" en la columna estado
        $data = collect($data)->map(function ($item) {
            $item = (array) $item; // Convertir a array
            if ($item['estado'] === "A Finalizar") {
                $item['estado'] = "Finalizado";
            }
            return (object) $item; // Convertir de nuevo a objeto si es necesario
        });
        
        // Total de registros sin filtro
        $totalData = DB::table('multinota as m')
            ->join('tramite_estado_tramite as te', 'm.id_tramite', '=', 'te.id_tramite')
            ->where('te.activo', 1)
            ->count();
    
        // Respuesta en formato JSON
        return response()->json([
            "draw" => intval($request->get('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }
}
