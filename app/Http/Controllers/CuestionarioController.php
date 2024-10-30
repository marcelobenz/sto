<?php

namespace App\Http\Controllers;

use App\Models\Cuestionario;
use App\Models\Pregunta;
use Illuminate\Http\Request;

class CuestionarioController extends Controller
{
    public function index()
    {
        $cuestionarios = Cuestionario::all(); 
    
        return view('cuestionarios.index', compact('cuestionarios'));
    }
    

    public function create()
    {
        return view('cuestionarios.crear');
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
        return redirect()->route('cuestionarios.index')->with('success', 'Cuestionario y preguntas guardados exitosamente.');
    }

    public function edit($id)
    {
        $cuestionario = Cuestionario::with('preguntas')->findOrFail($id); 
        return view('cuestionarios.editar', compact('cuestionario'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'preguntas' => 'array',  
            'nuevas_preguntas' => 'nullable|array' 
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
