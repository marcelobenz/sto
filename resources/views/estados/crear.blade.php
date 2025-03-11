@extends('navbar')

@section('heading')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('contenidoPrincipal')
    <div class="container-fluid px-3">
        <br/>
        <br/>
        <br/>
        <h2 class="mt-3">Crear Workflow de Estados - {{ $tipoTramite->nombre_tipo_tramite }}</h2>

        <div class="row">
            <!-- Sección de Estados -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Estados del Trámite</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Actuales</th>
                                    <th>Nuevos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estados as $estado)
                                    <tr>
                                        <td>{{ $estado['actual'] }}</td>
                                        <td>{{ $estado['nuevo'] }}</td>
                                        <td>
                                            <button class="estado-btn btn btn-sm btn-info fa fa-search" 
                                                    data-estado="{{ $estado['actual'] }}">
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ url('/estados') }}" class="btn btn-secondary mt-3">Volver</a>
                    </div>
                </div>
            </div>

            <!-- Sección de Relaciones -->
            <div class="col-md-8">
                <div id="seccion-relaciones" class="card" style="display: none;">
                    <div class="card-header">
                        <h5>Relaciones</h5>
                    </div>
                    <div class="card-body">
                        <label><input type="radio" name="relacion" value="anterior"> Anterior</label>
                        <label><input type="radio" name="relacion" value="posterior" checked> Posterior</label>
                        <select class="form-control mt-2">
                        <option>Seleccionar...</option>
                        @foreach($estados as $estado)
                        <option value="{{ $estado['actual'] }}">{{ $estado['nuevo'] }}</option>
                        @endforeach
                        </select>
                        <button class="btn btn-primary mt-2">Agregar</button>
                        <div class="mt-3">
                            <h6>Anteriores</h6>
                            <ul class="list-group"><li class="list-group-item">En Creación</li></ul>
                            <h6>Posteriores</h6>
                            <ul class="list-group"><li class="list-group-item">En Análisis</li></ul>
                        </div>
                    </div>
                </div>


                <!-- Sección de Restricciones -->
                <div id="seccion-restricciones" class="card mt-3" style="display: none;">
                    <div class="card-header">
                        <h5>Restricciones</h5>
                    </div>
                    <div class="card-body">
                        <label><input type="checkbox"> Puede rechazar</label><br>
                        <label><input type="checkbox" checked> Puede pedir documentación</label><br>
                        <label><input type="checkbox"> Tiene Expediente</label>
                    </div>
                </div>

                <!-- Sección de Responsables -->
                <div id="seccion-responsables" class="card mt-3" style="display: none;">
                    <div class="card-header">
                        <h5>Responsables</h5>
                    </div>
                    <div class="card-body">
                        <input type="text" class="form-control mb-2" placeholder="Buscar...">
                        <table class="table table-bordered">
                            <thead>
                                <tr><th>Legajo</th><th>Nombre</th><th>Área</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>999999999</td><td>Mesa de Entrada</td><td>ATENCION AL USUARIO</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripting')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Secciones
        let seccionRelaciones = document.getElementById("seccion-relaciones");
        let seccionRestricciones = document.getElementById("seccion-restricciones");
        let seccionResponsables = document.getElementById("seccion-responsables");

        // Asegurarse de que todas las secciones estén ocultas al inicio
        seccionRelaciones.style.display = "none";
        seccionRestricciones.style.display = "none";
        seccionResponsables.style.display = "none";

        // Cuando se haga clic en los botones de estado
        let botonesEstado = document.querySelectorAll(".estado-btn");

        botonesEstado.forEach(button => {
            button.addEventListener("click", function () {
                let estado = this.getAttribute("data-estado");

                // Mostrar las secciones de Relaciones y Responsables cuando se haga clic en un estado
                seccionRelaciones.style.display = "block";
                seccionResponsables.style.display = "block";

                // Mostrar u ocultar la sección de Restricciones dependiendo del estado
                if (estado === "En Creación") {
                    seccionRestricciones.style.display = "none";
                } else {
                    seccionRestricciones.style.display = "block";
                }
            });
        });
    });
</script>
@endsection