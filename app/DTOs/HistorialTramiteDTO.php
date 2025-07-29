<?php

namespace App\DTOs;

use Illuminate\Support\Carbon;

class HistorialTramiteDTO
{
    // ───────────────────────────────────
    // Propiedades privadas
    // ───────────────────────────────────
    private ?int $id_historial_tramite = null;

    private ?Carbon $fecha = null;

    private ?string $mensaje = null;

    private int $id_tramite;      // ✅ OBLIGATORIO

    private int $id_evento;       // ✅ OBLIGATORIO

    private ?int $id_estado_actual = null;

    private ?int $id_estado_anterior = null;

    private ?int $id_estado_tramite = null;

    private ?int $id_usuario_administrador = null;

    private ?int $id_usuario_asignado = null;

    private ?int $id_usuario_interno_administrador = null;

    private ?int $id_usuario_interno_asignado = null;

    // ───────────────────────────────────
    // Constructor (solo dos obligatorios)
    // ───────────────────────────────────
    public function __construct(int $id_evento, int $id_tramite)
    {
        $this->id_evento = $id_evento;
        $this->id_tramite = $id_tramite;
    }

    // ───────────────────────────────────
    // Getters
    // ───────────────────────────────────
    public function getIdHistorialTramite(): ?int
    {
        return $this->id_historial_tramite;
    }

    public function getFecha(): ?Carbon
    {
        return $this->fecha;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function getIdTramite(): int
    {
        return $this->id_tramite;
    }

    public function getIdEvento(): int
    {
        return $this->id_evento;
    }

    public function getIdEstadoActual(): ?int
    {
        return $this->id_estado_actual;
    }

    public function getIdEstadoAnterior(): ?int
    {
        return $this->id_estado_anterior;
    }

    public function getIdEstadoTramite(): ?int
    {
        return $this->id_estado_tramite;
    }

    public function getIdUsuarioAdministrador(): ?int
    {
        return $this->id_usuario_administrador;
    }

    public function getIdUsuarioAsignado(): ?int
    {
        return $this->id_usuario_asignado;
    }

    public function getIdUsuarioInternoAdministrador(): ?int
    {
        return $this->id_usuario_interno_administrador;
    }

    public function getIdUsuarioInternoAsignado(): ?int
    {
        return $this->id_usuario_interno_asignado;
    }

    // ───────────────────────────────────
    // Setters (devuelven void, estilo clásico)
    // ───────────────────────────────────
    public function setIdHistorialTramite(int $id): void
    {
        $this->id_historial_tramite = $id;
    }

    public function setFecha(Carbon $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function setMensaje(string $mensaje): void
    {
        $this->mensaje = $mensaje;
    }

    public function setIdEstadoActual(?int $id): void
    {
        $this->id_estado_actual = $id;
    }

    public function setIdEstadoAnterior(?int $id): void
    {
        $this->id_estado_anterior = $id;
    }

    public function setIdEstadoTramite(?int $id): void
    {
        $this->id_estado_tramite = $id;
    }

    public function setIdUsuarioAdministrador(?int $id): void
    {
        $this->id_usuario_administrador = $id;
    }

    public function setIdUsuarioAsignado(?int $id): void
    {
        $this->id_usuario_asignado = $id;
    }

    public function setIdUsuarioInternoAdministrador(?int $id): void
    {
        $this->id_usuario_interno_administrador = $id;
    }

    public function setIdUsuarioInternoAsignado(?int $id): void
    {
        $this->id_usuario_interno_asignado = $id;
    }

    // ───────────────────────────────────
    // Helper opcional: exportar a array
    // ───────────────────────────────────
    public function toArray(): array
    {
        return [
            'id_historial_tramite' => $this->id_historial_tramite,
            'fecha' => $this->fecha?->toDateTimeString(),
            'mensaje' => $this->mensaje,
            'id_tramite' => $this->id_tramite,
            'id_evento' => $this->id_evento,
            'id_estado_actual' => $this->id_estado_actual,
            'id_estado_anterior' => $this->id_estado_anterior,
            'id_estado_tramite' => $this->id_estado_tramite,
            'id_usuario_administrador' => $this->id_usuario_administrador,
            'id_usuario_asignado' => $this->id_usuario_asignado,
            'id_usuario_interno_administrador' => $this->id_usuario_interno_administrador,
            'id_usuario_interno_asignado' => $this->id_usuario_interno_asignado,
        ];
    }
}
