    @extends('navbar')

    @section('heading')
    <!-- En el <head> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .btn-expandible {
            overflow: hidden;
            white-space: nowrap;
            transition: width 0.3s ease, padding 0.3s ease;
            width: 40px; /* solo ícono por defecto */
        }

        .btn-expandible i {
            margin-right: 0;
            transition: margin 0.3s ease;
        }

        .btn-expandible span {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-expandible:hover {
            width: auto;
            padding-right: 1rem;
        }

        .btn-expandible:hover span {
            opacity: 1;
            margin-left: 0.5rem;
        }

        .btn-expandible {
            overflow: hidden;
            white-space: nowrap;
            transition: width 0.3s ease, padding 0.3s ease;
            width: 40px;
        }

        .btn-expandible i {
            margin-right: 0;
            transition: margin 0.3s ease;
        }

        .btn-expandible span {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-expandible:hover {
            width: auto;
            padding-right: 1rem;
        }

        .btn-expandible:hover span {
            opacity: 1;
            margin-left: 0.5rem;
        }

        .btn-naranja {
            background-color: #fd7e14;
            color: white;
        }

        .btn-naranja:hover {
            background-color: #e66900;
            color: white;
        }

        .btn-celeste {
            background-color: #0dcaf0;
            color: white;
        }

        .btn-celeste:hover {
            background-color: #0bbbd4;
            color: white;
        }
    </style>

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
                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-expandible">
                        <i class="fas fa-arrow-left"></i><span> Volver</span>
                    </a>
                    <button class="btn btn-naranja btn-expandible" data-bs-toggle="modal" data-bs-target="#modalReasignarTramite" data-id="{{ $idTramite }}">
                        <i class="fas fa-random"></i><span> Reasignar</span>
                    </button>
                    <button class="btn btn-warning btn-expandible" data-bs-toggle="modal" data-bs-target="#modalCambiarPrioridad">
                        <i class="fas fa-hourglass-half"></i><span> Prioridad</span>
                    </button>
                    <button class="btn btn-primary btn-expandible" onclick="tomarTramite({{ $idTramite }})">
                        <i class="fas fa-sign-out-alt"></i><span> Tomar</span>
                    </button>
                    @if(Session::get('usuario_interno')->id_usuario_interno === $tramiteInfo->id_asignado)
                        <button class="btn btn-success btn-expandible" onclick="completarEstado({{ $idTramite }})">
                            <i class="bi bi-check2-square"></i><span> Completar Estado</span>
                        </button>
                    @endif
                    <button class="btn btn-danger btn-expandible" onclick="darDeBajaTramite({{ $idTramite }})">
                        <i class="fas fa-ban"></i><span> Baja</span>
                    </button>
                    <button class="btn btn-celeste btn-expandible" onclick="window.open('{{ route('reporte.constancia', ['idTramite' => $idTramite]) }}', '_blank')">
                        <i class="fas fa-print"></i><span> Imprimir</span>
                    </button>
                </div>
            </div>

            <div class="col-md-6 col-lg-6 mt-4">
                <div class="container-fluid px-3">
                    <!-- Información del trámite -->
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center fs-5" colspan="4">Información</th>
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
                                <tr><th class="text-center fs-5">Comentarios</th></tr>
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

            <!-- Columna derecha: Adjuntos + Historial -->
            <div class="col-md-6 col-lg-6 mt-4">
                <div class="container-fluid px-3">
                    <!-- Adjuntos -->
                    <div class="table-responsive" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr><th class="text-center fs-5" colspan="4">Adjuntos</th></tr>
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
                    @if(Session::get('usuario_interno')->id_usuario_interno === $tramiteInfo->id_asignado)
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
                    @endif

                    <!-- Historial -->
                    <div class="container mt-4">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr><th colspan="4" class="text-center fs-5">Historial</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($historialTramite as $evento)
                                        <tr>
                                            <td>
                                                @if($evento->clave === 'COMPLETAR_ESTADO')
                                                    Se completó el estado <strong>{{ $evento->nombre_estado }}</strong>
                                                @elseif($evento->clave === 'AVANCE_ESTADO')
                                                    Se avanzó al estado <strong>{{ $evento->nombre_estado }}</strong>
                                                @else
                                                    {{ $evento->descripcion }}
                                                @endif
                                            </td>
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

    <!-- Modal Reasignar Trámite -->
    <div class="modal fade" id="modalReasignarTramite" tabindex="-1" aria-labelledby="modalReasignarTramiteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Reasignar Trámite</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
            <form id="formReasignarTramite">
            @csrf
            <input type="hidden" name="idTramite" id="idTramiteReasignar">
            <div class="form-group">
                <label for="usuarioSelect">Seleccione un usuario:</label>
                <select class="form-control" id="usuarioSelect" name="id_usuario_interno">
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id_usuario_interno }}">{{ $usuario->apellido }} {{ $usuario->nombre }}</option>
                @endforeach
                </select>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="reasignarTramite()">Reasignar</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Modal selección de estado -->
    <div class="modal fade" id="modalSeleccionEstado" tabindex="-1" aria-labelledby="modalSeleccionEstadoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalSeleccionEstadoLabel">Seleccionar próximo estado</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
            <select id="selectEstadoSiguiente" class="form-select">
            <!-- Opciones dinámicas -->
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="confirmarAvanceEstado()">Confirmar</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
        </div>
    </div>
    </div>

    @endsection

    @section('scripting')
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $(".alert").fadeOut(500, function () {
                    $(this).remove();
                });
            }, 2000);
        });

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

        $('#modalReasignarTramite').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const idTramite = button.data('id');
            $('#idTramiteReasignar').val(idTramite);
        });

        function reasignarTramite() {
            const data = $('#formReasignarTramite').serialize();

            $.post("{{ route('tramites.reasignar') }}", data, function(response) {
                if (response.success) {
                    $('#modalReasignarTramite').modal('hide');
                    location.reload();
                } else {
                    alert("Error al reasignar trámite.");
                }
            });
        }

        let tramiteSeleccionado = null;

        function completarEstado(idTramite) {
            tramiteSeleccionado = idTramite;
            $.ajax({
                url: '{{ route("tramites.completar") }}',
                method: 'POST',
                data: {
                    idTramite: idTramite,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.requires_selection) {
                        // Mostrar modal con las opciones
                        let select = $('#selectEstadoSiguiente');
                        select.empty();
                        response.siguientes_estados.forEach(function (estado) {
                            select.append(`<option value="${estado}">Estado ${estado}</option>`);
                        });
                        $('#modalSeleccionEstado').modal('show');
                    } else if (response.success) {
                        location.reload(); // o mostrar un toast de éxito
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Error en la solicitud');
                }
            });
        }

        function confirmarAvanceEstado() {
            let idEstado = $('#selectEstadoSiguiente').val();
            if (!idEstado) {
                alert('Debe seleccionar un estado');
                return;
            }

            $.ajax({
                url: '{{ route("tramites.completar") }}',
                method: 'POST',
                data: {
                    idTramite: tramiteSeleccionado,
                    id_estado_tramite_siguiente: idEstado,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        $('#modalSeleccionEstado').modal('hide');
                        location.reload(); // o mostrar un toast
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert('Error al completar el estado');
                }
            });
        }

    </script>
    @endsection
