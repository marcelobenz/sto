<?php

namespace App\Services;

use App\Repositories\CuestionarioRepository;
use App\Models\Pregunta;

class CuestionarioService
{
   protected $repository;

    public function __construct(CuestionarioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllCuestionarios()
    {
        return $this->repository->getAll();
    }

    public function getDataForCreateView()
    {
        $agrupado = $this->repository->getEstadosAgrupados();
        $tipos = $this->repository->getTiposTramite(array_keys($agrupado));
        
        return compact('agrupado', 'tipos');
    }

    public function getDataForEditView($id)
    {
        $cuestionario = $this->repository->findWithPreguntas($id);
        $agrupado = $this->repository->getEstadosAgrupados();
        $tipos = $this->repository->getTiposTramite(array_keys($agrupado));
        $estadosSeleccionados = $this->repository->getEstadosSeleccionados($id);

        return compact('cuestionario', 'agrupado', 'tipos', 'estadosSeleccionados');
    }

    public function createCuestionario(array $data)
    {
        $cuestionario = $this->repository->create($data);

        if (isset($data['preguntas'])) {
            $this->savePreguntas($cuestionario->id_cuestionario, json_decode($data['preguntas'], true));
        }

        if (isset($data['tipo_tramite_multinota'])) {
            $this->repository->syncEstadosTramite($cuestionario->id_cuestionario, $data['tipo_tramite_multinota']);
        }

        return $cuestionario;
    }

    public function updateCuestionario($id, array $data)
    {
        $cuestionario = $this->repository->update($id, $data);

        if (isset($data['preguntas'])) {
            $this->updatePreguntas($data['preguntas']);
        }

        if (isset($data['nuevas_preguntas'])) {
            $this->savePreguntas($cuestionario->id_cuestionario, $data['nuevas_preguntas']);
        }

        if (isset($data['tipo_tramite_multinota'])) {
            $this->repository->syncEstadosTramite($cuestionario->id_cuestionario, $data['tipo_tramite_multinota']);
        }

        return $cuestionario;
    }

    public function toggleCuestionarioStatus($id, $activate)
    {
        return $this->repository->toggleStatus($id, $activate ? 0 : 1);
    }

    protected function savePreguntas($cuestionarioId, array $preguntasData)
    {
        foreach ($preguntasData as $preguntaData) {
            Pregunta::create([
                'id_cuestionario' => $cuestionarioId,
                'fecha_sistema' => now(),
                'descripcion' => $preguntaData['texto'] ?? $preguntaData['descripcion'],
                'flag_detalle_si' => ($preguntaData['siDetalle'] ?? false) ? 1 : 0,
                'flag_detalle_no' => ($preguntaData['noDetalle'] ?? false) ? 1 : 0,
                'flag_finalizacion_si' => ($preguntaData['finalizaSi'] ?? false) ? 1 : 0,
                'flag_rechazo_no' => ($preguntaData['rechazaNo'] ?? false) ? 1 : 0,
                'flag_baja' => 0
            ]);
        }
    }

    protected function updatePreguntas(array $preguntasData)
    {
        foreach ($preguntasData as $idPregunta => $preguntaData) {
            Pregunta::where('id_pregunta', $idPregunta)->update([
                'descripcion' => $preguntaData['descripcion'],
                'flag_detalle_si' => isset($preguntaData['siDetalle']) ? 1 : 0,
                'flag_detalle_no' => isset($preguntaData['noDetalle']) ? 1 : 0,
                'flag_finalizacion_si' => isset($preguntaData['finalizaSi']) ? 1 : 0,
                'flag_rechazo_no' => isset($preguntaData['rechazaNo']) ? 1 : 0
            ]);
        }
    }
}