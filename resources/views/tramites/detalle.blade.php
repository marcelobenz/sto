@extends('navbar')

@section('heading')
<!-- En el <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Antes del cierre de </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('contenidoPrincipal')
<br><br>

<div class="container"> 
    <div class="row align-items-start">
        <div class="col" id="datosTramite">
            <div class="container-fluid px-3">
                <div class="container mt-4">
                    <h4>
                        Trámite Nro {{ $idTramite }} - {{ optional($tramiteInfo)->nombre ?? 'Sin nombre' }} - {{ optional($tramiteInfo)->fecha_alta ? date('d/m/Y', strtotime($tramiteInfo->fecha_alta)) : 'Sin fecha' }}
                    </h4>

                    <!-- Bootstrap Accordion -->
                    <div class="accordion" id="accordionTramite">
                        @php
                            $grupoDetalles = $detalleTramite->groupBy('titulo'); // Agrupar por título
                            $first = true; // Variable de control para la primera iteración
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
                            @php $first = false; @endphp <!-- Desactivamos la expansión después de la primera iteración -->
                        @endforeach
                    </div>

                    <a href="{{ route('tramites.index') }}" class="btn btn-secondary mt-3">Volver</a>
                </div>
            </div>
        </div>

        <div class="col" id="comentarios">
            <div class="container-fluid px-3">
                <div class="container mt-4">
                    <h4>Comentarios</h4>
                </div>
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

            </div>
        </div>
    </div>
</div>

@endsection
