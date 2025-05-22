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
                    \DB::raw('IF(configuracion_estado_tramite.id_tipo_tramite_multinota IS NULL, 0, 1) as existe_configuracion')
                ])
                ->groupBy('tipo_tramite_multinota.id_tipo_tramite_multinota', 'categoria.nombre', 'tipo_tramite_multinota.nombre');

            return DataTables::of($data)
                ->addIndexColumn()
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


    public function edit($id)
    {
        $tramite = TipoTramiteMultinota::findOrFail($id);
        return view('tramites.edit', compact('tramite'));
    }

    

public function guardar(Request $request, $id)
{
    $configuraciones = $request->input('configuraciones');


    if (!$configuraciones || !is_array($configuraciones)) {
        return response()->json(['success' => false, 'message' => 'Datos inválidos']);
    }

    DB::beginTransaction();
    try {
        $nombresEstados = [];

        foreach ($configuraciones as $conf) {
            $nombresEstados[] = $conf['estado_actual'];
            foreach ($conf['posteriores'] as $post) {
                $nombresEstados[] = $post['nombre'];
            }
        }

        $nombresEstados = array_unique($nombresEstados);


        $mapaEstados = [];

        foreach ($nombresEstados as $nombre) {
           
                \Log::info('Insertando estado:', [
                    'nombre' => $nombre,
                    'tipo' => $confEstado['tipo'] ?? strtoupper(str_replace(' ', '_', $nombre)),
                    'puede_rechazar' => $confEstado['puede_rechazar'] ?? 0,
                    'puede_pedir_documentacion' => $confEstado['puede_pedir_documentacion'] ?? 0,
                    'estado_tiene_expediente' => $confEstado['estado_tiene_expediente'] ?? 0,
                ]);
                

                $idNuevo = DB::table('estado_tramite')->insertGetId([
                    'fecha_sistema' => now(),
                    'nombre' => $nombre,
                    'tipo' => $confEstado['tipo'] ?? strtoupper(str_replace(' ', '_', $nombre)),
                    'puede_rechazar' => $confEstado['puede_rechazar'] ?? 0,
                    'puede_pedir_documentacion' => $confEstado['puede_pedir_documentacion'] ?? 0,
                    'puede_elegir_camino' =>  0,
                    'estado_tiene_expediente' => $confEstado['estado_tiene_expediente'] ?? 0,
                ]);

                

                $mapaEstados[$nombre] = $idNuevo;
            }

        $version = uniqid();

        foreach ($configuraciones as $conf) {
            $idEstadoActual = $mapaEstados[$conf['estado_actual']];
        
            if (empty($conf['posteriores'])) {
                DB::table('configuracion_estado_tramite')->insert([
                    'fecha_sistema' => now(),
                    'id_estado_tramite' => $idEstadoActual,
                    'id_proximo_estado' => null,
                    'version' => $version,
                    'publico' => 1,
                    'id_tipo_tramite_multinota' => $id,
                    'activo' => 1
                ]);
            } else {
                foreach ($conf['posteriores'] as $post) {
                    $idPosterior = $mapaEstados[$post['nombre']];
                    DB::table('configuracion_estado_tramite')->insert([
                        'fecha_sistema' => now(),
                        'id_estado_tramite' => $idEstadoActual,
                        'id_proximo_estado' => $idPosterior,
                        'version' => $version,
                        'publico' => 1,
                        'id_tipo_tramite_multinota' => $id,
                        'activo' => 1
                    ]);
                }
            }
        }
        

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Workflow guardado correctamente.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
    }
}

}
