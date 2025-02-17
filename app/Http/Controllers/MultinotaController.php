<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\TipoTramiteMultinota;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;

class MultinotaController extends Controller
{
    public function index(Request $request) {
        $dataFiltrada = null;

        $data = TipoTramiteMultinota::distinct()
            ->where('baja_logica', 0)
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::where('flag_activo', 1)
            ->orderBy('nombre')
            ->get();

        foreach ($data as $d) {
            //Se parsea la fecha de alta
            $fechaAltaParseObject = date_parse($d->fecha_alta);
            if($fechaAltaParseObject['month'] >= 1 && $fechaAltaParseObject['month'] <= 9) {
                if($fechaAltaParseObject['day'] >= 1 && $fechaAltaParseObject['day'] <= 9) {
                    $d->fecha_alta = "0{$fechaAltaParseObject['day']}-0{$fechaAltaParseObject['month']}-{$fechaAltaParseObject['year']}";
                } else {
                    $d->fecha_alta = "{$fechaAltaParseObject['day']}-0{$fechaAltaParseObject['month']}-{$fechaAltaParseObject['year']}";
                }
            } else {
                if($fechaAltaParseObject['day'] >= 1 && $fechaAltaParseObject['day'] <= 9) {
                    $d->fecha_alta = "0{$fechaAltaParseObject['day']}-{$fechaAltaParseObject['month']}-{$fechaAltaParseObject['year']}";
                } else {
                    $d->fecha_alta = "{$fechaAltaParseObject['day']}-{$fechaAltaParseObject['month']}-{$fechaAltaParseObject['year']}";
                }
            }

            //Obtener la categoria con el ID
            $idCategoria = $d->id_categoria;

            $filteredArray = $categorias->filter(function ($c) use ($idCategoria) {
                // Your filtering logic here
                return $c->id_categoria == $idCategoria;
            });

            foreach ($filteredArray as $key => $value) {
                $d->nombre_categoria = $value->nombre;
            }
        }

        if(($request->has('nombre') && !empty($request->nombre))
        || $request->has('categoria') && !empty($request->categoria)
        || $request->has('publicas') && !empty($request->publicas)
        || $request->has('mensajeInicial') && !empty($request->mensajeInicial)
        || $request->has('expediente') && !empty($request->expediente)) {
            $dataFiltrada = $data;

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
        
        return view('multinotas.index', compact('categorias'));
    }
}