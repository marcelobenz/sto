<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Multinota;
use App\Models\EstadoTramite;
use App\Models\UsuarioInterno;

class TramiteEstadoTramite extends Model {
  use HasFactory;

  // Define the table name if it's not the plural of the model name
  protected $table = 'tramite_estado_tramite';

  // Define the primary key
  protected $primaryKey = 'id_tramite_estado_tramite';

  public $incrementing = true;

  // Define the key type
  protected $keyType = 'int';

  // Define fillable attributes for mass assignment
  protected $fillable = [
    'id_estado_tramite',
    'id_tramite_estado_tramite_anterior',
    'id_estado_tramite_siguiente',
    'id_usuario_interno',
    'fecha_sistema',
    'activo',
    'completo',
    'reiniciado',
    'espera_documentacion',
    'id_tramite'
  ];

  // Disable timestamps if you are managing them manually
  public $timestamps = false;

  public function tramite() {
    return $this->belongsTo(Multinota::class, 'id_tramite', 'id_tramite');
  }

  public function estadoTramite() {
    return $this->belongsTo(EstadoTramite::class, 'id_estado_tramite', 'id_estado_tramite');
  }

  public function usuario() {
    return $this->belongsTo(UsuarioInterno::class, 'id_usuario_interno', 'id_usuario_interno');
  }

  public function estadoTramiteSiguiente() {
    return $this->belongsTo(EstadoTramite::class, 'id_estado_tramite_siguiente', 'id_estado_tramite');
  }
}
