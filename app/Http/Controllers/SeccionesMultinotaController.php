<?php

namespace App\Http\Controllers;

use App\Models\SeccionMultinota;
use App\Models\Campo;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;

class SeccionesMultinotaController extends Controller
{
    private $seccion;
    private $campos;

    public function index(Request $request) {
        $data = null;

        if ($request->ajax()) {
            $data = SeccionMultinota::select('seccion.*')
            ->where('seccion.activo', true)
            ->where('seccion.temporal', false)
            ->get();

            foreach ($data as &$d) {
                $camposString = '';

                $campos = Campo::select('*')
                ->where('id_seccion', $d->id_seccion)
                ->orderBy('orden', 'ASC')
                ->get();

                foreach ($campos as &$c) {
                    if($campos[count($campos)-1]->id_campo == $c->id_campo) {
                        $camposString = $camposString . $c->nombre;
                    } else {
                        $camposString = $camposString . $c->nombre . ', ';    
                    }
                }
    
                $d['campos'] = $camposString;
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('secciones-multinota.index');
    }

    public function edit($id) {
        $seccion = SeccionMultinota::findOrFail($id);
        $campos = Campo::select('*')
        ->where('id_seccion', $id)
        ->get();

        foreach ($campos as &$c) {
            if($c->tipo == 'INTEGER') {
                if($c->limite_minimo != null) {
                    $c['limite_minimo_num'] = '1';
                    if($c->limite_minimo == 1) {
                        $c->limite_minimo_num = '0';
                    } else {
                        for ($i = 0; $i < $c->limite_minimo; $i++) {
                            $c->limite_minimo_num = $c->limite_minimo_num . '0';
                        }
                    }
                }
                
                if($c->limite_maximo != null) {
                    $c['limite_maximo_num'] = '9';
                    if($c->limite_maximo != 1) {
                        for ($i = 1; $i < $c->limite_maximo; $i++) {
                            $c->limite_maximo_num = $c->limite_maximo_num . '9';
                        }
                    }
                }
            }
        }

        return view('secciones-multinota.edit', compact('seccion', 'campos'));
    }
}