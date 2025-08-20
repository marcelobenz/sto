<?php
namespace App\Services;

use App\Models\Tramite;

use App\Models\TramiteEstadoTramite;
use App\Enums\TipoEstadoEnum;
use App\Repositories\TramiteRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\DTOs\EstadoTramiteDTO;
use App\Http\Controllers\AsignableATramiteController;


class TramiteService {
      protected $tramiteRepository;
      protected $asignableController;

    public function __construct(TramiteRepository $tramiteRepository)
    {
        $this->tramiteRepository = $tramiteRepository;
    }

    public function getTramitesDataForDataTable(array $requestData, bool $soloIniciados)
    {
        $columnIndex = $requestData['order'][0]['column'] ?? 0;
        
        $params = [
            'columnIndex' => $columnIndex,
            'columnName' => $requestData['columns'][$columnIndex]['data'] ?? 'id_tramite',
            'columnSortOrder' => $requestData['order'][0]['dir'] ?? 'asc',
            'searchValue' => $requestData['search']['value'] ?? '',
            'start' => $requestData['start'] ?? 0,
            'length' => $requestData['length'] ?? 10
        ];

        $result = $this->tramiteRepository->getTramitesData($params, $soloIniciados);

        return [
            "draw" => intval($requestData['draw']),
            "recordsTotal" => $result['totalData'],
            "recordsFiltered" => $result['totalFiltered'],
            "data" => $result['data']
        ];
    }

    public function getTramiteDetails($idTramite)
    {
        return $this->tramiteRepository->getTramiteDetails($idTramite);
    }

    public function darDeBajaTramite($idTramite)
    {
        $idUsuario = Session::get('usuario_interno')->id_usuario_interno ?? 107;
        return $this->tramiteRepository->darDeBajaTramite($idTramite, $idUsuario);
    }

    public function cambiarPrioridad($idTramite, $idPrioridad)
    {
        $idUsuario = Session::get('usuario_interno')->id_usuario_interno ?? 107;
        return $this->tramiteRepository->cambiarPrioridadTramite($idTramite, $idPrioridad, $idUsuario);
    }

    public function tomarTramite($idTramite)
    {
        $idUsuario = Session::get('usuario_interno')->id_usuario_interno;
        return $this->tramiteRepository->tomarTramite($idTramite, $idUsuario);
    }

    public function getPosiblesEstados($idTramite)
    {
        return $this->tramiteRepository->getPosiblesEstados($idTramite);
    }

public function avanzarEstado($idTramite, $idEstadoNuevo)
{
    $estadoActual = $this->tramiteRepository->getUltimoEstadoTramite($idTramite);

    if (!$estadoActual) {
        \Log::warning('No se encontró estado actual para el trámite', ['idTramite' => $idTramite]);
        return false;
    }

    if ($idEstadoNuevo) {
        $siguienteEstadoId = (int)$idEstadoNuevo;
    } 

    if (!$siguienteEstadoId) {
        \Log::warning('No se encontró siguiente estado', ['idTramite' => $idTramite]);
        return false;
    }

    $siguienteEstado = DB::table('estado_tramite')
        ->where('id_estado_tramite', $siguienteEstadoId)
        ->first();

    if (!$siguienteEstado) {
        \Log::warning('No se encontró el registro del siguiente estado', ['siguienteEstadoId' => $siguienteEstadoId]);
        return false;
    }

    $idUsuarioRecomendado = app(\App\Http\Controllers\AsignableATramiteController::class)
        ->recomendadoPorId($siguienteEstado->id_estado_tramite);

    $idUsuarioAsignado = $idUsuarioRecomendado ?: Session::get('usuario_interno')->id_usuario_interno;
    $idUsuarioEjecutor = Session::get('usuario_interno')->id_usuario_interno;

    return DB::transaction(function () use ($idTramite, $siguienteEstadoId, $idUsuarioAsignado, $idUsuarioEjecutor, $idUsuarioRecomendado) {
        return $this->tramiteRepository->crearEstadoTramite(
            $idTramite, 
            $siguienteEstadoId, 
            $idUsuarioAsignado, 
            $idUsuarioEjecutor, 
            $idUsuarioRecomendado
        );
    });
}



  public function cantidadDeTramites(int $usuarioId): int {
    return TramiteEstadoTramite::query()
      // usuario en cuestión
      ->where('id_usuario_interno', $usuarioId)

      // registro aún no marcado como completo
      ->where('completo', 0)

      // TRÁMITE: no rechazado ni cancelado
      ->whereHas('tramite', fn ($q) => $q
          ->where('flag_rechazado', 0)
          ->where('flag_cancelado', 0)
      )

      // ESTADO_TRAMITE: distinto de A_FINALIZAR
      ->whereHas('estadoTramite', fn ($q) => $q
          ->where('tipo', '!=', TipoEstadoEnum::A_FINALIZAR)   // o 'A_FINALIZAR' como string si no usas enum
      )

      ->count();
  }
}