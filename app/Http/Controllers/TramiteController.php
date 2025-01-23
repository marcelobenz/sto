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
        $start = $request->get('start', 0); // Índice inicial
        $length = $request->get('length', 10); // Número de registros por página
        $search = $request->get('search')['value']; // Valor de búsqueda
    
        // Construir consulta base
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
                'm.fecha_alta',
                't.cuit_contribuyente',
                DB::raw("CONCAT(ce.nombre, ' ', ce.apellido) AS contribuyente"),
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) AS usuario_interno"),
                'c.nombre as nombre_categoria',
                'e.nombre as estado',
                'tt.nombre as tipo_tramite'
            )
            ->where('te.activo', 1);
    
        // Filtro de búsqueda
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('m.id_tramite', 'like', "%{$search}%")
                  ->orWhere('m.cuenta', 'like', "%{$search}%")
                  ->orWhere('t.cuit_contribuyente', 'like', "%{$search}%")
                  ->orWhere('c.nombre', 'like', "%{$search}%")
                  ->orWhere('e.nombre', 'like', "%{$search}%");
            });
        }
    
        // Contar total de registros filtrados
        $totalFiltered = $query->count();
    
        // Obtener datos paginados
        $data = $query->skip($start)->take($length)->get();
    
        // Contar total de registros sin filtros
        $totalData = DB::table('multinota as m')
            ->join('tramite_estado_tramite as te', 'm.id_tramite', '=', 'te.id_tramite')
            ->where('te.activo', 1)
            ->count();
    
        // Formatear respuesta para DataTable
        return response()->json([
            'draw' => $request->get('draw'), // Enviar el número de draw para sincronización
            'recordsTotal' => $totalData, // Total de registros sin filtro
            'recordsFiltered' => $totalFiltered, // Total de registros filtrados
            'data' => $data // Datos a mostrar en la tabla
        ]);
    }
}
