<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\SeccionMultinota;
use App\Models\Campo;
use App\Models\OpcionCampo;
use Illuminate\Database\Eloquent\Collection;
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
        $tipos = Campo::select('tipo')->distinct()->get();

        Session::put('SECCION_ACTUAL', $seccion);
        Session::put('CAMPOS_ACTUALES', $campos);
        Session::put('TIPOS', $tipos);
        Session::put('OPCIONES_CAMPO_ACTUALES', null);

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

            if($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION') {
                $opc = OpcionCampo::select('*')
                ->where('id_campo', $c->id_campo)
                ->orderBy('orden', 'asc')
                ->get();
                
                $c['opciones_campo'] = $opc;
                Session::put('OPCIONES_CAMPO_ACTUALES', $opc);
            } else {
                $c['opciones_campo'] = null;
            }
        }

        return view('secciones-multinota.edit', compact('seccion', 'campos', 'tipos'));
    }

    public function crearNuevaSeccion() {
        //Se instancia objeto de SeccionMultinota y se asignan atributos vacíos
        $seccion = new SeccionMultinota();
        //ID dummy
        $seccion->id_seccion = 1;
        $seccion->temporal = 0;
        $seccion->titulo = '';
        $seccion->activo = 1;

        //Se instancia una coleccion de Campos vacía
        $campos = new Collection([]); 

        $tipos = Campo::select('tipo')->distinct()->get();

        Session::put('SECCION_ACTUAL', $seccion);
        Session::put('CAMPOS_ACTUALES', $campos);
        Session::put('TIPOS', $tipos);
        Session::put('OPCIONES_CAMPO_ACTUALES', null);
    
        return view('secciones-multinota.edit', compact('seccion', 'campos', 'tipos'));
    }

    public function desactivarSeccion($id) {
        $seccion = SeccionMultinota::find($id);
        if ($seccion) {
            $seccion->activo = 0; 
            $seccion->save();
            return response()->json(['message' => 'Sección desactivada correctamente.'], 200);
        } else {
            return response()->json(['message' => 'Sección no encontrada.'], 404);
        }
    }

    public function selectCampo($id) {
        $campos = Session::get('CAMPOS_ACTUALES');
        
        for ($i = 0; $i < count($campos); $i++) {
            if($campos[$i]->id_campo == $id) {
                $campoSelected = $campos[$i];
            }
        }

        if($campoSelected->tipo == 'LISTA' || $campoSelected->tipo == 'CAJAS_SELECCION') {
            Session::put('OPCIONES_CAMPO_ACTUALES', $campoSelected->opciones_campo);
        }

        $tipos = Session::get('TIPOS');

        Session::put('ID_CAMPO_SELECTED', $campoSelected->id_campo);
        Session::put('CAMPO_SELECTED', $campoSelected);
        
        return view('partials.editar-campo', compact('campoSelected', 'tipos'));
    }

    public function nuevoCampo() {
        $tipos = Session::get('TIPOS');

        $campoSelected = new Campo();
        $campoSelected->nombre = '';
        $campoSelected->tipo = 'STRING';
        $campoSelected->dimension = 1;
        $campoSelected->limite_minimo = null;
        $campoSelected->limite_maximo = null;
        $campoSelected->mascara = null;
        $campoSelected->obligatorio = 0;
        $campoSelected['opciones_campo'] = null;
        Session::put('CAMPO_SELECTED', $campoSelected);

        return view('partials.editar-campo', compact('campoSelected', 'tipos'));
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

    public function actualizarDatosCampo(Request $request) {
        if($request->has('actualizar-datos-campo')) { 
            $seccion = Session::get('SECCION_ACTUAL');
            $campos = Session::get('CAMPOS_ACTUALES');
            $campoSelected = Session::get('CAMPO_SELECTED');

            $campoSelected->nombre = $request->post('nombre');
            
            if($campoSelected->tipo == 'LISTA' || $campoSelected->tipo == 'CAJAS_SELECCION') {
                $campoSelected['opciones_campo'] = Session::get('OPCIONES_CAMPO_ACTUALES');
            }

            $campoSelected->dimension = (int)$request->post('tamaño');
            if($request->post('obligatorio') == 'on') {
                $campoSelected->obligatorio = 1;    
            } else {
                $campoSelected->obligatorio = 0;
            }

            if($request->post('lleva-mascara') == 'on') {
                if($request->post('lleva-mascara-input-container') != null) {
                    $campoSelected->mascara = $request->post('lleva-mascara-input-container');
                }
            } else {
                $campoSelected->mascara = null;
            }

            if($request->post('limitar-caracteres') == 'on') {
                if($request->post('limitar-caracteres-input-min') != null && $request->post('limitar-caracteres-input-max') != null) {
                    $campoSelected->limite_minimo = $request->post('limitar-caracteres-input-min');
                    $campoSelected->limite_maximo = $request->post('limitar-caracteres-input-max');

                    if($campoSelected->tipo == 'INTEGER') {
                        $campoSelected['limite_minimo_num'] = '1';
                        if($campoSelected->limite_minimo == 1) {
                            $campoSelected->limite_minimo_num = '0';
                        } else {
                            for ($i = 0; $i < $campoSelected->limite_minimo; $i++) {
                                $campoSelected->limite_minimo_num = $campoSelected->limite_minimo_num . '0';
                            }
                        }
                        
                        if($campoSelected->limite_maximo != null) {
                            $campoSelected['limite_maximo_num'] = '9';
                            if($campoSelected->limite_maximo != 1) {
                                for ($i = 1; $i < $campoSelected->limite_maximo; $i++) {
                                    $campoSelected->limite_maximo_num = $campoSelected->limite_maximo_num . '9';
                                }
                            }
                        }
                    }
                }
            } else {
                $campoSelected->limite_minimo = null;
                $campoSelected->limite_maximo = null;
            }

            $existeElCampo = false;

            for ($i=0; $i<count($campos); $i++) { 
                if($campos[$i]->id_campo == $campoSelected->id_campo) {
                    $campos[$i] = $campoSelected;
                    $existeElCampo = true;
                }
            }

            if(!$existeElCampo) {
                //TO-DO - Revisar en el guardado real si esto es asi o rompe algo
                if(count($campos) == 0) {
                    $campos[count($campos)] = $campoSelected;
                    $campos[count($campos)-1]->id_campo = 1;
                    $campos[count($campos)-1]->orden = 0;
                } else {
                    $id_campo = $campos[count($campos)-1]->id_campo + 1;
                    $campos[count($campos)] = $campoSelected;
                    $campos[count($campos)-1]->id_campo = $id_campo;
                    $campos[count($campos)-1]->orden = $campos[count($campos)-2]->orden + 1;
                }
            }

            return view('secciones-multinota.edit', compact('seccion', 'campos'));
        }
    }

    public function getOpcionesCampo() {
        $opcionesCampo = Session::get('OPCIONES_CAMPO_ACTUALES');

        return view('partials.seccion-opciones-campo', compact('opcionesCampo'));
    }

    public function getOpcionesCampoAlfabeticamente() {
        $opcionesCampo = Session::get('OPCIONES_CAMPO_ACTUALES')->sortBy('opcion')->values();

        foreach ($opcionesCampo as $index => $opc) {
            $opc->orden = $index;
        }

        Session::put('OPCIONES_CAMPO_ACTUALES', $opcionesCampo);

        return view('partials.seccion-opciones-campo', compact('opcionesCampo'));
    }

    public function addOpcionCampo($nueva_opcion) {
        $opcionesCampo = Session::get('OPCIONES_CAMPO_ACTUALES');

        if($opcionesCampo == null) {
            $opcionesCampo = collect();
        }

        if(count($opcionesCampo) != 0) {
            $opcionesCampoDummyId = $opcionesCampo[count($opcionesCampo) - 1]->id_opcion_campo;
        } else {
            $opcionesCampoDummyId = -1;
        }

        $nuevaOpcion = new OpcionCampo();
        $nuevaOpcion->id_opcion_campo = $opcionesCampoDummyId + 1;
        $nuevaOpcion->opcion = $nueva_opcion;

        $opcionesCampo->push($nuevaOpcion);

        Session::put('OPCIONES_CAMPO_ACTUALES', $opcionesCampo);

        return view('partials.seccion-opciones-campo', compact('opcionesCampo'));
    }

    public function getOpcionesFormTipoCampo($tipo) {
        //TO-DO - Cambiar estructura de array a objeto ya que el campo seleccionado es 1 solo
        $campoSelected = Session::get('CAMPO_SELECTED');

        $campoSelected->setTipo($tipo);

        $tipos = Session::get('TIPOS');

        if($tipo == 'LISTA' || $campoSelected->tipo == 'CAJAS_SELECCION') {
            Session::put('OPCIONES_CAMPO_ACTUALES', []);
            $campoSelected->opciones_campo = [];
        }

        return view('partials.editar-campo', compact('campoSelected', 'tipos'));
    }

    public function deleteOpcionCampo($id) {
        $opcionesCampo = Session::get('OPCIONES_CAMPO_ACTUALES');

        $nuevasOpciones = $opcionesCampo->reject(function ($opc) use ($id) {
            return (string)$opc->id_opcion_campo === $id;
        });

        $opcionesCampo = $nuevasOpciones->values();

        Session::put('OPCIONES_CAMPO_ACTUALES', $opcionesCampo);

        return view('partials.seccion-opciones-campo', compact('opcionesCampo'));
    }

    public function editarSeccion(Request $request) {
        $seccionActual = Session::get('SECCION_ACTUAL');
        $campos = Session::get('CAMPOS_ACTUALES');
        $opcionesCampo = Session::get('OPCIONES_CAMPO_ACTUALES');

        //1) Actualizar seccion actual, seteando activo = 0. Si la seccion tiene ID = 1 (ID dummy), no se busca en base porque no existe
        if($seccionActual->id_seccion != 1) {
            $seccion = SeccionMultinota::find($seccionActual->id_seccion);
            $seccion->activo = 0;
            $seccion->save();
        }

        //2) Insertar nueva seccion, seteando activo = 1
        $nuevaSeccion = new SeccionMultinota();
        $nuevaSeccion->titulo = $request->titulo;
        $nuevaSeccion->temporal = 0;
        $nuevaSeccion->activo = 1;
        $nuevaSeccion->save();

        $nuevaSeccion = SeccionMultinota::where('activo', 1)
        ->where('titulo', $request->titulo)
        ->get();

        //3) Se insertan los campos de manera asociada al nuevo id de la seccion multinota
        foreach ($campos as $c) {
            $nuevoCampo = new Campo();
            $nuevoCampo->nombre = $c->nombre;
            $nuevoCampo->tipo = $c->tipo;
            $nuevoCampo->dimension = $c->dimension;
            $nuevoCampo->orden = $c->orden;
            $nuevoCampo->obligatorio = $c->obligatorio;
            $nuevoCampo->mascara = $c->mascara;
            $nuevoCampo->limite_minimo = $c->limite_minimo;
            $nuevoCampo->limite_maximo = $c->limite_maximo;
            $nuevoCampo->id_seccion = $nuevaSeccion[0]->id_seccion;
            $nuevoCampo->save();

            //4) Para los campos de tipo LISTA o CAJAS_SELECCION, se insertan nuevos registros en opcion_campo con la referencia del nuevo id_campo correspondiente
            if($c->tipo == 'LISTA' || $c->tipo == 'CAJAS_SELECCION') {
                $id_campo = Campo::where('id_seccion', $nuevaSeccion[0]->id_seccion)
                ->max('id_campo');

                foreach ($opcionesCampo as $opc) {
                    $opcion = new OpcionCampo();
                    $opcion->id_campo = $id_campo;
                    $opcion->opcion = $opc->opcion;
                    $opcion->orden = $opc->orden;
                    $opcion->save();
                }
            }
        }

        return view('secciones-multinota.index');
    }

    public function updateSeccion(Request $request) {
        $campos = Campo::hydrate($request->input('array'));
        foreach ($campos as $index => $c) {
            $c->orden = $index;
        }
        Session::put('CAMPOS_ACTUALES', $campos);
        $seccion = Session::get('SECCION_ACTUAL');

        // Render the partial view with updated data
        $html = view('partials.seccion-campos', compact('campos'))->render();

        // Return JSON response with rendered HTML
        return response()->json(['html' => $html]);
    }

    public function updateSeccionOpcionesCampo(Request $request) {
        //TO-DO - Checkear porque estoy usando Campo::hydrate. No se si esta bien
        $opcionesCampo = Campo::hydrate($request->input('array'));
        foreach ($opcionesCampo as $index => $opc) {
            $opc->orden = $index;
        }
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