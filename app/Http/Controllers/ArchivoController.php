<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\TramiteArchivo;
use App\Models\Archivo;
use Normalizer;

class ArchivoController extends Controller
{
    /**
     * Permite descargar un archivo adjunto del trámite.
     */
    public function descargar($id)
    {
        // Buscar el archivo en la base de datos con su relación
        $archivo = TramiteArchivo::with('archivo')->where('id_archivo', (int) $id)->first();

        if (!$archivo || !$archivo->archivo) {
            return back()->with('error', 'Archivo no encontrado.');
        }

        // Obtener y normalizar la ruta del archivo
        $rutaArchivo = Normalizer::normalize($archivo->archivo->path_archivo, Normalizer::FORM_C);

        if (!$rutaArchivo) {
            return back()->with('error', 'No se encontró la ruta del archivo.');
        }

        // Verificar si el archivo existe en el almacenamiento
        if (!Storage::disk('adjuntos')->exists($rutaArchivo)) {
            return back()->with('error', 'El archivo no está disponible.');
        }

        // Descargar el archivo usando la ruta completa
        return response()->download(storage_path('app/adjuntos/' . $rutaArchivo), $archivo->archivo->nombre);
    }

    /**
     * Permite subir un archivo adjunto.
     */
    public function subirArchivo(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|max:10240', // Máximo 10MB
            'id_tramite' => 'required|integer',
        ]);

        // Obtener el archivo desde el request
        $archivo = $request->file('archivo');

        // Definir la carpeta basada en el ID del trámite
        $carpetaTramite = $request->id_tramite;

        // Asegurar que la carpeta exista
        Storage::disk('adjuntos')->makeDirectory($carpetaTramite);

        // Generar un nombre único para el archivo (evitar colisiones)
        $nombreArchivo = time() . '_' . Normalizer::normalize($archivo->getClientOriginalName(), Normalizer::FORM_C);

        // Guardar el archivo en la carpeta correspondiente
        $rutaArchivo = $archivo->storeAs($carpetaTramite, $nombreArchivo, 'adjuntos');

        // Crear el registro en la base de datos
        $archivoDB = Archivo::create([
            'nombre' => $archivo->getClientOriginalName(),
            'tipo_contenido' => $archivo->getMimeType(),
            'path_archivo' => $rutaArchivo,
            'descripcion' => $request->descripcion ?? '',
        ]);

        try {
            // Relacionar el archivo con el trámite
            $tramiteArchivo = TramiteArchivo::create([
                'id_tramite' => $request->id_tramite,
                'id_archivo' => $archivoDB->id_archivo,
                'fecha_alta' => now(),
            ]);

            if (!$tramiteArchivo) {
                throw new \Exception('No se pudo crear el registro en tramite_archivo.');
            }

            return back()->with('success', 'Archivo subido correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al subir el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Permite subir un archivo adjunto previamente a tener el trámite creado (durante la instanciación).
     */
    public function subirArchivoTemporal(Request $request) {
        $file = $request->file('archivo');

        // Guardar temporalmente
        $path = $file->store('temp'); // Guarda en storage/app/temp

        // Guarda la metadata en sesión
        $archivos = Session::get('ARCHIVOS', []);
        $archivos[] = [
            'nombre' => $file->getClientOriginalName(),
            'tipoContenido' => $file->getMimeType(),
            'pathArchivo' => $path,
            'peso' => $file->getSize(),
            'fechaCarga' => now()->format('Y-m-d H:i:s'),
            'comentario' => '', // Opcional
        ];
        Session::put('ARCHIVOS', $archivos);

        $htmlVista = view('partials.etapas-tramite.adjuntar-documentacion', compact('archivos'))->render();

        return response()->json([
            'htmlVista' => $htmlVista,
        ]);
    }

    /**
     * Permite cargar un comentario a un archivo dado correspondiente a la fecha de carga obtenida como parámetro
     */
    public function cargarComentario(Request $request) {
        $comentario = $request->post('comentario');
        $fechaCarga = $request->post('fechaCarga');
        $archivos = Session::get('ARCHIVOS', []);

        foreach ($archivos as &$a) {
            if ($a['fechaCarga'] === $fechaCarga) {
                $a['comentario'] = $comentario;
            }
        }
        unset($a);

        Session::put('ARCHIVOS', $archivos);
    }

    /**
     * Permite eliminar un archivo adjunto previamente a tener el trámite creado (durante la instanciación).
     */
    public function eliminarArchivoTemporal(Request $request) {
        $fechaCarga = $request->post('fechaCarga');
        $archivos = Session::get('ARCHIVOS', []);
    
        // Se elimina el archivo con la fecha de carga correspondiente
        $archivos = array_filter($archivos, function ($archivo) use ($fechaCarga) {
            return $archivo['fechaCarga'] !== $fechaCarga;
        });

        // Reseteo de índices
        $archivos = array_values($archivos);

        Session::put('ARCHIVOS', $archivos);

        $htmlVista = view('partials.etapas-tramite.adjuntar-documentacion', compact('archivos'))->render();

        return response()->json([
            'htmlVista' => $htmlVista,
        ]);
    }
}
