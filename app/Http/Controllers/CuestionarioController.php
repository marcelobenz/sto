<?php

namespace App\Http\Controllers;

use App\Models\Cuestionario;
use App\Models\Pregunta;
use Illuminate\Http\Request;
use App\Models\ConfiguracionEstadoTramite;
use App\Models\EstadoTramite;
use App\Models\TipoTramiteMultinota;
use App\Models\CuestionarioEstadoTramite;
use Illuminate\Support\Facades\DB;

class CuestionarioController extends Controller
{
    public function index()
    {
        $cuestionarios = Cuestionario::all(); 
    
        return view('cuestionarios.index', compact('cuestionarios'));
    }
    

    public function create()
    {
        $configuraciones = ConfiguracionEstadoTramite::where('activo', 1)->get();

        $agrupado = [];

    foreach ($configuraciones as $conf) {
        $tipo = $conf->id_tipo_tramite_multinota;
        $estado = EstadoTramite::find($conf->id_estado_tramite);

        if (!$estado) continue;

        $agrupado[$tipo][] = [
            'id_estado_tramite' => $estado->id_estado_tramite,
            'nombre_estado' => $estado->nombre,
        ];
    }

    $tipos = TipoTramiteMultinota::whereIn('id_tipo_tramite_multinota', array_keys($agrupado))
             ->pluck('nombre', 'id_tipo_tramite_multinota');

    return view('cuestionarios.crear', compact('agrupado', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'preguntas' => 'required|json' 
        ]);
        $cuestionario = new Cuestionario();
        $cuestionario->fecha_sistema = now(); 
        $cuestionario->flag_baja = 0; 
        $cuestionario->titulo = $request->titulo;
        $cuestionario->descripcion = $request->descripcion;
        $cuestionario->save(); 
    
       
        $preguntas = json_decode($request->preguntas, true);
    
        foreach ($preguntas as $preguntaData) {
            $pregunta = new Pregunta();
            $pregunta->id_cuestionario = $cuestionario->id_cuestionario; 
            $pregunta->fecha_sistema = now(); 
            $pregunta->descripcion = $preguntaData['texto'];
            
          
            $pregunta->flag_detalle_si = $preguntaData['siDetalle'] ? 1 : 0;
            $pregunta->flag_detalle_no = $preguntaData['noDetalle'] ? 1 : 0;
            $pregunta->flag_finalizacion_si = $preguntaData['finalizaSi'] ? 1 : 0;
            $pregunta->flag_rechazo_no = $preguntaData['rechazaNo'] ? 1 : 0;
            $pregunta->flag_baja = 0; 
    
            $pregunta->save(); 
    }

     if ($request->has('tipo_tramite_multinota')) {
        CuestionarioEstadoTramite::where('id_cuestionario', $cuestionario->id_cuestionario)->delete();

        foreach ($request->tipo_tramite_multinota as $tipoId => $estadosSeleccionados) {
            foreach ($estadosSeleccionados as $id_estado_tramite) {
                CuestionarioEstadoTramite::create([
                    'id_cuestionario' => $cuestionario->id_cuestionario,
                    'id_estado_tramite' => $id_estado_tramite,
                    'fecha_sistema' => now()
                ]);
            }
        }

    }


        return redirect()->route('cuestionarios.index')->with('success', 'Cuestionario y preguntas guardados exitosamente.');
    }

   public function edit($id)
{
    $cuestionario = Cuestionario::with('preguntas')->findOrFail($id); 
    $configuraciones = ConfiguracionEstadoTramite::where('activo', 1)->get();

    $agrupado = [];

    foreach ($configuraciones as $conf) {
        $tipo = $conf->id_tipo_tramite_multinota;
        $estado = EstadoTramite::find($conf->id_estado_tramite);

        if (!$estado) continue;

        $agrupado[$tipo][] = [
            'id_estado_tramite' => $estado->id_estado_tramite,
            'nombre_estado' => $estado->nombre,
        ];
    }

    $tipos = TipoTramiteMultinota::whereIn('id_tipo_tramite_multinota', array_keys($agrupado))
             ->pluck('nombre', 'id_tipo_tramite_multinota');

    $estadosSeleccionados = DB::table('cuestionario_estado_tramite')
    ->where('id_cuestionario', $cuestionario->id_cuestionario)
    ->pluck('id_estado_tramite')
    ->toArray();


    return view('cuestionarios.editar', compact('cuestionario', 'agrupado', 'tipos', 'estadosSeleccionados'));
}


   public function update(Request $request, $id)
{
    $request->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'preguntas' => 'array',
        'nuevas_preguntas' => 'nullable|array',
        'tipo_tramite_multinota' => 'nullable|array'
    ]);

    $cuestionario = Cuestionario::findOrFail($id);
    $cuestionario->titulo = $request->titulo;
    $cuestionario->descripcion = $request->descripcion;
    $cuestionario->save();


    if ($request->has('preguntas')) {
        foreach ($request->preguntas as $idPregunta => $preguntaData) {
            $pregunta = Pregunta::findOrFail($idPregunta);
            $pregunta->descripcion = $preguntaData['descripcion'];
            $pregunta->flag_detalle_si = isset($preguntaData['siDetalle']) ? 1 : 0;
            $pregunta->flag_detalle_no = isset($preguntaData['noDetalle']) ? 1 : 0;
            $pregunta->flag_finalizacion_si = isset($preguntaData['finalizaSi']) ? 1 : 0;
            $pregunta->flag_rechazo_no = isset($preguntaData['rechazaNo']) ? 1 : 0;
            $pregunta->save();
        }
    }

    
    if ($request->has('nuevas_preguntas')) {
        foreach ($request->nuevas_preguntas as $preguntaData) {
            $nuevaPregunta = new Pregunta();
            $nuevaPregunta->id_cuestionario = $cuestionario->id_cuestionario;
            $nuevaPregunta->fecha_sistema = now();
            $nuevaPregunta->descripcion = $preguntaData['descripcion'];
            $nuevaPregunta->flag_detalle_si = !empty($preguntaData['siDetalle']) ? 1 : 0;
            $nuevaPregunta->flag_detalle_no = !empty($preguntaData['noDetalle']) ? 1 : 0;
            $nuevaPregunta->flag_finalizacion_si = !empty($preguntaData['finalizaSi']) ? 1 : 0;
            $nuevaPregunta->flag_rechazo_no = !empty($preguntaData['rechazaNo']) ? 1 : 0;
            $nuevaPregunta->flag_baja = 0;
            $nuevaPregunta->save();
        }
    }


    if ($request->has('tipo_tramite_multinota')) {
        CuestionarioEstadoTramite::where('id_cuestionario', $cuestionario->id_cuestionario)->delete();

        foreach ($request->tipo_tramite_multinota as $tipoId => $estadosSeleccionados) {
            foreach ($estadosSeleccionados as $id_estado_tramite) {
                CuestionarioEstadoTramite::create([
                    'id_cuestionario' => $cuestionario->id_cuestionario,
                    'id_estado_tramite' => $id_estado_tramite,
                    'fecha_sistema' => now()
                ]);
            }
        }

    }

    return redirect()->route('cuestionarios.index')->with('success', 'Cuestionario y preguntas actualizados exitosamente.');
} 
    

    public function activar($id)
    {
        $cuestionario = Cuestionario::findOrFail($id);
        $cuestionario->flag_baja = 0; 
        $cuestionario->save();
    
        return redirect()->route('cuestionarios.index')->with('success', 'Cuestionario activado exitosamente.');
    }
    
    public function desactivar($id)
    {
        $cuestionario = Cuestionario::findOrFail($id);
        $cuestionario->flag_baja = 1; 
        $cuestionario->save();
    
        return redirect()->route('cuestionarios.index')->with('success', 'Cuestionario desactivado exitosamente.');
    }
    
}
