<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\TipoTramiteMultinota;
use App\Models\Categoria;
use App\Models\MensajeInicial;
use App\Models\TipoTramiteMensajeInicial;
use App\Models\MultinotaTipoCuenta;
use App\Models\MultinotaSeccion;
use App\Models\SeccionMultinota;
use App\Models\Campo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use DB;

class MultinotaController extends Controller
{
    public function index(Request $request) {
        $shouldRefreshData = false;
        $dataFiltrada = null;

        // Chequea si la data existe en la cache
        if (!Cache::has('DATA_MULTINOTAS') || !Cache::has('CATEGORIAS') || !Cache::has('LAST_UPDATE_MULTINOTAS')) {
            $shouldRefreshData = true; // No existe asi que tenemos que refrescarla
        } else {
            // Chequea si la data necesita ser recuperada de base de nuevo segun la fecha de ultima actualizacion
            $lastUpdate = Cache::get('LAST_UPDATE_MULTINOTAS');
            $lastMultinotaUpdate = TipoTramiteMultinota::max('fecha_ultima_actualizacion');
            
            // Se convierte la fecha de ultima actualización de base y la fecha de la cache a una instancia de la librería Carbon
            $lastMultinotaUpdateUTC = Carbon::createFromFormat('Y-m-d H:i:s', $lastMultinotaUpdate, 'UTC');
            $lastUpdateUTC = Carbon::createFromFormat('Y-m-d H:i:s', $lastUpdate, 'UTC');

            // Se convierte la instancia a GMT-3
            $lastMultinotaUpdateHoraLocal = $lastMultinotaUpdateUTC->setTimezone('America/Sao_Paulo');
            $lastUpdateHoraLocal = $lastUpdateUTC->setTimezone('America/Sao_Paulo');

            if ($lastMultinotaUpdateHoraLocal > $lastUpdateHoraLocal) {
                $shouldRefreshData = true; // La base fue actualizada, necesitamos refrescar la cache
            }
        }

        if ($shouldRefreshData) {
            // Recupera la data desde base
            $data = TipoTramiteMultinota::distinct()
                ->where('baja_logica', 0)
                ->orderBy('nombre')
                ->get();

            $categorias = Categoria::where('flag_activo', 1)
                ->orderBy('nombre')
                ->get();

            // Se formatea la fecha y se recuperan los nombres de las categorias
            foreach ($data as $d) {
                $fechaAltaParseObject = date_parse($d->fecha_alta);
                if ($fechaAltaParseObject['month'] >= 1 && $fechaAltaParseObject['month'] <= 9) {
                    if ($fechaAltaParseObject['day'] >= 1 && $fechaAltaParseObject['day'] <= 9) {
                        $d->fecha_alta = "0{$fechaAltaParseObject['day']}-0{$fechaAltaParseObject['month']}-{$fechaAltaParseObject['year']}";
                    } else {
                        $d->fecha_alta = "{$fechaAltaParseObject['day']}-0{$fechaAltaParseObject['month']}-{$fechaAltaParseObject['year']}";
                    }
                } else {
                    if ($fechaAltaParseObject['day'] >= 1 && $fechaAltaParseObject['day'] <= 9) {
                        $d->fecha_alta = "0{$fechaAltaParseObject['day']}-{$fechaAltaParseObject['month']}-{$fechaAltaParseObject['year']}";
                    } else {
                        $d->fecha_alta = "{$fechaAltaParseObject['day']}-{$fechaAltaParseObject['month']}-{$fechaAltaParseObject['year']}";
                    }
                }

                $idCategoria = $d->id_categoria;

                $filteredArray = $categorias->filter(function ($c) use ($idCategoria) {
                    return $c->id_categoria == $idCategoria;
                });

                foreach ($filteredArray as $key => $value) {
                    $d->nombre_categoria = $value->nombre;
                }
            }

            // Se guarda la data en cache por 60 minutos
            Cache::put('DATA_MULTINOTAS', $data, 60 * 60);
            Cache::put('CATEGORIAS', $categorias, 60 * 60);
            Cache::put('LAST_UPDATE_MULTINOTAS', now(), 60 * 60);
        } else {
            // Se guarda la data desde la cache
            $data = Cache::get('DATA_MULTINOTAS');
            $categorias = Cache::get('CATEGORIAS');
        }

        if(($request->has('nombre') && !empty($request->nombre))
        || $request->has('categoria') && !empty($request->categoria)
        || $request->has('publicas') && !empty($request->publicas)
        || $request->has('mensajeInicial') && !empty($request->mensajeInicial)
        || $request->has('expediente') && !empty($request->expediente)) {
            $dataFiltrada = Cache::get('DATA_MULTINOTAS');

            if($request->has('nombre') && !empty($request->nombre)) {
                $nombre = Str::lower($request->nombre);

                $dataFiltrada = $dataFiltrada->filter(function ($d) use ($nombre) {
                    return str_contains(Str::lower($d->nombre), $nombre);
                });
            }

            if($request->has('categoria') && !empty($request->categoria)) {
                $idCategoria = intval($request->categoria);

                $dataFiltrada = $dataFiltrada->filter(function ($d) use ($idCategoria) {
                    return str_contains(Str::lower($d->id_categoria), $idCategoria);
                });
            }

            if($request->publicas != 'Todas') {
                if($request->publicas == 'Públicas') {
                    $dataFiltrada = $dataFiltrada->filter(function ($d) {
                        return $d->publico == 1;
                    });
                } else {
                    $dataFiltrada = $dataFiltrada->filter(function ($d) {
                        return $d->publico == 0;
                    });
                }
            }

            if($request->mensajeInicial != 'Todas') {
                if($request->mensajeInicial == 'Muestran mensaje inicial') {
                    $dataFiltrada = $dataFiltrada->filter(function ($d) {
                        return $d->muestra_mensaje == 1;
                    });
                } else {
                    $dataFiltrada = $dataFiltrada->filter(function ($d) {
                        return $d->muestra_mensaje == 0;
                    });
                }
            }

            if($request->expediente != 'Todas') {
                if($request->expediente == 'Llevan expediente') {
                    $dataFiltrada = $dataFiltrada->filter(function ($d) {
                        return $d->lleva_expediente == 1;
                    });
                } else {
                    $dataFiltrada = $dataFiltrada->filter(function ($d) {
                        return $d->lleva_expediente == 0;
                    });
                }
            }
        }

        if($dataFiltrada != null) {
            return DataTables::of($dataFiltrada)
                    ->addIndexColumn()
                    ->make(true);
        }
        
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        $categorias = Cache::get('CATEGORIAS');
        
        return view('multinotas.index', compact('categorias'));
    }

    public function view($id) {
        $data = Cache::get('DATA_MULTINOTAS');
        if($data == null) {
            return redirect()->route('multinotas.index');
        }

        $array = MultinotaController::buildMultinotaSelected($id, $data);

        $multinotaSelected = $array[0];
        $seccionesAsociadas = $array[1];

        return view('multinotas.view', compact('multinotaSelected', 'seccionesAsociadas'));
    }

    public function edit($id) {
        $data = Cache::get('DATA_MULTINOTAS');
        if($data == null) {
            return redirect()->route('multinotas.index');
        }

        $array = MultinotaController::buildMultinotaSelected($id, $data);

        $multinotaSelected = $array[0];
        $seccionesAsociadas = $array[1];
        $todasLasSecciones = $array[2];
        $categoriasPadre = $array[3];

        $subcategorias = MultinotaController::getSubcategoriasPorIdPadre($multinotaSelected->id_categoria_padre);
        Session::put('SUBCATEGORIAS', $subcategorias);
        $codigos = MultinotaController::getCodigosMultinotasActivas();
        Session::put('IS_EDITAR', true);
        $isEditar = Session::get('IS_EDITAR');

        return view('multinotas.edit', compact('multinotaSelected', 'seccionesAsociadas', 'todasLasSecciones', 'subcategorias', 'categoriasPadre', 'codigos', 'isEditar'));
    }

    public function crearNuevaMultinota() {
        $multinotaSelected = new MultinotaController();
        $multinotaSelected->id_tipo_tramite_multinota = 0; //ID dummy
        $multinotaSelected->nombre_categoria_padre = null;
        $multinotaSelected->id_categoria = null;
        $multinotaSelected->codigo = null;
        $multinotaSelected->nombre = null;
        $multinotaSelected->publico = 1;
        $multinotaSelected->nivel = 1;
        $multinotaSelected->lleva_documentacion = 0;
        $multinotaSelected->muestra_mensaje = 1;
        $multinotaSelected->lleva_expediente = 0;
        $multinotaSelected->nombre_subcategoria = null;
        $multinotaSelected->mensaje_inicial = '';

        $seccionesAsociadas = [];
        $todasLasSecciones = MultinotaController::getTodasLasSecciones();
        Session::put('TODAS_LAS_SECCIONES', $todasLasSecciones);
        $subcategorias = [];
        $categoriasPadre = MultinotaController::getCategoriasPadre();
        Session::put('CATEGORIAS_PADRE', $categoriasPadre);
        $codigos = MultinotaController::getCodigosMultinotasActivas();

        //Se setean los datos de sesion en 'nulos'
        Session::put('MULTINOTA_SELECTED', $multinotaSelected);
        Session::put('SECCIONES_ASOCIADAS', []);
        Session::put('SUBCATEGORIAS', []);
        Session::put('IS_EDITAR', false);
        $isEditar = Session::get('IS_EDITAR');

        return view('multinotas.edit', compact('multinotaSelected', 'seccionesAsociadas', 'todasLasSecciones', 'subcategorias', 'categoriasPadre', 'codigos', 'isEditar'));
    }

    private static function buildMultinotaSelected($id, $data) {
        $categorias = Cache::get('CATEGORIAS');
        $multinotaSelected = null;

        foreach ($data as $d) {
            if($d->id_tipo_tramite_multinota == $id) {
                $multinotaSelected = $d;
                Session::put('MULTINOTA_SELECTED', $multinotaSelected);
            }
        }

        //Se recuperan nombres de categoría y subcategoría de la multinota
        foreach ($categorias as $c) {
            if($c->id_categoria == $multinotaSelected->id_categoria) {
                $multinotaSelected->nombre_subcategoria = $c->nombre;
                Session::put('MULTINOTA_SELECTED', $multinotaSelected);

                if($c->id_padre != null) {
                    $categoriaPadre = Categoria::where('id_categoria', $c->id_padre)
                    ->get();

                    if($categoriaPadre[0] != null) {
                        $multinotaSelected->nombre_categoria_padre = $categoriaPadre[0]->nombre;
                        $multinotaSelected->id_categoria_padre = $categoriaPadre[0]->id_categoria;
                        Session::put('MULTINOTA_SELECTED', $multinotaSelected);
                    }
                }
            }
        }

        //Se recuperan las categorias padre
        $categoriasPadre = MultinotaController::getCategoriasPadre();

        Session::put('CATEGORIAS_PADRE', $categoriasPadre);

        //Se recupera el mensaje inicial de la multinota
        $result = MensajeInicial::join('tipo_tramite_mensaje_inicial as ttmi', 'mensaje_inicial.id_mensaje_inicial', '=', 'ttmi.id_mensaje_inicial')
        ->where('ttmi.id_tipo_tramite_multinota', $id)
        ->orderBy('mensaje_inicial.id_mensaje_inicial', 'desc')
        ->limit(1)
        ->select('mensaje_inicial.*')
        ->first();

        $multinotaSelected->mensaje_inicial = $result->mensaje_inicial;
        Session::put('MULTINOTA_SELECTED', $multinotaSelected);

        //Se recuperan las secciones de la multinota
        $secciones = SeccionMultinota::join('multinota_seccion as ms', 'seccion.id_seccion', '=', 'ms.id_seccion')
        ->where('ms.id_tipo_tramite_multinota', $id)
        ->orderBy('ms.orden')
        ->select('seccion.*', 'ms.orden')
        ->get();

        // Array de secciones que se enviaran a la vista
        $seccionesAsociadas = [];

        foreach ($secciones as $s) {
            $campos = Campo::where('id_seccion', $s->id_seccion)
            ->orderBy('orden')
            ->get();

            $seccion = new \stdClass();
            $seccion->id_seccion = $s->id_seccion;
            $seccion->temporal = $s->temporal;
            $seccion->titulo = $s->titulo;
            $seccion->campos = $campos;
            $seccion->orden = $s->orden;

            $seccionesAsociadas[] = $seccion;
        }

        Session::put('SECCIONES_ASOCIADAS', $seccionesAsociadas);

        $todasLasSecciones = MultinotaController::getTodasLasSecciones();

        Session::put('TODAS_LAS_SECCIONES', $todasLasSecciones);

        return array($multinotaSelected, $seccionesAsociadas, $todasLasSecciones, $categoriasPadre);
    }

    public static function getCodigosMultinotasActivas() {
        $codigos = TipoTramiteMultinota::where('baja_logica', 0)->pluck('codigo');

        return $codigos;
    }

    public static function getCategoriasPadre() {
        $categoriasPadre = Categoria::from('categoria as c1')
        ->join('categoria as c2', 'c1.id_padre', '=', 'c2.id_categoria')
        ->whereNotNull('c1.id_padre')
        ->where('c1.flag_activo', 1)
        ->select('c1.id_padre', 'c2.nombre')
        ->distinct()
        ->get();

        return $categoriasPadre;
    }

    public static function getTodasLasSecciones() {
        //Se recuperan todas las secciones multinota
        $secciones = SeccionMultinota::select('seccion.*')
            ->where('seccion.activo', true)
            ->where('seccion.temporal', false)
            ->get();

        // Array de todas las secciones que se enviaran a la vista
        $todasLasSecciones = [];

        foreach ($secciones as $s) {
            $campos = Campo::where('id_seccion', $s->id_seccion)
            ->orderBy('orden')
            ->get();

            $seccion = new \stdClass();
            $seccion->id_seccion = $s->id_seccion;
            $seccion->temporal = $s->temporal;
            $seccion->titulo = $s->titulo;
            $seccion->campos = $campos;

            $todasLasSecciones[] = $seccion;
        }

        return $todasLasSecciones;
    }

    public function refresh() {
        $seccionesAsociadas = Session::get('SECCIONES_ASOCIADAS');

        return response()->json([
            'updatedSecciones' => $seccionesAsociadas,
        ]);
    }

    public function agregarSeccion(Request $request) {
        $id = $request->post('id');
        $seccionesAsociadas = Session::get('SECCIONES_ASOCIADAS');
        $todasLasSecciones = Session::get('TODAS_LAS_SECCIONES');

        $campos = Campo::where('id_seccion', $id)
            ->orderBy('orden')
            ->get();

        $s = SeccionMultinota::where('id_seccion', $id)
            ->get();

        if(count($seccionesAsociadas) == 0) {
            $maxOrden = -1;
        } else {
            $maxOrden = max(array_column($seccionesAsociadas, 'orden'));
        }

        if ($s) {
            $seccion = new \stdClass();
            $seccion->id_seccion = $s[0]->id_seccion;
            $seccion->temporal = $s[0]->temporal;
            $seccion->titulo = $s[0]->titulo;
            $seccion->campos = $campos;
            $seccion->orden = $maxOrden + 1;

            $seccionesAsociadas[] = $seccion; 

            Session::put('SECCIONES_ASOCIADAS', $seccionesAsociadas);
        }

        // Render the partial view with updated data
        $html = view('partials.secciones-container', compact('seccionesAsociadas', 'todasLasSecciones'))->render();

        // Return JSON response with rendered HTML
        return response()->json(['html' => $html]);
    }

    public function quitarSeccion(Request $request) {
        $id = $request->post('id');
        $seccionesAsociadas = Session::get('SECCIONES_ASOCIADAS');
        $todasLasSecciones = Session::get('TODAS_LAS_SECCIONES');

        // Quitar seccion
        $nuevasSeccionesAsociadas = array_filter($seccionesAsociadas, function ($s) use ($id) {
            return $s->id_seccion !== $id; // Se quita elemento con id_seccion seleccionado
        });

        // Se reindexa array
        $nuevasSeccionesAsociadas = array_values($nuevasSeccionesAsociadas);

        // Se recorre array para setear orden tras reindexacion
        foreach ($nuevasSeccionesAsociadas as $index => $s) {
            $s->orden = $index; // Se setea orden desde el 0
        }

        Session::put('SECCIONES_ASOCIADAS', $nuevasSeccionesAsociadas);

        $seccionesAsociadas = $nuevasSeccionesAsociadas;

        // Agregar seccion a 'todasLasSecciones' (disponibles)
        $campos = Campo::where('id_seccion', $id)
            ->orderBy('orden')
            ->get();

        $s = SeccionMultinota::where('id_seccion', $id)
            ->get();

        if ($s) {
            $seccion = new \stdClass();
            $seccion->id_seccion = $s[0]->id_seccion;
            $seccion->temporal = $s[0]->temporal;
            $seccion->titulo = $s[0]->titulo;
            $seccion->campos = $campos;

            $todasLasSecciones[] = $seccion; 

            Session::put('TODAS_LAS_SECCIONES', $todasLasSecciones);
        }

        // Render the partial view with updated data
        $html = view('partials.secciones-container', compact('seccionesAsociadas', 'todasLasSecciones'))->render();

        // Return JSON response with rendered HTML
        return response()->json(['html' => $html]);
    }

    public function setearNuevoOrdenSeccion($array) {
        $seccionesAsociadas = Session::get('SECCIONES_ASOCIADAS');

        // Se guardan los IDs de las secciones multinota, parseados y en orden en un array
        $arrayIdsSeccionOrdenados = explode(',', $array);

        foreach ($seccionesAsociadas as $index => $s) {
            if(count(Session::get('SECCIONES_ASOCIADAS')[$index]->campos) == 0) {
                array_splice($arrayIdsSeccionOrdenados, $index, 0, $s->id_seccion);
            }
        }

        // Se reubican las secciones
        $seccionesOrdenadas = [];
        foreach ($arrayIdsSeccionOrdenados as $id) {
            $seccionesOrdenadas[] = current(array_filter($seccionesAsociadas, fn($s) => $s->id_seccion == (int) $id));
        }

        // Se acomoda el atributo 'orden'
        foreach ($seccionesOrdenadas as $index => $s) {
            $s->orden = $index;
        }

        // Se setean las secciones en sesion
        Session::put('SECCIONES_ASOCIADAS', $seccionesOrdenadas);
    }

    public static function getSubcategoriasPorIdPadre($idCategoria) {
        $subcategorias = Categoria::where('flag_activo', 1)
        ->where('id_padre', (int) $idCategoria)
        ->orderBy('nombre')
        ->get();

        return $subcategorias;
    }

    public function recargarSubcategorias($idCategoria) {
        $multinotaSelected = Session::get('MULTINOTA_SELECTED');
        $isEditar = Session::get('IS_EDITAR');


        if((int) $idCategoria == 0) {
            $subcategorias = [];
        } else {
            $subcategorias = MultinotaController::getSubcategoriasPorIdPadre($idCategoria);
        }

        //Se guardan las nuevas subcategorias asociadas a la categoria padre seleccionada
        Session::put('SUBCATEGORIAS', $subcategorias);

        // Se renderiza el partial de las subcategorias con los datos actualizados
        $html = view('partials.select-subcategorias', compact('multinotaSelected', 'subcategorias', 'isEditar'))->render();

        // Se retorna el JSON con el nuevo HTML
        return response()->json(['html' => $html]);
    }

    public function previsualizarCambiosMultinota(Request $request) {
        $multinotaSelected = Session::get('MULTINOTA_SELECTED');
        $seccionesAsociadas = Session::get('SECCIONES_ASOCIADAS');
        $categorias = Cache::get('CATEGORIAS');
        $isEditar = Session::get('IS_EDITAR');

        // Se actualiza multinotaSelected

        // Codigo
        if(!$isEditar) {
            $multinotaSelected->codigo = $request->post('codigo');
        }

        // Nombre
        $multinotaSelected->nombre = $request->post('nombre');

        // Categoria y Subcategoria
        foreach ($categorias as $c) {
            if($c->id_categoria == $request->post('categoria')) {
                $multinotaSelected->nombre_categoria_padre = $c->nombre;
                Session::put('MULTINOTA_SELECTED', $multinotaSelected);
            }

            if($c->id_categoria == $request->post('subcategoria')) {
                $multinotaSelected->nombre_subcategoria = $c->nombre;
                $multinotaSelected->id_categoria = $c->id_categoria;
                Session::put('MULTINOTA_SELECTED', $multinotaSelected);
            }
        }

        // Público
        if($request->post('publico') == 'on') {
            $multinotaSelected->publico = 1;
        } else {
            $multinotaSelected->publico = 0;
        }

        // Lleva documentación
        if($request->post('llevaDocumentacion') == 'on') {
            $multinotaSelected->lleva_documentacion = 1;
        } else {
            $multinotaSelected->lleva_documentacion = 0;
        }

        // Muestra mensaje inicial
        if($request->post('muestraMensaje') == 'on') {
            $multinotaSelected->muestra_mensaje = 1;
        } else {
            $multinotaSelected->muestra_mensaje = 0;
        }

        // Mensaje inicial
        $multinotaSelected->mensaje_inicial = $request->post('mensajeInicial');

        // Se renderiza el partial con el detalle, con los datos actualizados
        $html = view('partials.detalle-multinota', compact('seccionesAsociadas', 'multinotaSelected'))->render();

        // Se retorna el JSON con el nuevo HTML
        return response()->json(['html' => $html]);
    }

    public function guardarMultinota($id) {
        $multinotaSelected = Session::get('MULTINOTA_SELECTED');
        $seccionesAsociadas = Session::get('SECCIONES_ASOCIADAS');
        $categorias = Cache::get('CATEGORIAS');

        // A la multinota obsoleta se le asigna "baja_logica" = 1
        TipoTramiteMultinota::where('id_tipo_tramite_multinota', (int) $id)->update(['baja_logica' => 1]);

        // Mensaje inicial se guarda en mensaje_inicial
        MensajeInicial::create(['mensaje_inicial' => $multinotaSelected->mensaje_inicial]);

        // Los demas datos de la multinota se guardan en tipo_tramite_multinota 
        TipoTramiteMultinota::create([
            'nombre' => $multinotaSelected->nombre,
            'codigo' => $multinotaSelected->codigo,
            'id_categoria' => $multinotaSelected->id_categoria,
            'publico' => $multinotaSelected->publico,
            'nivel' => $multinotaSelected->nivel,
            'muestra_mensaje' => $multinotaSelected->muestra_mensaje,
            'lleva_expediente' => $multinotaSelected->lleva_expediente,
            'baja_logica' => 0,
            'lleva_documentacion' => $multinotaSelected->lleva_documentacion,
        ]);

        // La recupero para saber el ID de la nueva multinota
        $res = TipoTramiteMultinota::select('id_tipo_tramite_multinota')
        ->where('baja_logica', 0)
        ->where('nombre', $multinotaSelected->nombre)
        ->get();

        $nuevoId = $res[0]->id_tipo_tramite_multinota;

        // Recupero ID de mensaje inicial previamente insertado
        $maxIdMensajeInicial = MensajeInicial::max('id_mensaje_inicial');

        // Relacion mensaje inicial / multinota se guarda en tipo_tramite_mensaje_inicial
        TipoTramiteMensajeInicial::create([
            'id_tipo_tramite_multinota' => $nuevoId,
            'id_mensaje_inicial' => $maxIdMensajeInicial,
        ]);

        // La relacion entre la multinota y sus secciones se guarda en multinota_seccion, asi como el orden
        
        // Se recorre el array de secciones para ir insertando una por una en base
        foreach ($seccionesAsociadas as $s) {
            MultinotaSeccion::create([
                'id_tipo_tramite_multinota' => $nuevoId,
                'id_seccion' => $s->id_seccion,
                'orden' => $s->orden,
            ]);
        }

        // Se inserta un registro en la tabla multinota_tipo_cuenta con valor '-'
        MultinotaTipoCuenta::create([
            'id_tipo_tramite_multinota' => $nuevoId,
            'cod_tipo_cuenta' => '-',
        ]);

        return view('multinotas.index', compact('categorias'));
    }

    public function desactivarMultinota($id) {
        $multinota = TipoTramiteMultinota::find((int) $id);
        if ($multinota) {
            $multinota->baja_logica = 1; 
            $multinota->fecha_ultima_actualizacion = Carbon::now();
            $multinota->save();
            return response()->json(['message' => 'Multinota desactivada correctamente.'], 200);
        } else {
            return response()->json(['message' => 'Multinota no encontrada.'], 404);
        }
    }
}