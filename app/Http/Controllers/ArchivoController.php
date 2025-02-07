<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\TramiteArchivo;

class ArchivoController extends Controller
{
    /**
     * Permite descargar un archivo adjunto del trámite.
     */
    public function descargar($id)
    {
        // Buscar el archivo en la base de datos
        $archivo = TramiteArchivo::find($id);

        // Verificar si el archivo existe
        if (!$archivo) {
            return back()->with('error', 'Archivo no encontrado.');
        }

        // Obtener la ruta completa del archivo (Ejemplo: en S3 o almacenamiento local)
        $rutaArchivo = $archivo->ruta; // Asegúrate de que en la BD esté almacenada la ruta correcta

        // Verificar si el archivo existe en el almacenamiento
        if (!Storage::exists($rutaArchivo)) {
            return back()->with('error', 'El archivo no está disponible.');
        }

        // Retornar el archivo como descarga
        return Storage::download($rutaArchivo, $archivo->nombre);
    }
}
