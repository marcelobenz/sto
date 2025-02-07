@extends('navbar')

@section('heading')
<!-- En el <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<!-- Antes del cierre de </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('contenidoPrincipal')
<br><br>

<div class="container-fluid">
    <div class="row gx-4 align-items-start mt-4">
        <h4>
            Trámite Nro {{ $idTramite }} - {{ optional($tramiteInfo)->nombre ?? 'Sin nombre' }} - 
            {{ optional($tramiteInfo)->fecha_alta ? date('d/m/Y', strtotime($tramiteInfo->fecha_alta)) : 'Sin fecha' }}
        </h4>

        <!-- Columna izquierda: Datos del Trámite -->
        <div class="col-md-6 col-lg-6" id="datosTramite">
            <div class="container-fluid px-3">
                <div class="container mt-4">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center fs-4" colspan="4">Información</th>
                            </tr>
                        </thead>
                    </table>


                    <!-- Bootstrap Accordion -->
                    <div class="accordion" id="accordionTramite">
                        @php
                            $grupoDetalles = $detalleTramite->groupBy('titulo');
                            $first = true;
                        @endphp

                        @foreach($grupoDetalles as $titulo => $detalles)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ Str::slug($titulo) }}">
                                    <button class="accordion-button {{ $first ? '' : 'collapsed' }}" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ Str::slug($titulo) }}" 
                                        aria-expanded="{{ $first ? 'true' : 'false' }}" 
                                        aria-controls="collapse{{ Str::slug($titulo) }}">
                                        {{ $titulo }}
                                    </button>
                                </h2>
                                <div id="collapse{{ Str::slug($titulo) }}" class="accordion-collapse collapse {{ $first ? 'show' : '' }}" 
                                    aria-labelledby="heading{{ Str::slug($titulo) }}" 
                                    data-bs-parent="#accordionTramite">
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

                    <div class="container mt-4">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center fs-4" colspan="4">Adjuntos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tramiteArchivo as $tramiteArchivo)
                                    <tr>
                                        <td>{{ $tramiteArchivo->fecha_alta }}</td>
                                        <td>{{ $tramiteArchivo->nombre }}</td>
                                        <td>{{ $tramiteArchivo->descripcion }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('archivo.descargar', $tramiteArchivo->id_archivo) }}" class="icono-descarga" title="Descargar">
                                                <i class="bi bi-download fs-5"></i>
                                            </a>
                                        </td>                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <a href="{{ route('tramites.index') }}" class="btn btn-secondary mt-3">Volver</a>
                </div>
            </div>
        </div>

        <!-- Columna derecha: Comentarios e Historial -->
        <div class="col-md-6 col-lg-6 mt-4" id="comentarios">
            <div class="container-fluid px-3">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center fs-4">Comentarios</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                <!-- Formulario para agregar comentario -->
                                <form action="{{ route('comentario.guardar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id_tramite" value="{{ $idTramite }}">
                                    <input type="hidden" name="id_usuario_administador" value="{{ auth()->user()->id ?? '152' }}">

                                    <div class="mb-3">
                                        <label for="comentario_{{ Str::slug($titulo) }}" class="form-label">Agregar Comentario:</label>
                                        <textarea class="form-control" name="mensaje" id="comentario_{{ Str::slug($titulo) }}" rows="3" required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Aceptar</button>
                                </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Sección del Historial -->
                <div class="container mt-4">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px;">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th colspan="3" class="text-center fs-4">Historial</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historialTramite as $evento)
                                    <tr>
                                        <td>{{ $evento->descripcion }}</td>
                                        <td><span class="badge bg-primary">{{ $evento->clave }}</span></td>
                                        <td>{{ date('d/m/Y H:i', strtotime($evento->fecha_alta)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay eventos en el historial.</td>
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

@endsection
