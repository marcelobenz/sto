<?php

namespace App\Http\Controllers;

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
}
