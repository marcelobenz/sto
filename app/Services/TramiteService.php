<?php
namespace App\Services;

use App\Models\Tramite;

use App\Models\TramiteEstadoTramite;
use App\Enums\TipoEstadoEnum;
use App\Repositories\TramiteRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class TramiteService {
      protected $tramiteRepository;

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

public function avanzarEstado($idTramite)
{
    return DB::transaction(function () use ($idTramite) {
        $estadoActual = $this->tramiteRepository->getUltimoEstadoTramite($idTramite);
        if (!$estadoActual) {
            return false;
        }

        $siguienteEstado = $this->tramiteRepository->getSiguienteEstado($estadoActual->id_estado_tramite);
        if (!$siguienteEstado) {
            return false;
        }

        return $this->tramiteRepository->crearEstadoTramite($idTramite, $siguienteEstado);
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