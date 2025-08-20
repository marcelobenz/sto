@extends('navbar')

@section('heading')
<!-- En el <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('contenidoPrincipal')
<br><br>
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row gx-4 align-items-start mt-4">
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap bg-light p-3 rounded">
            <div class="d-flex flex-grow-1 gap-2">
                <div class="border rounded p-2 flex-grow-1 bg-white">
                    Trámite Nro <strong>{{ $idTramite }}</strong>
                    <span class="mx-1">-</span>
                    {{ $tramiteInfo->nombre ?? 'Sin nombre' }}
                    <span class="mx-1">-</span>
                    {{ $tramiteInfo->fecha_alta ? date('d/m/Y', strtotime($tramiteInfo->fecha_alta)) : 'Sin fecha' }}
                </div>
                <div class="border rounded p-2 flex-grow-1 bg-white">
                    Estado actual:
                    <span class="badge 
                        @if($tramiteInfo->estado_actual === 'Aprobado') bg-success
                        @elseif($tramiteInfo->estado_actual === 'Rechazado') bg-danger
                        @elseif($tramiteInfo->estado_actual === 'Dado de Baja') bg-secondary
                        @elseif($tramiteInfo->estado_actual === 'Iniciado') bg-primary
                        @elseif($tramiteInfo->estado_actual === 'Finalizado') bg-success
                        @else bg-dark
                        @endif">
                        {{ $tramiteInfo->estado_actual ?? 'Desconocido' }}
                    </span>
                </div>
                <div class="border rounded p-2 flex-grow-1 bg-white">
                    Prioridad:
                    <span class="badge
                        @if(strtolower($tramiteInfo->prioridad) === 'baja') bg-success
                        @elseif(strtolower($tramiteInfo->prioridad) === 'normal') bg-warning text-dark
                        @elseif(strtolower($tramiteInfo->prioridad) === 'alta') bg-danger
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($tramiteInfo->prioridad) ?? 'Sin prioridad' }}
                    </span>
                </div>

                <div class="border rounded p-2 flex-grow-1 bg-white">
                    Asignado a: <strong>{{ $tramiteInfo->nombre_usuario }} {{ $tramiteInfo->apellido_usuario }}</strong>
                </div>
            </div>
            <div class="ms-3 d-flex gap-1">
                <button class="btn btn-warning" title="Reasignar"><i class="fas fa-random"></i></button>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCambiarPrioridad" title="Cambiar prioridad"><i class="fas fa-exclamation"></i></button>
                 @if(Session::has('usuario_interno') && isset($tramiteInfo->legajo))
                    @php
                        $usuarioSesion = Session::get('usuario_interno');
                    @endphp
                @if($usuarioSesion->legajo != $tramiteInfo->legajo)
                <button class="btn btn-primary" onclick="tomarTramite({{ $idTramite }})" title="Tomar"><i class="fas fa-sign-out-alt"></i></button>
                @endif
                @if($usuarioSesion->legajo == $tramiteInfo->legajo)
                <button class="btn btn-success" onclick="avanzarEstado({{ $idTramite }})" title="Avanzar Estado"><i class="fas fa-arrow-right"></i></button>
                @endif
                @endif
                <button class="btn btn-danger" onclick="darDeBajaTramite({{ $idTramite }})" title="Dar de baja"><i class="fas fa-ban"></i></button>
                <button class="btn btn-danger" onclick="window.open('{{ route('reporte.constancia', ['idTramite' => $idTramite]) }}', '_blank')" title="Imprimir"><i class="fas fa-print"></i></button>
            </div>
        </div>



        <!-- Modal Selección Estado -->
<div class="modal fade" id="modalSeleccionEstado" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formSeleccionEstado">
      @csrf
      <input type="hidden" name="id_tramite" id="id_tramite_modal">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Seleccionar nuevo estado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <select class="form-select" name="id_estado_nuevo" id="select_estado" required>
            <option value="" disabled selected>Seleccione un estado</option>
            <!-- Options will be added dynamically -->
          </select>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Aceptar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</div>


        <div class="col-md-6 col-lg-6 mt-4">
            <div class="container-fluid px-3">
                <!-- Información del trámite -->
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center fs-4" colspan="4">Información</th>
                        </tr>
                    </thead>
                </table>

                <div class="accordion mb-4" id="accordionTramite">
                    @php
                        $grupoDetalles = $detalleTramite->groupBy('titulo');
                        $first = false;
                    @endphp
                    @foreach($grupoDetalles as $titulo => $detalles)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ Str::slug($titulo) }}">
                                <button class="accordion-button {{ $first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($titulo) }}" aria-expanded="{{ $first ? 'true' : 'false' }}" aria-controls="collapse{{ Str::slug($titulo) }}">
                                    {{ $titulo }}
                                </button>
                            </h2>
                            <div id="collapse{{ Str::slug($titulo) }}" class="accordion-collapse collapse {{ $first ? 'show' : '' }}" aria-labelledby="heading{{ Str::slug($titulo) }}" data-bs-parent="#accordionTramite">
                                <div class="accordion-body">
                                    <table class="table table-bordered">
                                        <tbody>
                                            @foreach($detalles as $detalle)
                                                <tr>
                                                    <td>{{ $detalle->nombre }}</td>
                                                    <td>{{ $detalle->valor }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @php $first = false; @endphp
                    @endforeach
                </div>

                <!-- Comentarios -->
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr><th class="text-center fs-4">Comentarios</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                <form action="{{ route('comentario.guardar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_tramite" value="{{ $idTramite }}">
                                    <input type="hidden" name="id_usuario_administador" value="{{ auth()->user()->id ?? '152' }}">
                                    <div class="mb-3">
                                        <label class="form-label">Agregar Comentario:</label>
                                        <textarea class="form-control" name="mensaje" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Aceptar</button>
                                </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Columna derecha: Adjuntos + Cuestionarios + Historial -->
        <div class="col-md-6 col-lg-6 mt-4">
            <div class="container-fluid px-3">
                <!-- Adjuntos -->
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr><th class="text-center fs-4" colspan="4">Adjuntos</th></tr>
                        </thead>
                        <tbody>
                            @foreach($tramiteArchivo as $tramiteArchivo)
                                <tr>
                                    <td>{{ $tramiteArchivo->fecha_alta }}</td>
                                    <td>{{ $tramiteArchivo->nombre }}</td>
                                    <td>{{ $tramiteArchivo->descripcion }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('archivo.descargar', $tramiteArchivo->id_archivo) }}" title="Descargar">
                                            <i class="bi bi-download fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Subir archivo -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('tramites.index') }}" class="btn btn-secondary">Volver</a>
                    <form action="{{ route('archivo.subir') }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center">
                        @csrf
                        <input type="hidden" name="id_tramite" value="{{ $idTramite }}">
                        <div class="input-group">
                            <input type="file" name="archivo" class="form-control" required>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-upload"></i> Subir Archivo
                            </button>
                        </div>
                    </form>
                </div>

             
<!-- Cuestionarios - Mostrar siempre que haya preguntas o respuestas -->
@if($preguntas->isNotEmpty() || $respuestasCuestionario->isNotEmpty())
<div class="container mt-4">
    <div class="table-responsive" style="max-height: 350px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th colspan="2" class="text-center fs-4">Cuestionarios</th>
                </tr>
                @if($preguntas->where('es_editable', 1)->isEmpty() && $respuestasCuestionario->isNotEmpty())
                <tr>
                    <td colspan="2" class="text-center text-warning bg-light">
                        <small><i class="fas fa-info-circle"></i> Respuestas históricas (solo lectura)</small>
                    </td>
                </tr>
                @endif
            </thead>
            <tbody>
                @if($preguntas->isNotEmpty())
                <form action="{{ route('cuestionarios.guardar') }}" method="POST" id="formCuestionarios">
                    @csrf
                    <input type="hidden" name="id_tramite" value="{{ $idTramite }}">
                    
                    @foreach($preguntas as $pregunta)
                    @php
                        $respuestaExistente = $respuestasCuestionario->has($pregunta->id_pregunta) 
                            ? $respuestasCuestionario->get($pregunta->id_pregunta) 
                            : null;
                        
                        $respuestaValor = $respuestaExistente ? $respuestaExistente->flag_valor : '';
                        $detalleRespuesta = $respuestaExistente ? $respuestaExistente->detalle : '';
                        
                        $mostrarDetalleSi = $pregunta->flag_detalle_si == 1;
                        $mostrarDetalleNo = $pregunta->flag_detalle_no == 1;
                        
                        $mostrarTextarea = false;
                        if ($respuestaValor == 1 && $mostrarDetalleSi) {
                            $mostrarTextarea = true;
                        } elseif ($respuestaValor == 0 && $mostrarDetalleNo) {
                            $mostrarTextarea = true;
                        }
                        
                        $esEditable = $pregunta->es_editable ?? 1;
                    @endphp
                    
                    <tr>
                        <td class="align-middle" style="width: 70%;">
                            <strong>{{ $pregunta->descripcion }}</strong>
                            @if(!$esEditable)
                            <br><small class="text-warning"><i class="fas fa-lock"></i> Respuesta histórica</small>
                            @endif
                        </td>
                        <td class="align-middle" style="width: 30%;">
                            @if($esEditable)
                            <!-- Campo editable -->
                            <select name="respuestas[{{ $pregunta->id_pregunta }}]" class="form-select form-select-sm mb-2" 
                                    data-flag-detalle-si="{{ $pregunta->flag_detalle_si }}"
                                    data-flag-detalle-no="{{ $pregunta->flag_detalle_no }}"
                                    onchange="toggleDetalle({{ $pregunta->id_pregunta }}, this.value)">
                                <option value="">Seleccionar...</option>
                                <option value="1" {{ $respuestaValor == 1 ? 'selected' : '' }}>SI</option>
                                <option value="0" {{ $respuestaValor == 0 ? 'selected' : '' }}>NO</option>
                            </select>
                            @else
                            <!-- Campo solo lectura -->
                            <div class="form-control form-control-sm bg-light mb-2">
                                {{ $respuestaValor == 1 ? 'SI' : ($respuestaValor == 0 ? 'NO' : 'Sin respuesta') }}
                            </div>
                            @endif
                            
                            @if($mostrarDetalleSi || $mostrarDetalleNo)
                            <div id="detalle_{{ $pregunta->id_pregunta }}" style="display: {{ $mostrarTextarea ? 'block' : 'none' }};">
                                @if($esEditable)
                                <textarea name="detalles[{{ $pregunta->id_pregunta }}]" 
                                          class="form-control form-control-sm" 
                                          rows="2" 
                                          placeholder="Ingrese el detalle...">{{ $detalleRespuesta }}</textarea>
                                @else
                                <div class="form-control form-control-sm bg-light" style="min-height: 60px;">
                                    {{ $detalleRespuesta ?: 'Sin detalle' }}
                                </div>
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($preguntas->where('es_editable', 1)->isNotEmpty())
                    <tr>
                        <td colspan="2" class="text-center">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save"></i> Guardar Respuestas
                            </button>
                            <button type="button" class="btn btn-warning btn-sm ms-2" onclick="limpiarCuestionario()">
                                <i class="fas fa-eraser"></i> Limpiar Respuestas
                            </button>
                        </td>
                    </tr>
                    @endif
                </form>
                @elseif($respuestasCuestionario->isNotEmpty())
                <!-- Solo mostrar respuestas históricas -->
                @foreach($respuestasCuestionario as $respuesta)
                @php
                    $preguntaInfo = DB::table('pregunta')->where('id_pregunta', $respuesta->id_pregunta_cuestionario)->first();
                @endphp
                @if($preguntaInfo)
                <tr>
                    <td class="align-middle" style="width: 70%;">
                        <strong>{{ $preguntaInfo->descripcion }}</strong>
                        <br><small class="text-warning"><i class="fas fa-lock"></i> Respuesta histórica</small>
                    </td>
                    <td class="align-middle" style="width: 30%;">
                        <div class="form-control form-control-sm bg-light mb-2">
                            {{ $respuesta->flag_valor == 1 ? 'SI' : ($respuesta->flag_valor == 0 ? 'NO' : 'Sin respuesta') }}
                        </div>
                        
                        @if($respuesta->detalle)
                        <div class="form-control form-control-sm bg-light" style="min-height: 60px;">
                            {{ $respuesta->detalle }}
                        </div>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
@else
<!-- Mensaje cuando no hay cuestionarios -->
<div class="container mt-4">
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle"></i>
        No hay cuestionarios configurados para este trámite.
    </div>
</div>
@endif

                <!-- Historial -->
                <div class="container mt-4">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr><th colspan="4" class="text-center fs-4">Historial</th></tr>
                            </thead>
                            <tbody>
                                @forelse($historialTramite as $evento)
                                    <tr>
                                        <td>{{ $evento->descripcion }}</td>
                                        <td><span class="badge bg-primary">{{ $evento->clave }}</span></td>
                                        <td>{{ date('d/m/Y H:i', strtotime($evento->fecha_alta)) }}</td>
                                        <td class="text-center">{{ $evento->legajo }} - {{ $evento->usuario }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No hay eventos en el historial.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cambiar Prioridad -->
<div class="modal fade" id="modalCambiarPrioridad" tabindex="-1" aria-labelledby="modalCambiarPrioridadLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('tramites.cambiarPrioridad') }}">
            @csrf
            <input type="hidden" name="id_tramite" value="{{ $idTramite }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Prioridad del Trámite</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="id_prioridad">Seleccione nueva prioridad:</label>
                    <select name="id_prioridad" id="id_prioridad" class="form-select" required>
                        @foreach($prioridades as $prioridad)
                            <option value="{{ $prioridad->id_prioridad }}">{{ $prioridad->nombre }} </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>

    console.log('Información del trámite:', @json($tramiteInfo));
    $(document).ready(function () {
        setTimeout(function () {
            $(".alert").fadeOut(500, function () {
                $(this).remove();
            });
        }, 2000);
    });

    function toggleDetalle(idPregunta, respuesta) {
        const detalleDiv = document.getElementById('detalle_' + idPregunta);
        const textarea = detalleDiv.querySelector('textarea');
        
        if (respuesta === 'SI_CON_DETALLE' || respuesta === 'NO_CON_DETALLE') {
            detalleDiv.style.display = 'block';
            textarea.required = true;
        } else {
            detalleDiv.style.display = 'none';
            textarea.required = false;
            textarea.value = '';
        }
    }

    function darDeBajaTramite(idTramite) {
        if (confirm("¿Estás seguro de que deseas dar de baja este trámite?")) {
            fetch("{{ route('tramites.darDeBaja') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ idTramite })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert("Error: " + data.message);
                });
        }
    }

    function tomarTramite(idTramite) {
        if (confirm("¿Estás seguro de que deseas tomar este trámite?")) {
            fetch("{{ route('tramites.tomarTramite') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ idTramite })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert("Error: " + data.message);
                });
        }
    }


let modalSeleccionEstado;

function avanzarEstado(idTramite) {
    fetch("{{ route('tramites.getPosiblesEstados') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ idTramite })
    })
    .then(res => res.json())
    .then(data => {
        if (data.estados.length === 1) {
            if (confirm("¿Avanzar el trámite al siguiente estado?")) {
                fetch("{{ route('tramites.avanzarEstado') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ 
                        idTramite: idTramite,
                        idEstadoNuevo: data.estados[0].id_estado_tramite
                    })
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.success) location.reload();
                    else alert(resp.message);
                });
            }
        } else if (data.estados.length > 1) {
            let select = document.getElementById("select_estado");
            select.innerHTML = "";
            
            let defaultOpt = document.createElement("option");
            defaultOpt.value = "";
            defaultOpt.textContent = "Seleccione un estado";
            defaultOpt.disabled = true;
            defaultOpt.selected = true; 
            select.appendChild(defaultOpt);

            data.estados.forEach(e => {
                let opt = document.createElement("option");
                opt.value = e.id_estado_tramite;
                opt.textContent = e.nombre_estado;
                select.appendChild(opt);
            });

            document.getElementById("id_tramite_modal").value = idTramite;

            // Crear la instancia del modal si no existe
            if (!modalSeleccionEstado) {
                modalSeleccionEstado = new bootstrap.Modal(document.getElementById("modalSeleccionEstado"));
                
                // Agregar event listeners para los botones de cerrar
                const modalElement = document.getElementById("modalSeleccionEstado");
                
                // Botón X (close)
                const btnClose = modalElement.querySelector(".btn-close");
                if (btnClose) {
                    btnClose.addEventListener("click", function() {
                        modalSeleccionEstado.hide();
                    });
                }
                
                // Botón Cancelar
                const btnCancel = modalElement.querySelector('.btn-secondary[data-bs-dismiss="modal"]');
                if (btnCancel) {
                    btnCancel.addEventListener("click", function() {
                        modalSeleccionEstado.hide();
                    });
                }
                
                // Click fuera del modal
                modalElement.addEventListener("click", function(e) {
                    if (e.target === modalElement) {
                        modalSeleccionEstado.hide();
                    }
                });
                
                // Tecla ESC
                document.addEventListener("keydown", function(e) {
                    if (e.key === "Escape" && modalSeleccionEstado._isShown) {
                        modalSeleccionEstado.hide();
                    }
                });
            }
            
            modalSeleccionEstado.show();
        } else {
            alert("No hay configuraciones de avance disponibles.");
        }
    });
}

document.getElementById("formSeleccionEstado").addEventListener("submit", function(e) {
    e.preventDefault();
    
    let idTramite = document.getElementById("id_tramite_modal").value;
    let idEstadoNuevo = document.getElementById("select_estado").value;

    console.log("Valor seleccionado:", idEstadoNuevo);

    fetch("{{ route('tramites.avanzarEstado') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ 
            idTramite: idTramite, 
            idEstadoNuevo: idEstadoNuevo 
        })
    })
    .then(r => r.json())
    .then(resp => {
        // Cerrar el modal ANTES de verificar la respuesta
        if (modalSeleccionEstado) {
            modalSeleccionEstado.hide();
        }
        
        if (resp.success) {
            // Pequeño delay para asegurar que el modal se cierre completamente
            setTimeout(() => {
                location.reload();
            }, 300);
        } else {
            alert(resp.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Cerrar el modal incluso si hay error
        if (modalSeleccionEstado) {
            modalSeleccionEstado.hide();
        }
        alert('Error al procesar la solicitud');
    });
});

document.getElementById('formCuestionarios').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const idTramite = formData.get('id_tramite');
    
    // Recopilar todas las respuestas y detalles
    const data = {
        id_tramite: idTramite,
        respuestas: {},
        detalles: {}
    };
    
    // Obtener todas las respuestas
    document.querySelectorAll('select[name^="respuestas"]').forEach(select => {
        const name = select.getAttribute('name');
        const match = name.match(/\[(\d+)\]/);
        if (match) {
            const idPregunta = match[1];
            data.respuestas[idPregunta] = select.value;
        }
    });
    
    // Obtener todos los detalles
    document.querySelectorAll('textarea[name^="detalles"]').forEach(textarea => {
        const name = textarea.getAttribute('name');
        const match = name.match(/\[(\d+)\]/);
        if (match) {
            const idPregunta = match[1];
            data.detalles[idPregunta] = textarea.value;
        }
    });
    
    // Validar que todas las preguntas obligatorias tengan respuesta
    let todasRespondidas = true;
    let mensajeError = '';
    
    document.querySelectorAll('select[name^="respuestas"]').forEach(select => {
        if (!select.value) {
            todasRespondidas = false;
            const preguntaText = select.closest('tr').querySelector('strong').textContent;
            mensajeError += `- ${preguntaText}\n`;
        }
    });
    
    if (!todasRespondidas) {
        alert('Por favor, responda todas las preguntas:\n' + mensajeError);
        return;
    }
    
    // Validar detalles cuando son requeridos según los flags
    let detallesFaltantes = false;
    let mensajeDetallesError = '';
    
    document.querySelectorAll('select[name^="respuestas"]').forEach(select => {
        const name = select.getAttribute('name');
        const match = name.match(/\[(\d+)\]/);
        if (match) {
            const idPregunta = match[1];
            const respuesta = select.value;
            
            // Obtener los flags de la pregunta
            const flagDetalleSi = parseInt(select.getAttribute('data-flag-detalle-si'));
            const flagDetalleNo = parseInt(select.getAttribute('data-flag-detalle-no'));
            
            // Verificar si se requiere detalle
            if ((respuesta === '1' && flagDetalleSi === 1) || (respuesta === '0' && flagDetalleNo === 1)) {
                const detalleTextarea = document.querySelector(`textarea[name="detalles[${idPregunta}]"]`);
                if (!detalleTextarea || !detalleTextarea.value.trim()) {
                    detallesFaltantes = true;
                    const preguntaText = select.closest('tr').querySelector('strong').textContent;
                    mensajeDetallesError += `- ${preguntaText} requiere un detalle\n`;
                }
            }
        }
    });
    
    if (detallesFaltantes) {
        alert('Las siguientes preguntas requieren detalles:\n' + mensajeDetallesError);
        return;
    }
    
    // Mostrar indicador de carga
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        submitBtn.disabled = true;
    }
    
    // Enviar datos al servidor
    fetch("{{ route('cuestionarios.guardar') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        // Restaurar botón
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Respuestas';
            submitBtn.disabled = false;
        }
        
        if (data.success) {
            // Mostrar mensaje de éxito
            showAlert('success', data.message);
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Restaurar botón
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Respuestas';
            submitBtn.disabled = false;
        }
        
        showAlert('error', 'Error al guardar las respuestas');
    });
});

// Función para mostrar alertas bonitas
function showAlert(type, message) {
    // Crear elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Agregar al documento
    document.body.appendChild(alertDiv);
    
    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Función para mostrar/ocultar campos de detalle
function toggleDetalle(idPregunta, respuesta) {
    const detalleDiv = document.getElementById('detalle_' + idPregunta);
    if (!detalleDiv) return;
    
    // Verificar si el campo es editable
    const textarea = detalleDiv.querySelector('textarea');
    const divReadonly = detalleDiv.querySelector('.form-control.bg-light');
    
    // Solo procesar si es editable (textarea existe)
    if (textarea) {
        const select = document.querySelector(`select[name="respuestas[${idPregunta}]"]`);
        const flagDetalleSi = parseInt(select.getAttribute('data-flag-detalle-si'));
        const flagDetalleNo = parseInt(select.getAttribute('data-flag-detalle-no'));
        
        const respuestaNum = parseInt(respuesta);
        
        if ((respuestaNum === 1 && flagDetalleSi === 1) || (respuestaNum === 0 && flagDetalleNo === 1)) {
            detalleDiv.style.display = 'block';
            textarea.required = true;
        } else {
            detalleDiv.style.display = 'none';
            textarea.required = false;
            textarea.value = '';
        }
    }
}

// Hacer las funciones disponibles globalmente
window.toggleDetalle = toggleDetalle;

// Inicializar los detalles al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('select[name^="respuestas"]').forEach(select => {
        const name = select.getAttribute('name');
        const match = name.match(/\[(\d+)\]/);
        if (match) {
            const idPregunta = match[1];
            const respuesta = select.value;
            if (respuesta) {
                setTimeout(() => {
                    toggleDetalle(idPregunta, respuesta);
                }, 100);
            }
        }
    });
});


// Función para limpiar solo campos editables
function limpiarCuestionario() {
    if (confirm("¿Estás seguro de que deseas limpiar todas las respuestas editables del cuestionario?")) {
        let camposLimpiados = 0;
        
        // Limpiar solo selects editables
        document.querySelectorAll('select[name^="respuestas"]').forEach(select => {
            if (select.value !== '') {
                camposLimpiados++;
                select.value = '';
                
                const name = select.getAttribute('name');
                const match = name.match(/\[(\d+)\]/);
                if (match) {
                    const idPregunta = match[1];
                    const detalleDiv = document.getElementById('detalle_' + idPregunta);
                    if (detalleDiv) {
                        detalleDiv.style.display = 'none';
                    }
                }
            }
        });
        
        // Limpiar solo textareas editables
        document.querySelectorAll('textarea[name^="detalles"]').forEach(textarea => {
            if (textarea.value !== '') {
                camposLimpiados++;
                textarea.value = '';
                textarea.required = false;
            }
        });
        
        if (camposLimpiados > 0) {
            showAlert('success', `Se limpiaron ${camposLimpiados} campos editables`);
        } else {
            showAlert('info', 'No había campos editables para limpiar');
        }
    }
}

// Hacer la función disponible globalmente
window.limpiarCuestionario = limpiarCuestionario;

</script>
@endpush