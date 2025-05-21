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
                <button class="btn btn-primary" onclick="tomarTramite({{ $idTramite }})" title="Tomar"><i class="fas fa-sign-out-alt"></i></button>
                <button class="btn btn-danger" onclick="darDeBajaTramite({{ $idTramite }})" title="Dar de baja"><i class="fas fa-ban"></i></button>
                <button class="btn btn-danger" onclick="window.open('{{ route('reporte.constancia', ['idTramite' => $idTramite]) }}', '_blank')" title="Imprimir"><i class="fas fa-print"></i></button>
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

        <!-- Columna derecha: Adjuntos + Historial -->
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
</script>
@endpush
