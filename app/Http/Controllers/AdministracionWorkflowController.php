<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoTramiteMultinota;
use App\Models\Categoria;
use App\Models\GrupoInterno;
use App\Models\UsuarioInterno;
use DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdministracionWorkflowController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TipoTramiteMultinota::join('categoria', 'tipo_tramite_multinota.id_categoria', '=', 'categoria.id_categoria')
                ->leftJoin('configuracion_estado_tramite', 'tipo_tramite_multinota.id_tipo_tramite_multinota', '=', 'configuracion_estado_tramite.id_tipo_tramite_multinota')
                ->where('tipo_tramite_multinota.baja_logica', 0) 
                ->select([
                        'tipo_tramite_multinota.id_tipo_tramite_multinota',
                        'categoria.nombre as categoria',
                        'tipo_tramite_multinota.nombre as nombre_tipo_tramite',
                        \DB::raw('IF(configuracion_estado_tramite.id_tipo_tramite_multinota IS NULL, 0, 1) as existe_configuracion'),
                        \DB::raw("IF(EXISTS (
                                SELECT 1 FROM configuracion_estado_tramite cet
                                WHERE cet.id_tipo_tramite_multinota = tipo_tramite_multinota.id_tipo_tramite_multinota
                                AND cet.publico = 0
                                ), 1, 0) as existe_borrador")
                        ])
                ->groupBy('tipo_tramite_multinota.id_tipo_tramite_multinota', 'categoria.nombre', 'tipo_tramite_multinota.nombre');

           return DataTables::of($data)
                    ->addIndexColumn()
                    ->filterColumn('categoria', function($query, $keyword) {
                    $query->whereRaw('LOWER(categoria.nombre) LIKE ?', ["%" . strtolower($keyword) . "%"]);
                })
                    ->filterColumn('nombre_tipo_tramite', function($query, $keyword) {
                    $query->whereRaw('LOWER(tipo_tramite_multinota.nombre) LIKE ?', ["%" . strtolower($keyword) . "%"]);
            })
                    ->make(true);

        }

        return view('estados.index');
    }
    

    public function crear($id)
    {
        $tipoTramite = TipoTramiteMultinota::findOrFail($id);

        // Estados por defecto
        $estados = [
            ['actual' => 'En Creación', 'nuevo' => 'En Creación'],
            ['actual' => 'Iniciado', 'nuevo' => 'Iniciado'],
            ['actual' => 'En Análisis', 'nuevo' => 'En Análisis'],
            ['actual' => 'En Aprobación', 'nuevo' => 'En Aprobación'],
            ['actual' => 'A Finalizar', 'nuevo' => 'A Finalizar'],
        ];


        $grupos = GrupoInterno::with('usuarios')->get();

        return view('estados.crear', compact('tipoTramite', 'estados', 'grupos'));

    }


    public function editar($id)
{
    $tipoTramite = TipoTramiteMultinota::findOrFail($id);

    $estadosDB = DB::table('estado_tramite as et')
        ->join('configuracion_estado_tramite as cet', 'et.id_estado_tramite', '=', 'cet.id_estado_tramite')
        ->where('cet.id_tipo_tramite_multinota', $id)
        ->where('cet.activo', 1)
        ->select('et.id_estado_tramite', 'et.nombre', 'et.tipo', 'et.puede_rechazar', 'et.puede_pedir_documentacion', 'et.estado_tiene_expediente')
        ->distinct()
        ->get();

    $posteriores = DB::table('configuracion_estado_tramite as cet')
        ->join('estado_tramite as et', 'cet.id_proximo_estado', '=', 'et.id_estado_tramite')
        ->where('cet.id_tipo_tramite_multinota', $id)
        ->whereNotNull('cet.id_proximo_estado')
        ->select('cet.id_estado_tramite', 'et.nombre as nombre_posterior')
        ->get()
        ->groupBy('id_estado_tramite');

    $asignaciones = DB::table('estado_tramite_asignable')
        ->whereIn('id_estado_tramite', $estadosDB->pluck('id_estado_tramite'))
        ->get()
        ->groupBy('id_estado_tramite');

    $estados = $estadosDB->map(function ($estado) use ($posteriores, $asignaciones) {
        return [
           'estado_actual' => (string) $estado->nombre,
            'posteriores' => isset($posteriores[$estado->id_estado_tramite])
            ? $posteriores[$estado->id_estado_tramite]->map(function ($p) {
            return ['nombre' => $p->nombre_posterior];
            })->values()->toArray()
            : [],
            'puede_rechazar' => $estado->puede_rechazar,
            'puede_pedir_documentacion' => $estado->puede_pedir_documentacion,
            'estado_tiene_expediente' => $estado->estado_tiene_expediente,
            'asignaciones' => isset($asignaciones[$estado->id_estado_tramite])
            ? collect($asignaciones[$estado->id_estado_tramite])->map(function ($asig) {
            return [
                'id_grupo_interno' => $asig->id_grupo_interno,
                'id_usuario_interno' => $asig->id_usuario_interno,
            ];
            })->values()->toArray()
            : [],

        ];
    });

    $grupos = GrupoInterno::with('usuarios')->get();

    return view('estados.editar', compact('tipoTramite', 'estados', 'grupos'));
}

public function borrador($id)
{
    $tipoTramite = TipoTramiteMultinota::findOrFail($id);

    $estadosDB = DB::table('estado_tramite as et')
        ->join('configuracion_estado_tramite as cet', 'et.id_estado_tramite', '=', 'cet.id_estado_tramite')
        ->where('cet.id_tipo_tramite_multinota', $id)
        ->where('cet.activo', 0)
        ->where('cet.publico', 0)
        ->select('et.id_estado_tramite', 'et.nombre', 'et.tipo', 'et.puede_rechazar', 'et.puede_pedir_documentacion', 'et.estado_tiene_expediente')
        ->distinct()
        ->get();

    $posteriores = DB::table('configuracion_estado_tramite as cet')
        ->join('estado_tramite as et', 'cet.id_proximo_estado', '=', 'et.id_estado_tramite')
        ->where('cet.id_tipo_tramite_multinota', $id)
        ->where('cet.publico', 0)
        ->whereNotNull('cet.id_proximo_estado')
        ->select('cet.id_estado_tramite', 'et.nombre as nombre_posterior')
        ->get()
        ->groupBy('id_estado_tramite');

    $asignaciones = DB::table('estado_tramite_asignable')
        ->whereIn('id_estado_tramite', $estadosDB->pluck('id_estado_tramite'))
        ->get()
        ->groupBy('id_estado_tramite');

    $estados = $estadosDB->map(function ($estado) use ($posteriores, $asignaciones) {
        return [
            'estado_actual' => (string) $estado->nombre,
            'posteriores' => isset($posteriores[$estado->id_estado_tramite])
                ? $posteriores[$estado->id_estado_tramite]->map(function ($p) {
                    return ['nombre' => $p->nombre_posterior];
                })->values()->toArray()
                : [],
            'puede_rechazar' => $estado->puede_rechazar,
            'puede_pedir_documentacion' => $estado->puede_pedir_documentacion,
            'estado_tiene_expediente' => $estado->estado_tiene_expediente,
            'asignaciones' => isset($asignaciones[$estado->id_estado_tramite])
            ? $asignaciones[$estado->id_estado_tramite]->map(function ($asig) {
                return [
                    'id_grupo_interno' => $asig->id_grupo_interno,
                    'id_usuario_interno' => $asig->id_usuario_interno,
                ];
            })->values()->toArray()
            : [],
    ];
});

    $grupos = GrupoInterno::with('usuarios')->get();

    return view('estados.borrador', compact('tipoTramite', 'estados', 'grupos'));
}


    

public function guardar(Request $request, $id)
{
    $configuraciones = $request->input('configuraciones');

    if (!$configuraciones || !is_array($configuraciones)) {
        return response()->json(['success' => false, 'message' => 'Datos inválidos']);
    }

    DB::beginTransaction();
    try {
        $definiciones = collect();

        foreach ($configuraciones as $conf) {
            $definiciones->put($conf['estado_actual'], $conf);

            foreach ($conf['posteriores'] as $post) {
                $definiciones->put($post['nombre'], $post + ['tipo' => null]);
            }
        }

        $mapaEstados = [];
        foreach ($definiciones as $nombre => $confEstado) {
            $tipo = $confEstado['tipo'] ?? strtoupper(str_replace(' ', '_', $nombre));

            $idNuevo = DB::table('estado_tramite')->insertGetId([
                'fecha_sistema' => now(),
                'nombre' => $nombre,
                'tipo' => $tipo,
                'puede_rechazar' => $confEstado['puede_rechazar'] ?? 0,
                'puede_pedir_documentacion' => $confEstado['puede_pedir_documentacion'] ?? 0,
                'puede_elegir_camino' => 0,
                'estado_tiene_expediente' => $confEstado['estado_tiene_expediente'] ?? 0,
            ]);

            $mapaEstados[$nombre] = $idNuevo;
        }

        $version = uniqid();
        $now = now();

        foreach ($configuraciones as $conf) {
            $idEstadoActual = $mapaEstados[$conf['estado_actual']];

            $posteriores = $conf['posteriores'];
            if (empty($posteriores)) {
                DB::table('configuracion_estado_tramite')->insert([
                    'fecha_sistema' => $now,
                    'id_estado_tramite' => $idEstadoActual,
                    'id_proximo_estado' => null,
                    'version' => $version,
                    'publico' => 1,
                    'id_tipo_tramite_multinota' => $id,
                    'activo' => 1
                ]);
            } else {
                $transiciones = array_map(function ($post) use ($idEstadoActual, $mapaEstados, $version, $id, $now) {
                    return [
                        'fecha_sistema' => $now,
                        'id_estado_tramite' => $idEstadoActual,
                        'id_proximo_estado' => $mapaEstados[$post['nombre']],
                        'version' => $version,
                        'publico' => 1,
                        'id_tipo_tramite_multinota' => $id,
                        'activo' => 1
                    ];
                }, $posteriores);

                DB::table('configuracion_estado_tramite')->insert($transiciones);
            }
        }

        foreach ($configuraciones as $conf) {
            $idEstado = $mapaEstados[$conf['estado_actual']] ?? null;
            if (!$idEstado || empty($conf['asignaciones'])) continue;

            $asignaciones = array_map(function ($asig) use ($idEstado) {
            return [
                'fecha_sistema' => now(),
                'id_estado_tramite' => $idEstado,
                'id_grupo_interno' => $asig['id_grupo_interno'] ?? null,
                'id_usuario_interno' => $asig['id_usuario_interno'] ?? null,
            ];
        }, $conf['asignaciones']);


        DB::table('estado_tramite_asignable')->insert($asignaciones);
    }

        $this->actualizarTramitesActivos($id, $mapaEstados);

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Workflow guardado correctamente.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
    }
}


public function guardarEdicion(Request $request, $id)
{
    $configuraciones = $request->input('configuraciones');

    if (!$configuraciones || !is_array($configuraciones)) {
        return response()->json(['success' => false, 'message' => 'Datos inválidos: configuraciones no es un array']);
    }

    DB::beginTransaction();
    try {
        $versionesExistentes = DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $id)
            ->select('version', 'publico', DB::raw('MAX(fecha_sistema) as fecha_maxima'))
            ->groupBy('version', 'publico')
            ->orderBy('fecha_maxima', 'desc')
            ->get();

            $versionesPublicas = $versionesExistentes->where('publico', 1)->values();
            $borradores = $versionesExistentes->where('publico', 0)->values();

        DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $id)
            ->where('activo', 1)
            ->update(['activo' => 0]);

        $mapaEstados = [];
        $version = uniqid();
        $now = now();

        foreach ($configuraciones as $index => $conf) {
            if (!is_array($conf)) {
                throw new \Exception("Configuración en índice $index no es un array");
            }

            if (!isset($conf['estado_actual'])) {
                throw new \Exception("Falta 'estado_actual' en configuración $index");
            }

            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            
            if (!isset($mapaEstados[$nombreEstado])) {
                $idEstado = DB::table('estado_tramite')->insertGetId([
                    'fecha_sistema' => $now,
                    'nombre' => $nombreEstado,
                    'tipo' => strtoupper(str_replace(' ', '_', $nombreEstado)),
                    'puede_rechazar' => $this->parseBool($conf['puede_rechazar'] ?? 0),
                    'puede_pedir_documentacion' => $this->parseBool($conf['puede_pedir_documentacion'] ?? 0),
                    'puede_elegir_camino' => 0,
                    'estado_tiene_expediente' => $this->parseBool($conf['estado_tiene_expediente'] ?? 0),
                ]);
                $mapaEstados[$nombreEstado] = $idEstado;
            }

            if (isset($conf['posteriores']) && is_array($conf['posteriores'])) {
                foreach ($conf['posteriores'] as $postIndex => $posterior) {
                    if (!is_array($posterior)) {
                        throw new \Exception("Posterior en índice $postIndex no es un array");
                    }

                    if (!isset($posterior['nombre'])) {
                        throw new \Exception("Falta 'nombre' en posterior $postIndex");
                    }

                    $nombrePosterior = $this->normalizarNombreEstado($posterior['nombre']);

                    if (!isset($mapaEstados[$nombrePosterior])) {
                        $posteriorConf = collect($configuraciones)->first(function ($c) use ($nombrePosterior) {
                            return $this->normalizarNombreEstado($c['estado_actual']) === $nombrePosterior;
                        });

                        $idPosterior = DB::table('estado_tramite')->insertGetId([
                            'fecha_sistema' => $now,
                            'nombre' => $nombrePosterior,
                            'tipo' => strtoupper(str_replace(' ', '_', $nombrePosterior)),
                            'puede_rechazar' => $this->parseBool($posteriorConf['puede_rechazar'] ?? 0),
                            'puede_pedir_documentacion' => $this->parseBool($posteriorConf['puede_pedir_documentacion'] ?? 0),
                            'puede_elegir_camino' => 0,
                            'estado_tiene_expediente' => $this->parseBool($posteriorConf['estado_tiene_expediente'] ?? 0),
                        ]);

                        $mapaEstados[$nombrePosterior] = $idPosterior;
                    }
                }
            }
        }

        foreach ($configuraciones as $conf) {
            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            $idEstadoActual = $mapaEstados[$nombreEstado];

            DB::table('estado_tramite_asignable')
                ->where('id_estado_tramite', $idEstadoActual)
                ->delete();

            if (isset($conf['asignaciones']) && is_array($conf['asignaciones'])) {
                $asignacionesValidas = [];
                
                foreach ($conf['asignaciones'] as $asig) {
                    if (!is_array($asig)) continue;
                    
                    $grupoId = isset($asig['id_grupo_interno']) ? (int)$asig['id_grupo_interno'] : null;
                    $usuarioId = isset($asig['id_usuario_interno']) ? (int)$asig['id_usuario_interno'] : null;
                    
                    if ($grupoId !== null || $usuarioId !== null) {
                        $asignacionesValidas[] = [
                            'fecha_sistema' => $now,
                            'id_estado_tramite' => $idEstadoActual,
                            'id_grupo_interno' => $grupoId,
                            'id_usuario_interno' => $usuarioId,
                        ];
                    }
                }

                if (!empty($asignacionesValidas)) {
                    DB::table('estado_tramite_asignable')->insert($asignacionesValidas);
                }
            }

            $transiciones = [];
            
            if (empty($conf['posteriores']) || !is_array($conf['posteriores'])) {
                $transiciones[] = [
                    'fecha_sistema' => $now,
                    'id_estado_tramite' => $idEstadoActual,
                    'id_proximo_estado' => null,
                    'version' => $version,
                    'publico' => 1,
                    'id_tipo_tramite_multinota' => $id,
                    'activo' => 1
                ];
            } else {
                foreach ($conf['posteriores'] as $post) {
                    if (!isset($post['nombre'])) continue;
                    
                    $nombrePosterior = $this->normalizarNombreEstado($post['nombre']);
                    $transiciones[] = [
                        'fecha_sistema' => $now,
                        'id_estado_tramite' => $idEstadoActual,
                        'id_proximo_estado' => $mapaEstados[$nombrePosterior],
                        'version' => $version,
                        'publico' => 1,
                        'id_tipo_tramite_multinota' => $id,
                        'activo' => 1
                    ];
                }
            }

            if (!empty($transiciones)) {
                DB::table('configuracion_estado_tramite')->insert($transiciones);
            }
        }

       if ($versionesPublicas->count() > 2) {
            $versionesPublicasAEliminar = $versionesPublicas->slice(2)->pluck('version');
            
            DB::table('configuracion_estado_tramite')
                ->where('id_tipo_tramite_multinota', $id)
                ->where('publico', 1)
                ->whereIn('version', $versionesPublicasAEliminar)
                ->delete();
        }

       
        $this->actualizarTramitesActivos($id, $mapaEstados);


        DB::commit();
        return response()->json(['success' => true, 'message' => 'Workflow actualizado correctamente.']);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al guardar workflow', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'input' => $request->all()
        ]);
        return response()->json(['success' => false, 'message' => 'Error al guardar el workflow.']);
    }
}

private function actualizarTramitesActivos($id, $mapaEstados)
{
    $tramitesActivos = DB::table('tramite_estado_tramite as tet')
        ->join('multinota as m', 'tet.id_tramite', '=', 'm.id_tramite')
        ->where('m.id_tipo_tramite_multinota', $id)
        ->where('tet.activo', 1)
        ->exists();

    if (!$tramitesActivos) return;

    $tramites = DB::table('tramite_estado_tramite as tet')
        ->join('multinota as m', 'tet.id_tramite', '=', 'm.id_tramite')
        ->join('estado_tramite as et', 'tet.id_estado_tramite', '=', 'et.id_estado_tramite')
        ->where('m.id_tipo_tramite_multinota', $id)
        ->where('tet.activo', 1)
        ->where('m.flag_rechazado', 0)
        ->where('m.flag_cancelado', 0)
        ->where(function ($query) {
            $query->where('et.nombre', '!=', 'A Finalizar')
                  ->orWhere('tet.completo', '!=', 1);
        })
        ->select('tet.*')
        ->get();

    $estadosViejos = DB::table('estado_tramite')
        ->whereIn('id_estado_tramite', $tramites->pluck('id_estado_tramite'))
        ->pluck('nombre', 'id_estado_tramite');

    foreach ($tramites as $tramite) {
        $nombreEstado = $this->normalizarNombreEstado($estadosViejos[$tramite->id_estado_tramite] ?? '');

        if (isset($mapaEstados[$nombreEstado])) {
            $nuevoId = $mapaEstados[$nombreEstado];

            DB::table('tramite_estado_tramite')
                ->where('id_estado_tramite', $tramite->id_estado_tramite)
                ->update(['id_estado_tramite' => $nuevoId]);
        }
    }
}


public function publicarBorrador(Request $request, $id)
{
    $configuraciones = $request->input('configuraciones');

    if (!$configuraciones || !is_array($configuraciones)) {
        return response()->json(['success' => false, 'message' => 'Datos inválidos: configuraciones no es un array']);
    }

    DB::beginTransaction();
    try {
        $versionesExistentes = DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $id)
            ->select('version', 'publico', DB::raw('MAX(fecha_sistema) as fecha_maxima'))
            ->groupBy('version', 'publico')
            ->orderBy('fecha_maxima', 'desc')
            ->get();

            $versionesPublicas = $versionesExistentes->where('publico', 1)->values();
            $borradores = $versionesExistentes->where('publico', 0)->values();

        DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $id)
            ->where('activo', 1)
            ->update(['activo' => 0]);

        $mapaEstados = [];
        $version = uniqid();
        $now = now();

        foreach ($configuraciones as $index => $conf) {
            if (!is_array($conf)) {
                throw new \Exception("Configuración en índice $index no es un array");
            }

            if (!isset($conf['estado_actual'])) {
                throw new \Exception("Falta 'estado_actual' en configuración $index");
            }

            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            
            if (!isset($mapaEstados[$nombreEstado])) {
                $idEstado = DB::table('estado_tramite')->insertGetId([
                    'fecha_sistema' => $now,
                    'nombre' => $nombreEstado,
                    'tipo' => strtoupper(str_replace(' ', '_', $nombreEstado)),
                    'puede_rechazar' => $this->parseBool($conf['puede_rechazar'] ?? 0),
                    'puede_pedir_documentacion' => $this->parseBool($conf['puede_pedir_documentacion'] ?? 0),
                    'puede_elegir_camino' => 0,
                    'estado_tiene_expediente' => $this->parseBool($conf['estado_tiene_expediente'] ?? 0),
                ]);
                $mapaEstados[$nombreEstado] = $idEstado;
            }

            if (isset($conf['posteriores']) && is_array($conf['posteriores'])) {
                foreach ($conf['posteriores'] as $postIndex => $posterior) {
                    if (!is_array($posterior)) {
                        throw new \Exception("Posterior en índice $postIndex no es un array");
                    }

                    if (!isset($posterior['nombre'])) {
                        throw new \Exception("Falta 'nombre' en posterior $postIndex");
                    }

                    $nombrePosterior = $this->normalizarNombreEstado($posterior['nombre']);

                    if (!isset($mapaEstados[$nombrePosterior])) {
                        $posteriorConf = collect($configuraciones)->first(function ($c) use ($nombrePosterior) {
                            return $this->normalizarNombreEstado($c['estado_actual']) === $nombrePosterior;
                        });

                        $idPosterior = DB::table('estado_tramite')->insertGetId([
                            'fecha_sistema' => $now,
                            'nombre' => $nombrePosterior,
                            'tipo' => strtoupper(str_replace(' ', '_', $nombrePosterior)),
                            'puede_rechazar' => $this->parseBool($posteriorConf['puede_rechazar'] ?? 0),
                            'puede_pedir_documentacion' => $this->parseBool($posteriorConf['puede_pedir_documentacion'] ?? 0),
                            'puede_elegir_camino' => 0,
                            'estado_tiene_expediente' => $this->parseBool($posteriorConf['estado_tiene_expediente'] ?? 0),
                        ]);

                        $mapaEstados[$nombrePosterior] = $idPosterior;
                    }
                }
            }
        }

        foreach ($configuraciones as $conf) {
            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            $idEstadoActual = $mapaEstados[$nombreEstado];

            DB::table('estado_tramite_asignable')
                ->where('id_estado_tramite', $idEstadoActual)
                ->delete();

            if (isset($conf['asignaciones']) && is_array($conf['asignaciones'])) {
                $asignacionesValidas = [];
                
                foreach ($conf['asignaciones'] as $asig) {
                    if (!is_array($asig)) continue;
                    
                    $grupoId = isset($asig['id_grupo_interno']) ? (int)$asig['id_grupo_interno'] : null;
                    $usuarioId = isset($asig['id_usuario_interno']) ? (int)$asig['id_usuario_interno'] : null;
                    
                    if ($grupoId !== null || $usuarioId !== null) {
                        $asignacionesValidas[] = [
                            'fecha_sistema' => $now,
                            'id_estado_tramite' => $idEstadoActual,
                            'id_grupo_interno' => $grupoId,
                            'id_usuario_interno' => $usuarioId,
                        ];
                    }
                }

                if (!empty($asignacionesValidas)) {
                    DB::table('estado_tramite_asignable')->insert($asignacionesValidas);
                }
            }

            $transiciones = [];
            
            if (empty($conf['posteriores']) || !is_array($conf['posteriores'])) {
                $transiciones[] = [
                    'fecha_sistema' => $now,
                    'id_estado_tramite' => $idEstadoActual,
                    'id_proximo_estado' => null,
                    'version' => $version,
                    'publico' => 1,
                    'id_tipo_tramite_multinota' => $id,
                    'activo' => 1
                ];
            } else {
                foreach ($conf['posteriores'] as $post) {
                    if (!isset($post['nombre'])) continue;
                    
                    $nombrePosterior = $this->normalizarNombreEstado($post['nombre']);
                    $transiciones[] = [
                        'fecha_sistema' => $now,
                        'id_estado_tramite' => $idEstadoActual,
                        'id_proximo_estado' => $mapaEstados[$nombrePosterior],
                        'version' => $version,
                        'publico' => 1,
                        'id_tipo_tramite_multinota' => $id,
                        'activo' => 1
                    ];
                }
            }

            if (!empty($transiciones)) {
                DB::table('configuracion_estado_tramite')->insert($transiciones);
            }
        }

       if ($versionesPublicas->count() > 2) {
            $versionesPublicasAEliminar = $versionesPublicas->slice(2)->pluck('version');
            
            DB::table('configuracion_estado_tramite')
                ->where('id_tipo_tramite_multinota', $id)
                ->where('publico', 1)
                ->whereIn('version', $versionesPublicasAEliminar)
                ->delete();
        }


        DB::table('configuracion_estado_tramite')
            ->where('publico', 0)
            ->delete();

       
        $this->actualizarTramitesActivos($id, $mapaEstados);


        DB::commit();
        return response()->json(['success' => true, 'message' => 'Workflow actualizado correctamente.']);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al guardar workflow', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'input' => $request->all()
        ]);
        return response()->json(['success' => false, 'message' => 'Error al guardar el workflow.']);
    }
}





public function guardarBorrador(Request $request, $id)
{
    $configuraciones = $request->input('configuraciones');

    if (!$configuraciones || !is_array($configuraciones)) {
        return response()->json(['success' => false, 'message' => 'Datos inválidos: configuraciones no es un array']);
    }

    DB::beginTransaction();
    try {
        
        DB::table('configuracion_estado_tramite')
            ->where('id_tipo_tramite_multinota', $id)
            ->where('publico', 0)
            ->delete();

        $mapaEstados = [];
        $version = uniqid();
        $now = now();

        foreach ($configuraciones as $index => $conf) {
            if (!is_array($conf)) {
                throw new \Exception("Configuración en índice $index no es un array");
            }

            if (!isset($conf['estado_actual'])) {
                throw new \Exception("Falta 'estado_actual' en configuración $index");
            }

            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            
            if (!isset($mapaEstados[$nombreEstado])) {
                $idEstado = DB::table('estado_tramite')->insertGetId([
                    'fecha_sistema' => $now,
                    'nombre' => $nombreEstado,
                    'tipo' => strtoupper(str_replace(' ', '_', $nombreEstado)),
                    'puede_rechazar' => $this->parseBool($conf['puede_rechazar'] ?? 0),
                    'puede_pedir_documentacion' => $this->parseBool($conf['puede_pedir_documentacion'] ?? 0),
                    'puede_elegir_camino' => 0,
                    'estado_tiene_expediente' => $this->parseBool($conf['estado_tiene_expediente'] ?? 0),
                ]);
                $mapaEstados[$nombreEstado] = $idEstado;
            }

     if (isset($conf['posteriores']) && is_array($conf['posteriores'])) {
    foreach ($conf['posteriores'] as $postIndex => $posterior) {
        if (!is_array($posterior)) {
            throw new \Exception("Posterior en índice $postIndex no es un array");
        }

        if (!isset($posterior['nombre'])) {
            throw new \Exception("Falta 'nombre' en posterior $postIndex");
        }

        $nombrePosterior = $this->normalizarNombreEstado($posterior['nombre']);


        if (!isset($mapaEstados[$nombrePosterior])) {
            $posteriorConf = collect($configuraciones)->first(function ($c) use ($nombrePosterior) {
                return $this->normalizarNombreEstado($c['estado_actual']) === $nombrePosterior;
            });

            $idPosterior = DB::table('estado_tramite')->insertGetId([
                'fecha_sistema' => $now,
                'nombre' => $nombrePosterior,
                'tipo' => strtoupper(str_replace(' ', '_', $nombrePosterior)),
                'puede_rechazar' => $this->parseBool($posteriorConf['puede_rechazar'] ?? 0),
                'puede_pedir_documentacion' => $this->parseBool($posteriorConf['puede_pedir_documentacion'] ?? 0),
                'puede_elegir_camino' => 0,
                'estado_tiene_expediente' => $this->parseBool($posteriorConf['estado_tiene_expediente'] ?? 0),
            ]);

            $mapaEstados[$nombrePosterior] = $idPosterior;
        }
    }
}

        }

        foreach ($configuraciones as $conf) {
            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            $idEstadoActual = $mapaEstados[$nombreEstado];

            DB::table('estado_tramite_asignable')
                ->where('id_estado_tramite', $idEstadoActual)
                ->delete();

            if (isset($conf['asignaciones']) && is_array($conf['asignaciones'])) {
                $asignacionesValidas = [];
                
                foreach ($conf['asignaciones'] as $asig) {
                    if (!is_array($asig)) continue;
                    
                    $grupoId = isset($asig['id_grupo_interno']) ? (int)$asig['id_grupo_interno'] : null;
                    $usuarioId = isset($asig['id_usuario_interno']) ? (int)$asig['id_usuario_interno'] : null;
                    
                    if ($grupoId !== null || $usuarioId !== null) {
                        $asignacionesValidas[] = [
                            'fecha_sistema' => $now,
                            'id_estado_tramite' => $idEstadoActual,
                            'id_grupo_interno' => $grupoId,
                            'id_usuario_interno' => $usuarioId,
                        ];
                    }
                }

                if (!empty($asignacionesValidas)) {
                    DB::table('estado_tramite_asignable')->insert($asignacionesValidas);
                }
            }

            $transiciones = [];
            
            if (empty($conf['posteriores']) || !is_array($conf['posteriores'])) {
                $transiciones[] = [
                    'fecha_sistema' => $now,
                    'id_estado_tramite' => $idEstadoActual,
                    'id_proximo_estado' => null,
                    'version' => $version,
                    'publico' => 0,
                    'id_tipo_tramite_multinota' => $id,
                    'activo' => 0
                ];
            } else {
                foreach ($conf['posteriores'] as $post) {
                    if (!isset($post['nombre'])) continue;
                    
                    $nombrePosterior = $this->normalizarNombreEstado($post['nombre']);
                    $transiciones[] = [
                        'fecha_sistema' => $now,
                        'id_estado_tramite' => $idEstadoActual,
                        'id_proximo_estado' => $mapaEstados[$nombrePosterior],
                        'version' => $version,
                        'publico' => 0,
                        'id_tipo_tramite_multinota' => $id,
                        'activo' => 0
                    ];
                }
            }

            if (!empty($transiciones)) {
                DB::table('configuracion_estado_tramite')->insert($transiciones);
            }
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Workflow actualizado correctamente.']);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error al guardar workflow', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'input' => $request->all()
        ]);
        return response()->json(['success' => false, 'message' => 'Error al guardar el workflow.']);
    }
}


protected function normalizarNombreEstado($nombre)
{
    if (is_array($nombre) && isset($nombre['nombre'])) {
        $nombre = $nombre['nombre'];
    }

    if (is_array($nombre)) {
        Log::error('Se recibió un array como nombre de estado', ['nombre' => $nombre]);
        throw new \Exception("Nombre de estado no puede ser un array");
    }

    $nombre = (string) $nombre;

    if (empty(trim($nombre))) {
        throw new \Exception("Nombre de estado no puede estar vacío");
    }

    return trim($nombre);
}


protected function parseBool($value)
{
    if (is_bool($value)) {
        return $value ? 1 : 0;
    }
    
    return $value ? 1 : 0;
}


}
