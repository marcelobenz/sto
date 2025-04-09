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

<!-- Botón Guardar -->
<div class="mt-3 text-right">
    <button id="btn-guardar-configuracion" class="btn btn-success">
        <i class="fas fa-save"></i> Guardar Configuración
    </button>
</div>


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
                        <select id="select-estado" class="form-control mt-2">
                            <option>Seleccionar...</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado['actual'] }}">{{ $estado['nuevo'] }}</option>
                            @endforeach
                        </select>
                        <button id="btn-agregar" class="btn btn-primary mt-2">Agregar</button>
                        <div class="mt-3">
                            <h6>Anteriores</h6>
                            <ul id="lista-anteriores" class="list-group"></ul>
                            <h6>Posteriores</h6>
                            <ul id="lista-posteriores" class="list-group"></ul>
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
                        @include('components.treeview-usuarios', ['grupos' => $grupos])
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripting')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // Objeto para almacenar las configuraciones de cada estado
    let configuraciones = {};

    // Variable para almacenar el estado actual seleccionado
    let estadoActualSeleccionado = null;

    // Cuando se haga clic en los botones de estado
    let botonesEstado = document.querySelectorAll(".estado-btn");

    botonesEstado.forEach(button => {
        button.addEventListener("click", function () {
            // Obtener el estado actual seleccionado
            estadoActualSeleccionado = this.getAttribute("data-estado");

            // Mostrar las secciones de Relaciones y Responsables cuando se haga clic en un estado
            seccionRelaciones.style.display = "block";
            seccionResponsables.style.display = "block";

            // Mostrar u ocultar la sección de Restricciones dependiendo del estado
            if (estadoActualSeleccionado === "En Creación") {
                seccionRestricciones.style.display = "none";
            } else {
                seccionRestricciones.style.display = "block";
            }

            // Restablecer las listas de Anteriores y Posteriores
            document.getElementById("lista-anteriores").innerHTML = "";
            document.getElementById("lista-posteriores").innerHTML = "";

            // Cargar la configuración del estado seleccionado (si existe)
            if (configuraciones[estadoActualSeleccionado]) {
                configuraciones[estadoActualSeleccionado].anteriores.forEach(item => {
                    let nuevoItem = document.createElement("li");
                    nuevoItem.className = "list-group-item";
                    nuevoItem.textContent = item;
                    document.getElementById("lista-anteriores").appendChild(nuevoItem);
                });

                configuraciones[estadoActualSeleccionado].posteriores.forEach(item => {
                    let nuevoItem = document.createElement("li");
                    nuevoItem.className = "list-group-item";
                    nuevoItem.textContent = item;
                    document.getElementById("lista-posteriores").appendChild(nuevoItem);
                });
            }
        });
    });

    // Lógica para agregar estados a las listas de Anteriores o Posteriores
    document.getElementById("btn-agregar").addEventListener("click", function () {
        let selectEstado = document.getElementById("select-estado");
        let estadoSeleccionado = selectEstado.options[selectEstado.selectedIndex].text;
        let relacion = document.querySelector('input[name="relacion"]:checked').value;

        if (estadoSeleccionado !== "Seleccionar..." && estadoActualSeleccionado) {
            let lista = relacion === "anterior" ? document.getElementById("lista-anteriores") : document.getElementById("lista-posteriores");
            let nuevoItem = document.createElement("li");
            nuevoItem.className = "list-group-item";
            nuevoItem.textContent = estadoSeleccionado;
            lista.appendChild(nuevoItem);

            // Guardar la configuración en el objeto configuraciones
            if (!configuraciones[estadoActualSeleccionado]) {
                configuraciones[estadoActualSeleccionado] = { anteriores: [], posteriores: [] };
            }

            if (relacion === "anterior") {
                configuraciones[estadoActualSeleccionado].anteriores.push(estadoSeleccionado);
            } else {
                configuraciones[estadoActualSeleccionado].posteriores.push(estadoSeleccionado);
            }
        }
    });

    document.getElementById("btn-guardar-configuracion").addEventListener("click", function () {
    if (!Object.keys(configuraciones).length) {
        Swal.fire("Error", "No se ha configurado ningún estado.", "error");
        return;
    }

    const payload = [];

    Object.entries(configuraciones).forEach(([estadoActual, config]) => {
        payload.push({
            estado_actual: estadoActual,
            posteriores: config.posteriores.map(nombre => ({ nombre }))
        });
    });

    fetch("{{ route('workflow.guardar', ['id' => $tipoTramite->id_tipo_tramite_multinota]) }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ configuraciones: payload })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: "Éxito",
                text: data.message,
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = "/estados";
            });
        } else {
            Swal.fire("Error", data.message || "Ocurrió un error", "error");
        }
    })
    .catch(error => {
        console.error(error);
        Swal.fire("Error", "No se pudo guardar la configuración.", "error");
     });
    });
});
</script>
@endsection