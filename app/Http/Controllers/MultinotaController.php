<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\TipoTramiteMultinota;
use App\Models\Categoria;
use App\Models\MensajeInicial;
use App\Models\TipoTramiteMensajeInicial;
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
            $latestMultinotaUpdate = TipoTramiteMultinota::max('fecha_ultima_actualizacion');
            // Se convierte la fecha de ultima actualización de base a una instancia de la librería Carbon
            $latestMultinotaUpdateUTC = Carbon::createFromFormat('Y-m-d H:i:s', $latestMultinotaUpdate, 'UTC');

            // Se convierte la instancia a GMT-3
            $latestMultinotaUpdateHoraLocal = $latestMultinotaUpdateUTC->setTimezone('America/Sao_Paulo');

            if ($latestMultinotaUpdateHoraLocal > $lastUpdate) {
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
                        Session::put('MULTINOTA_SELECTED', $multinotaSelected);
                    }
                }
            }
        }

        //Se recupera el mensaje inicial de la multinota
        if(Session::get('MULTINOTA_SELECTED')->muestra_mensaje == 1) {
            $result = MensajeInicial::join('tipo_tramite_mensaje_inicial as ttmi', 'mensaje_inicial.id_mensaje_inicial', '=', 'ttmi.id_mensaje_inicial')
            ->where('ttmi.id_tipo_tramite_multinota', $id)
            ->orderBy('mensaje_inicial.id_mensaje_inicial', 'desc')
            ->limit(1)
            ->select('mensaje_inicial.*')
            ->first();

            $multinotaSelected->mensaje_inicial = $result->mensaje_inicial;
            Session::put('MULTINOTA_SELECTED', $multinotaSelected);
        }

        //Se recuperan las secciones de la multinota
        $secciones = SeccionMultinota::join('multinota_seccion as ms', 'seccion.id_seccion', '=', 'ms.id_seccion')
        ->where('ms.id_tipo_tramite_multinota', $id)
        ->orderBy('ms.orden')
        ->select('seccion.*')
        ->get();

        // Array de secciones que se enviaran a la vista
        $seccionesMultinota = [];

        foreach ($secciones as $s) {
            $campos = Campo::where('id_seccion', $s->id_seccion)
            ->orderBy('orden')
            ->get();

            $seccion = new \stdClass();
            $seccion->id_seccion = $s->id_seccion;
            $seccion->temporal = $s->temporal;
            $seccion->titulo = $s->titulo;
            $seccion->campos = $campos;

            $seccionesMultinota[] = $seccion;
        }

        return view('multinotas.view', compact('multinotaSelected', 'seccionesMultinota'));
    }

    public function edit($id) {
        return view('multinotas.edit');
    }
}