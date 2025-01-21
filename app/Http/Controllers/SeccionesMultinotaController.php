<?php

namespace App\Http\Controllers;

use App\Models\SeccionMultinota;
use App\Models\Campo;
use App\Models\OpcionCampo;
use Illuminate\Support\Facades\Session;
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

        Session::put('SECCION_ACTUAL', $seccion);
        Session::put('CAMPOS_ACTUALES', $campos);

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

    public function deleteCampo($id) {
        $seccion = Session::get('SECCION_ACTUAL');
        $campos = Session::get('CAMPOS_ACTUALES');
        
        for ($i = 0; $i <= count($campos); $i++) {
            if($campos[$i]->id_campo == $id) {
                unset($campos[$i]);
                $camposAux = $campos->values();
                Session::put('CAMPOS_ACTUALES', $camposAux);
            }
        }

        $campos = Session::get('CAMPOS_ACTUALES');

        return view('secciones-multinota.edit', compact('seccion', 'campos'));
    }

    public function selectCampo($id) {
        $campos = Campo::select('*')
        ->where('id_campo', $id)
        ->get();

        $tipos = Campo::select('tipo')->distinct()->get();
        $tiposWithId = $tipos->map(function ($item, $index) {
            return ['id' => $index + 1, 'tipo' => $item->tipo];
        });

        return view('partials.editar-campo', compact('campos', 'tipos'));
    }

    public function getOpcionesCampo($id, $tipo) {
        $opcionesCampo = OpcionCampo::select('*')
        ->where('id_campo', $id)
        ->get();

        Session::put('OPCIONES_CAMPO_ACTUALES', $opcionesCampo);

        return view('partials.seccion-opciones-campo', compact('opcionesCampo'));
    }

    public function getOpcionesFormTipoCampo($id, $tipo) {
        //TO-DO - Evitar tener que recuperar de la base nuevamente
        //TO-DO - Cambiar estructura de array a objeto ya que el campo seleccionado es 1 solo
        $campos = Campo::select('*')
        ->where('id_campo', $id)
        ->get();

        $campos[0]->setTipo($tipo);

        $tipos = Campo::select('tipo')->distinct()->get();
        $tiposWithId = $tipos->map(function ($item, $index) {
            return ['id' => $index + 1, 'tipo' => $item->tipo];
        });

        return view('partials.editar-campo', compact('campos', 'tipos'));
    }

    public function deleteOpcionCampo($id) {
        error_log('aaa');
    }

    public function update($id) {
        error_log('a');
    }

    public function updateSeccion(Request $request) {
        $campos = Campo::hydrate($request->input('array'));
        Session::put('CAMPOS_ACTUALES', $campos);
        $seccion = Session::get('SECCION_ACTUAL');

        // Render the partial view with updated data
        $html = view('partials.seccion-campos', compact('campos'))->render();

        // Return JSON response with rendered HTML
        return response()->json(['html' => $html]);
    }

    public function updateSeccionOpcionesCampo(Request $request) {
        $opcionesCampo = Campo::hydrate($request->input('array'));
        Session::put('OPCIONES_CAMPO_ACTUALES', $opcionesCampo);

        // Render the partial view with updated data
        $html = view('partials.seccion-opciones-campo', compact('opcionesCampo'))->render();

        // Return JSON response with rendered HTML
        return response()->json(['html' => $html]);
    }

    public function refresh() {
        $campos = Session::get('CAMPOS_ACTUALES');

        return response()->json([
            'updatedCampos' => $campos,
        ]);
    }

    public function refreshOpcionesCampo() {
        $opcionesCampo = Session::get('OPCIONES_CAMPO_ACTUALES');

        return response()->json([
            'updatedOpcionesCampo' => $opcionesCampo,
        ]);
    }

    public function setCampos(String $campos) {
		$this->campos = $campos;
	}

    public function getCampos() {
		return $this->campos;
	}
}