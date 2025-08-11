<?php

namespace App\Services;

use App\Repositories\AdministrarWorkflowRepository;
use Illuminate\Support\Facades\Log;

class AdministrarWorkflowService
{
    protected $repository;

    public function __construct(AdministrarWorkflowRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obtenerDatosParaDataTable()
    {
        return $this->repository->obtenerTramitesParaDataTable();
    }

    public function prepararDatosParaCrear($idTipoTramite)
    {
        $tipoTramite = $this->repository->obtenerTipoTramite($idTipoTramite);
        
        $estados = [
            ['actual' => 'En Creación', 'nuevo' => 'En Creación'],
            ['actual' => 'Iniciado', 'nuevo' => 'Iniciado'],
            ['actual' => 'En Análisis', 'nuevo' => 'En Análisis'],
            ['actual' => 'En Aprobación', 'nuevo' => 'En Aprobación'],
            ['actual' => 'A Finalizar', 'nuevo' => 'A Finalizar'],
        ];

        return [
            'tipoTramite' => $tipoTramite,
            'estados' => $estados
        ];
    }

    public function prepararDatosParaEditar($idTipoTramite, $publico = 1, $activo = 1)
    {
        $tipoTramite = $this->repository->obtenerTipoTramite($idTipoTramite);
        $estadosDB = $this->repository->obtenerEstadosConfigurados($idTipoTramite, $publico, $activo);
        
        $posteriores = $this->repository->obtenerEstadosPosteriores($idTipoTramite, $publico)
            ->groupBy('id_estado_tramite');
            
        $asignaciones = $this->repository->obtenerAsignaciones($estadosDB->pluck('id_estado_tramite'))
            ->groupBy('id_estado_tramite');

        $estados = $estadosDB->map(function ($estado) use ($posteriores, $asignaciones) {
            return [
                'estado_actual' => (string) $estado->nombre,
                'posteriores' => isset($posteriores[$estado->id_estado_tramite])
                    ? $posteriores[$estado->id_estado_tramite]->map(function ($p) {
                        return ['nombre' => $p->nombre_posterior];
                    })->values()->toArray()
                    : [],
                'puede_rechazar' => $estado->puede_rechazar,
                'puede_pedir_documentacion' => $estado->puede_pedir_documentacion,
                'estado_tiene_expediente' => $estado->estado_tiene_expediente,
                'asignaciones' => isset($asignaciones[$estado->id_estado_tramite])
                    ? $asignaciones[$estado->id_estado_tramite]->map(function ($asig) {
                        return [
                            'id_grupo_interno' => $asig->id_grupo_interno,
                            'id_usuario_interno' => $asig->id_usuario_interno,
                        ];
                    })->values()->toArray()
                    : [],
            ];
        });

        return [
            'tipoTramite' => $tipoTramite,
            'estados' => $estados
        ];
    }

    public function guardarWorkflow($idTipoTramite, $configuraciones, $publico = 1, $activo = 1)
    {
        try {
            $definiciones = $this->normalizarConfiguraciones($configuraciones);
            
            $mapaEstados = $this->repository->crearNuevosEstados($definiciones);
            
            $configuracionesParaInsertar = $this->prepararConfiguraciones($configuraciones, $mapaEstados);
            
            $version = uniqid();
            
            if ($publico) {
                $this->repository->desactivarConfiguracionesAnteriores($idTipoTramite);
            }
            
            $this->repository->crearConfiguraciones($configuracionesParaInsertar, $version, $idTipoTramite, $publico, $activo);
            
            $asignaciones = $this->prepararAsignaciones($configuraciones, $mapaEstados);
            $this->repository->crearAsignaciones($asignaciones);
            
            if ($publico) {
                $this->repository->actualizarTramitesActivos($idTipoTramite, $mapaEstados);
                
                $versionesExistentes = $this->repository->obtenerVersionesExistentes($idTipoTramite);
                $versionesPublicas = $versionesExistentes->where('publico', 1)->values();
                
                if ($versionesPublicas->count() > 2) {
                    $versionesAEliminar = $versionesPublicas->slice(2)->pluck('version');
                    $this->repository->eliminarVersionesAntiguas($idTipoTramite, $versionesAEliminar);
                }
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error al guardar workflow', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function guardarBorrador($idTipoTramite, $configuraciones)
    {
        $this->repository->eliminarBorradores($idTipoTramite);
        
        return $this->guardarWorkflow($idTipoTramite, $configuraciones, 0, 0);
    }

    public function publicarBorrador($idTipoTramite, $configuraciones)
    {
        try {
            $resultado = $this->guardarWorkflow($idTipoTramite, $configuraciones);
            
            if ($resultado) {
                $this->repository->eliminarBorradores($idTipoTramite);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('Error al publicar borrador', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    protected function normalizarConfiguraciones($configuraciones)
    {
        $definiciones = collect();
        
        foreach ($configuraciones as $conf) {
            if (!is_array($conf) || !isset($conf['estado_actual'])) {
                throw new \Exception("Configuración inválida");
            }
            
            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            $definiciones->put($nombreEstado, [
                'puede_rechazar' => $this->parseBool($conf['puede_rechazar'] ?? 0),
                'puede_pedir_documentacion' => $this->parseBool($conf['puede_pedir_documentacion'] ?? 0),
                'estado_tiene_expediente' => $this->parseBool($conf['estado_tiene_expediente'] ?? 0)
            ]);
            
            if (isset($conf['posteriores']) && is_array($conf['posteriores'])) {
                foreach ($conf['posteriores'] as $post) {
                    if (!is_array($post)) {
                        continue;
                    }
                    
                    $nombrePosterior = $this->normalizarNombreEstado($post['nombre'] ?? '');
                    if (!empty($nombrePosterior)) {
                        $definiciones->put($nombrePosterior, [
                            'puede_rechazar' => 0,
                            'puede_pedir_documentacion' => 0,
                            'estado_tiene_expediente' => 0
                        ]);
                    }
                }
            }
        }
        
        return $definiciones->toArray();
    }

    protected function prepararConfiguraciones($configuraciones, $mapaEstados)
    {
        $configuracionesParaInsertar = [];
        
        foreach ($configuraciones as $conf) {
            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            $idEstado = $mapaEstados[$nombreEstado] ?? null;
            
            if (!$idEstado) {
                continue;
            }
            
            $item = [
                'id_estado' => $idEstado,
                'posteriores' => []
            ];
            
            if (isset($conf['posteriores']) && is_array($conf['posteriores'])) {
                foreach ($conf['posteriores'] as $post) {
                    $nombrePosterior = $this->normalizarNombreEstado($post['nombre'] ?? '');
                    if (isset($mapaEstados[$nombrePosterior])) {
                        $item['posteriores'][] = ['id' => $mapaEstados[$nombrePosterior]];
                    }
                }
            }
            
            $configuracionesParaInsertar[] = $item;
        }
        
        return $configuracionesParaInsertar;
    }

    protected function prepararAsignaciones($configuraciones, $mapaEstados)
    {
        $asignaciones = [];
        $now = now();
        
        foreach ($configuraciones as $conf) {
            $nombreEstado = $this->normalizarNombreEstado($conf['estado_actual']);
            $idEstado = $mapaEstados[$nombreEstado] ?? null;
            
            if (!$idEstado || empty($conf['asignaciones']) || !is_array($conf['asignaciones'])) {
                continue;
            }
            
            foreach ($conf['asignaciones'] as $asig) {
                if (!is_array($asig)) {
                    continue;
                }
                
                $grupoId = isset($asig['id_grupo_interno']) ? (int)$asig['id_grupo_interno'] : null;
                $usuarioId = isset($asig['id_usuario_interno']) ? (int)$asig['id_usuario_interno'] : null;
                
                if ($grupoId !== null || $usuarioId !== null) {
                    $asignaciones[] = [
                        'fecha_sistema' => $now,
                        'id_estado_tramite' => $idEstado,
                        'id_grupo_interno' => $grupoId,
                        'id_usuario_interno' => $usuarioId,
                    ];
                }
            }
        }
        
        return $asignaciones;
    }

    protected function normalizarNombreEstado($nombre)
    {
        if (is_array($nombre)) {
            throw new \Exception("Nombre de estado no puede ser un array");
        }

        $nombre = (string) $nombre;

        if (empty(trim($nombre))) {
            throw new \Exception("Nombre de estado no puede estar vacío");
        }

        return trim($nombre);
    }

    protected function parseBool($value)
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }
        
        return $value ? 1 : 0;
    }
}