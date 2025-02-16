<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Services\OpenAIService;
use App\Models\Tramite;
use App\Models\Estado;

class TramiteController extends Controller
{

    /*Definicion de funciones para prueba de openAI*/
    protected $openAIService;

    public function __construct(OpenAIService $openAIService) {
        $this->openAIService = $openAIService;
    }

    public function consultar(Request $request) {
        $pregunta = $request->input('pregunta', '');
        \Log::info("❓ Pregunta recibida: {$pregunta}");
    
        // 🔍 Detectar si menciona un estado
        $estadosValidos = ['Iniciado', 'En Análisis', 'En Aprobación', 'A Finalizar', 'Finalizado'];
        foreach ($estadosValidos as $estado) {
            if (stripos($pregunta, $estado) !== false) {
                $url = url("/tramites?estado=" . urlencode($estado));
                \Log::info("🔄 Redirigiendo a listado por estado:", ['url' => $url]);
                return response()->json([
                    'tipo' => 'redirect',
                    'url' => $url
                ]);
            }
        }
    
        // 🔍 Detectar si el usuario menciona un número de trámite
        preg_match('/(\d+)/', $pregunta, $matches);
        $codigoTramite = $matches[0] ?? null;
        \Log::info("❓ ID Tramite recibida: {$codigoTramite}");
    
        if ($codigoTramite) {
            \Log::info("🔎 Buscando trámite con ID: {$codigoTramite}");
            $tramite = Tramite::with(['estadoActual.estado'])->where('id_tramite', $codigoTramite)->first();
    
            if ($tramite) {
                $estadoNombre = optional($tramite->estadoActual->estado)->nombre; // ✅ Evita errores si no hay estado
                if ($estadoNombre) {
                    \Log::info("✅ Trámite encontrado. Estado: {$estadoNombre}");
                    return response()->json([
                        'tipo' => 'texto',
                        'respuesta' => "El estado del trámite {$codigoTramite} es: {$estadoNombre}."
                    ]);
                }
            }
    
            \Log::warning("❌ Trámite no encontrado o sin estado: {$codigoTramite}");
            return response()->json([
                'tipo' => 'texto',
                'respuesta' => "No encontré el trámite *{$codigoTramite}*. Verifica el número e intenta nuevamente."
            ]);
        }
    
        // 🟢 Si no es una consulta de filtro ni de trámite, responder con OpenAI
        \Log::info("💬 Enviando consulta a OpenAI");
        $respuesta = $this->openAIService->consultarTramite($pregunta);
    
        return response()->json([
            'tipo' => 'texto',
            'respuesta' => $respuesta
        ]);
    }
    
    public function mostrarVistaConsulta()
    {
        return view('tramites.consulta');
    }

    /*Definicion de funciones para prueba de openAI*/


    /**
     * Muestra la vista principal.
     */
    public function index()
    {
        return view('tramites.index');
    }

    /**
     * Proporciona los datos para el DataTable.
     */
    public function getTramitesData(Request $request)
    {
        \Log::info('Entrando a getTramitesData');
        \Log::info('Parámetros recibidos en la petición:', $request->all());
    
        // Verificar si 'order' está presente en la petición
        $order = $request->get('order') ?? [];
        if (!empty($order) && isset($order[0]['column'])) {
            $columnIndex = $order[0]['column'];
            $columnName = $request->get('columns')[$columnIndex]['data'];
            $columnSortOrder = $order[0]['dir'];
        } else {
            // Valores por defecto si no viene la ordenación
            $columnIndex = 0;
            $columnName = 'id_tramite';
            $columnSortOrder = 'desc';
        }
    
        // Verificar si 'search' está presente
        $searchValue = $request->get('search')['value'] ?? null;
    
        // Verificar si 'estado' está presente
        $estadoFiltro = strtolower($request->get('estado', ''));
    
        // Construcción de la consulta
        $query = DB::table('multinota as m')
            ->join('tramite as t', 'm.id_tramite', '=', 't.id_tramite')
            ->join('tipo_tramite_multinota as tt', 'm.id_tipo_tramite_multinota', '=', 'tt.id_tipo_tramite_multinota')
            ->join('contribuyente_externo as ce', 't.id_tramite', '=', 'ce.id_tramite')
            ->join('tramite_estado_tramite as te', 'm.id_tramite', '=', 'te.id_tramite')
            ->join('usuario_interno as u', 'te.id_usuario_interno', '=', 'u.id_usuario_interno')
            ->join('categoria as c', 'tt.id_categoria', '=', 'c.id_categoria')
            ->join('estado_tramite as e', 'te.id_estado_tramite', '=', 'e.id_estado_tramite')
            ->select(
                'm.id_tramite as id_tramite',
                'm.cuenta',
                'c.nombre as nombre_categoria',
                'tt.nombre as tipo_tramite',
                'e.nombre as estado',
                'm.fecha_alta',
                't.cuit_contribuyente',
                DB::raw("CONCAT(ce.nombre, ' ', ce.apellido) as contribuyente"),
                DB::raw("CONCAT(u.nombre, ' ', u.apellido) as usuario_interno")
            )
            ->where('te.activo', 1);
    
        // Aplicar filtro de estado si se recibe
        if (!empty($estadoFiltro)) {
            $query->whereRaw('LOWER(e.nombre) LIKE LOWER(?)', ["%{$estadoFiltro}%"]);
        }
    
        // Aplicar búsqueda si se recibe
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('m.id_tramite', 'like', "%{$searchValue}%")
                  ->orWhere('m.cuenta', 'like', "%{$searchValue}%")
                  ->orWhere('c.nombre', 'like', "%{$searchValue}%")
                  ->orWhere('tt.nombre', 'like', "%{$searchValue}%")
                  ->orWhere('e.nombre', 'like', "%{$searchValue}%")
                  ->orWhere(DB::raw("CONCAT(ce.nombre, ' ', ce.apellido)"), 'like', "%{$searchValue}%")
                  ->orWhere(DB::raw("CONCAT(u.nombre, ' ', u.apellido)"), 'like', "%{$searchValue}%");
            });
        }
    
        // Ordenar los resultados
        $query->orderBy($columnName, $columnSortOrder);
    
        // Obtener total de registros después del filtro
        $totalFiltered = $query->count();
    
        // Paginación
        $data = $query->skip($request->get('start', 0))->take($request->get('length', 10))->get();
    
        // Obtener total de registros sin filtro
        $totalData = DB::table('multinota as m')
            ->join('tramite_estado_tramite as te', 'm.id_tramite', '=', 'te.id_tramite')
            ->where('te.activo', 1)
            ->count();
    
        // Respuesta en formato JSON
        return response()->json([
            "draw" => intval($request->get('draw', 1)),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }
        

    public function show($idTramite)
    {
        $detalleTramite = DB::table('multinota_seccion_valor as ms')
            ->join('seccion as s', 'ms.id_seccion', '=', 's.id_seccion')
            ->join('campo as c', 'ms.id_campo', '=', 'c.id_campo')
            ->select('ms.id_multinota_seccion_valor', 's.titulo', 'c.nombre', 'ms.valor')
            ->where('ms.id_tramite', $idTramite)
            ->orderBy('ms.id_multinota_seccion_valor', 'asc')
            ->get();

        $tramiteInfo = DB::table('multinota as m')
            ->where('m.id_tramite', $idTramite)
            ->join('tipo_tramite_multinota as ttm', 'm.id_tipo_tramite_multinota', '=', 'ttm.id_tipo_tramite_multinota')
            ->select('ttm.nombre', 'm.fecha_alta')
            ->first();

        $historialTramite = DB::table('historial_tramite as h')
            ->join('evento as e', 'h.id_evento', '=', 'e.id_evento')
            ->join('usuario_interno as u', 'h.id_usuario_interno_asignado', '=', 'u.id_usuario_interno')
            ->selectRaw('COALESCE(e.descripcion, e.desc_contrib) AS descripcion, e.fecha_alta, e.clave, u.legajo, CONCAT(u.nombre, u.apellido) as usuario')
            ->where('h.id_tramite', $idTramite)
            ->orderBy('e.fecha_alta', 'desc') // Ordenar por fecha de forma descendente
            ->get();

        $tramiteArchivo = DB::table('archivo as a')
            ->join('tramite_archivo as ta', 'a.id_archivo', '=','ta.id_archivo')
            ->select('a.id_archivo', 'a.fecha_alta', 'a.nombre', 'a.descripcion', 'a.path_archivo')
            ->where('ta.id_tramite', $idTramite)
            ->orderBy('a.descripcion')
            ->get();

        return view('tramites.detalle', compact('detalleTramite', 'idTramite', 'tramiteInfo', 'historialTramite', 'tramiteArchivo'));
    }

    public function darDeBaja(Request $request)
    {
        try {
            $idTramite = $request->input('idTramite');
    
            DB::beginTransaction(); // 1️⃣ Iniciar transacción
    
            // 2️⃣ Actualizar el trámite
            DB::table('tramite')
                ->where('id_tramite', $idTramite)
                ->update([
                    'flag_cancelado' => 1,
                    'flag_ingreso' => 1,
                    'fecha_modificacion' => now()
                ]);
    
            // 3️⃣ Insertar el evento
            $idEvento = DB::table('evento')->insertGetId([
                'descripcion' => 'Se dio de baja el trámite',
                'fecha_alta' => now(),
                'fecha_modificacion' => now(),
                'id_tipo_evento' => 14,
                'clave' => 'CANCELAR'
            ]);
    
            // 4️⃣ Registrar en historial_tramite
            DB::table('historial_tramite')->insert([
                'fecha' => now(),
                'id_tramite' => $idTramite,
                'id_evento' => $idEvento
            ]);
    
            DB::commit(); // 5️⃣ Confirmar cambios
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack(); // 6️⃣ Revertir cambios en caso de error
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
}
