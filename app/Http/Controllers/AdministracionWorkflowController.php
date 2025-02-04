<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoTramiteMultinota;
use App\Models\Categoria;
use DataTables;

class AdministracionWorkflowController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TipoTramiteMultinota::join('categoria', 'tipo_tramite_multinota.id_categoria', '=', 'categoria.id_categoria')
                ->where('tipo_tramite_multinota.baja_logica', 0) 
                ->select([
                    'tipo_tramite_multinota.id_tipo_tramite_multinota',
                    'categoria.nombre as categoria',
                    'tipo_tramite_multinota.nombre as nombre_tipo_tramite'
                ]);
    
                return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    
        return view('estados.index');
    }
    
    

    public function edit($id)
    {
        $tramite = TipoTramiteMultinota::findOrFail($id);
        return view('tramites.edit', compact('tramite'));
    }
}
