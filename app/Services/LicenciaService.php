<?php
namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

use App\DTOs\UsuarioInternoDTO;
use App\Models\Licencia;
use App\Repositories\LicenciaRepository;


class LicenciaService {

 protected $licenciaRepository;

    public function __construct(LicenciaRepository $licenciaRepository)
    {
        $this->licenciaRepository = $licenciaRepository;
    }

  public function estaDeLicencia(UsuarioInternoDTO $usuario): bool {
    return Licencia::where('id_usuario_interno', $usuario->id)
      ->whereDate('fecha_inicio', '<=', Carbon::today())
      ->whereDate('fecha_fin',    '>=', Carbon::today())
      ->exists();
  }
  
  private function obtenerPorIdUsuario(int $usuarioId): Collection {
    return Licencia::where('id_usuario_interno', $usuarioId)->get();
  }

  private function sacarLicenciasIniciadasDespuesDeLaFechaActual(Collection $licencias): Collection {
    return $licencias->reject(
      fn (Licencia $l) => $l->fecha_inicio->greaterThan(Carbon::today())
    );
  }

  private function sacarLicenciasFinalizadasAntesDeLaFechaActual(Collection $licencias): Collection {
    return $licencias->reject(
      fn (Licencia $l) => $l->fecha_fin->lessThan(Carbon::today())
    );
  }

  public function estaDeLicenciaJavaStyle(UsuarioInternoDTO $usuario): bool {
    $licencias = $this->obtenerPorIdUsuario($usuario->id);

    $licenciasFiltradas = $this->sacarLicenciasFinalizadasAntesDeLaFechaActual(
      $this->sacarLicenciasIniciadasDespuesDeLaFechaActual($licencias)
    );

    return $licenciasFiltradas->isNotEmpty();
  }

  public function obtenerDatosParaFormulario($idUsuario)
    {
        $usuario = $this->licenciaRepository->findUsuarioById($idUsuario);
        $historial = $this->licenciaRepository->getHistorialLicenciasUsuario($idUsuario);

        return compact('usuario', 'historial');
    }

    public function guardarLicencia($idUsuario, array $data)
    {
        $data['id_usuario_interno'] = $idUsuario;
        return $this->licenciaRepository->createLicencia($data);
    }


}