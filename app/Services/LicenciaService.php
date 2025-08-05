<?php
namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

use App\DTOs\UsuarioInternoDTO;
use App\Models\Licencia;

class LicenciaService {
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
}