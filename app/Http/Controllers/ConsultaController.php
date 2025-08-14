<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ConsultaController extends Controller
{
    public function formulario()
    {
        return view('tramites.consulta');
    }

    public function consultar(Request $request)
    {
        $pregunta = $request->input('pregunta');

        if (!preg_match('/tr[aá]mite|documento|prioridad|rechazado|estado|sección|campo|cancelado|finalizado/i', $pregunta)) {
            return response()->json([
                'respuesta' => '⚠️ No puedo ayudarte con eso, pero si tenés alguna duda respecto a los trámites estoy para ayudarte.',
                'tipo' => 'texto',
                'resumen' => 'No puedo ayudarte con eso, pero si tenés alguna duda respecto a los trámites estoy para ayudarte.'
            ]);
        }

        $esquema = file_get_contents(resource_path('prompts/esquema_tramites.txt'));
        $instrucciones = file_get_contents(resource_path('prompts/plantilla_prompt.txt'));
        $mensaje = <<<PROMPT
                    $instrucciones

                    Esquema de base de datos:
                    $esquema

                    Pregunta del usuario:
                    $pregunta
                    PROMPT;
    
        $respuesta = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'system', 'content' => 'Sos un asistente experto en bases de datos. Respondé solo con SQL válida.'],
                ['role' => 'user', 'content' => $mensaje],
            ],
        ]);

        $sql = trim($respuesta['choices'][0]['message']['content']);

        // Extraer SQL si viene dentro de un bloque Markdown (```sql ... ```)
        if (preg_match('/```sql(.*?)```/is', $sql, $matches)) {
            $sql = trim($matches[1]);
        }
        \Log::info("Consulta GPT: $pregunta");
        \Log::info("SQL generada: $sql");

        if (!str_starts_with(strtolower($sql), 'select')) {
            return response()->json([
                'respuesta' => '⚠️ La consulta generada no es segura o no es un SELECT.',
                'tipo' => 'texto'
            ]);
        }

        try {
            $resultados = DB::select($sql);

            if (empty($resultados)) {
                $respuestaHTML = '🔍 No se encontraron resultados.';
                $resumen = 'No se encontraron resultados.';
            } else {
                // Armar tabla
                $respuestaHTML = '<table class="table table-bordered table-sm"><thead><tr>';
                foreach ((array) $resultados[0] as $col => $val) {
                    $respuestaHTML .= "<th>" . e($col) . "</th>";
                }
                $respuestaHTML .= "</tr></thead><tbody>";
                foreach ($resultados as $fila) {
                    $respuestaHTML .= "<tr>";
                    foreach ((array) $fila as $valor) {
                        $respuestaHTML .= "<td>" . e($valor) . "</td>";
                    }
                    $respuestaHTML .= "</tr>";
                }
                $respuestaHTML .= "</tbody></table>";

                // Generar resumen (ejemplo: para COUNT(*) AS Total → "Se encontraron 12 trámites.")
                $primeraClave = array_keys((array) $resultados[0])[0];
                $valor = ((array) $resultados[0])[$primeraClave];
                //$resumen = "Se encontraron $valor trámites.";
                $resumen = $this->generarResumen($pregunta, $resultados);

            }

            return response()->json([
                'respuesta' => $respuestaHTML,
                'tipo' => 'texto',
                'resumen' => $resumen
            ]);
        } catch (\Exception $e) {
            \Log::error("Error al ejecutar SQL: " . $e->getMessage());
            return response()->json([
                'respuesta' => '❌ Error al ejecutar la consulta.',
                'tipo' => 'texto'
            ]);
        }
    }

    function generarResumen($pregunta, $resultados)
    {
        $pregunta = strtolower($pregunta);
        $resumen = '';

        if (str_contains($pregunta, 'cuántos') || str_contains($pregunta, 'cuantos')) {
            $valor = $resultados[0]->{array_key_first((array)$resultados[0])};
            $resumen = "Se encontraron $valor trámites.";
        } elseif (preg_match('/estado del tr[aá]mite (\d+)/i', $pregunta, $matches)) {
            $id = $matches[1];
            $estado = $resultados[0]->{array_key_first((array)$resultados[0])};
            $resumen = "El estado del trámite $id es $estado.";
        } else {
            // Si es una tabla compleja, mostrás la cantidad de filas
            $cantidad = count($resultados);
            $resumen = "Se encontraron $cantidad resultados.";
        }

        if (str_contains($pregunta, 'tiempo') || str_contains($pregunta, 'resolución') || str_contains($pregunta, 'demor[aó]')) {
            $fila = (array) $resultados[0];

            $tiempoFormateado = $this->formatoTiempoResolucion(
                $fila['dias_resolucion'] ?? 0,
                $fila['fecha_inicio'] ?? $fila['fechaInicio'] ?? '',
                $fila['fecha_final'] ?? $fila['fechaFinal'] ?? ''
            );

            $resumen = "El trámite {$fila['id_tramite']} se resolvió en $tiempoFormateado.";
        }


        return $resumen;
    }

    private function formatoTiempoResolucion($dias, $fechaInicio, $fechaFin)
    {
        if ($dias > 0) {
            return "Días: $dias";
        }

        $inicio = strtotime($fechaInicio);
        $fin = strtotime($fechaFin);
        $segundos = $fin - $inicio;

        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos % 3600) / 60);
        $restoSegundos = $segundos % 60;

        return sprintf('%02d:%02d:%02d', $horas, $minutos, $restoSegundos);
    }


}
