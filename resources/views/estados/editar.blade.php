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
        <h2 class="mt-3">Crear Workflow de Estados - {{ $tipoTramite->nombre }}</h2>

        <x-loader />

        <div class="mt-3 text-right">
            <button id="btn-guardar-configuracion" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Configuración
            </button>
            <button id="btn-guardar-borrador" class="btn btn-warning ml-2">
                <i class="fas fa-save"></i> Guardar Borrador
            </button>
        </div>

        <div class="row">
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
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estados as $estado)
                                    <tr>
                                        <td>{{ $estado['estado_actual'] }}</td>
                                        <td>
                                            <button class="estado-btn btn btn-sm btn-info fa fa-search" 
                                                    data-estado="{{ $estado['estado_actual'] }}">
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="mt-3">
                            <button id="btn-nuevo-estado" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Agregar Estado
                            </button>

                            <div id="form-nuevo-estado" class="mt-2" style="display: none;">
                                <input type="text" id="input-nuevo-estado" class="form-control" placeholder="Nombre del nuevo estado">
                                <div class="mt-2 text-right">
                                    <button id="btn-confirmar-estado" class="btn btn-success btn-sm">Confirmar</button>
                                    <button id="btn-cancelar-estado" class="btn btn-secondary btn-sm">Cancelar</button>
                                </div>
                            </div>
                        </div>

                        <a href="{{ url('/estados') }}" class="btn btn-secondary mt-3">Volver</a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div id="seccion-relaciones" class="card" style="display: none;">
                    <div class="card-header">
                        <h5>Relaciones - <span id="estado-actual-titulo"></span></h5>
                    </div>
                    <div class="card-body">
                        <label><input type="hidden" name="relacion" value="posterior"></label>
                        <select id="select-estado" class="form-control mt-2">
                            <option>Seleccionar...</option>
                        </select>
                        <button id="btn-agregar" class="btn btn-primary mt-2">Agregar</button>
                        <div class="mt-3">
                            <h6>Posteriores</h6>
                            <ul id="lista-posteriores" class="list-group"></ul>
                        </div>
                    </div>
                </div>

                <div id="seccion-restricciones" class="card mt-3" style="display: none;">
                    <div class="card-header">
                        <h5>Restricciones</h5>
                    </div>
                    <div class="card-body">
                        <input type="checkbox" id="puede-rechazar"> Puede rechazar<br>
                        <input type="checkbox" id="puede-doc"> Puede pedir documentación<br>
                        <input type="checkbox" id="tiene-expediente"> Tiene Expediente
                    </div>
                </div>

                <div id="seccion-responsables" class="card mt-3" style="display: none;">
                    <div class="card-header">
                        <h5>Responsables</h5>
                    </div>
                    <div class="card-body">
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
    let configuraciones = {};
    
    @foreach($estados as $estado)
    configuraciones['{{ $estado['estado_actual'] }}'] = {
        nombre: '{{ $estado['estado_actual'] }}',
        tipo: '{{ $estado['tipo'] ?? strtoupper(str_replace(' ', '_', $estado['estado_actual'])) }}',
        puede_rechazar: {{ $estado['puede_rechazar'] ?? 0 }},
        puede_pedir_documentacion: {{ $estado['puede_pedir_documentacion'] ?? 0 }},
        tiene_expediente: {{ $estado['estado_tiene_expediente'] ?? 0 }},
        posteriores: {!! json_encode($estado['posteriores'] ?? []) !!},
        asignaciones: {!! json_encode($estado['asignaciones'] ?? []) !!}
    };
    @endforeach

    const seccionRelaciones = document.getElementById("seccion-relaciones");
    const seccionRestricciones = document.getElementById("seccion-restricciones");
    const seccionResponsables = document.getElementById("seccion-responsables");
    let estadoActualSeleccionado = null;

    function actualizarConfiguracionEstado(estado) {
        if (!estado) return;
        
        configuraciones[estado] = configuraciones[estado] || {
            nombre: estado,
            tipo: estado.toUpperCase().replace(/ /g, "_"),
            puede_rechazar: 0,
            puede_pedir_documentacion: 0,
            tiene_expediente: 0,
            posteriores: [],
            asignaciones: []
        };
        
        configuraciones[estado].puede_rechazar = document.getElementById('puede-rechazar').checked ? 1 : 0;
        configuraciones[estado].puede_pedir_documentacion = document.getElementById('puede-doc').checked ? 1 : 0;
        configuraciones[estado].tiene_expediente = document.getElementById('tiene-expediente').checked ? 1 : 0;
        
        const posteriores = [];
        document.querySelectorAll('#lista-posteriores li span').forEach(span => {
            posteriores.push({ nombre: span.textContent });
        });
        configuraciones[estado].posteriores = posteriores;
        
        const asignaciones = [];
        document.querySelectorAll('.usuario-checkbox:checked').forEach(checkbox => {
            asignaciones.push({
                id_usuario_interno: checkbox.dataset.usuarioId,
                id_grupo_interno: checkbox.dataset.grupoId
            });
        });
        configuraciones[estado].asignaciones = asignaciones;
    }

    function seleccionarEstado(button) {
        if (estadoActualSeleccionado) {
            actualizarConfiguracionEstado(estadoActualSeleccionado);
        }

        estadoActualSeleccionado = button.getAttribute("data-estado");
        document.getElementById('estado-actual-titulo').textContent = estadoActualSeleccionado;

        seccionRelaciones.style.display = "block";
        seccionResponsables.style.display = "block";
        seccionRestricciones.style.display = (estadoActualSeleccionado === "En Creación") ? "none" : "block";

        const selectEstado = document.getElementById("select-estado");
        selectEstado.innerHTML = '<option>Seleccionar...</option>';

        Object.keys(configuraciones).forEach(nombre => {
            if (nombre !== "En Creación" && nombre !== estadoActualSeleccionado) {
                const option = document.createElement("option");
                option.value = nombre;
                option.textContent = nombre;
                selectEstado.appendChild(option);
            }
        });

        const listaPosteriores = document.getElementById("lista-posteriores");
        listaPosteriores.innerHTML = "";
        
        const config = configuraciones[estadoActualSeleccionado] || {};
        (config.posteriores || []).forEach(item => {
            const nombrePosterior = typeof item === 'string' ? item : item.nombre;
            if (nombrePosterior) {
                const nuevoItem = document.createElement("li");
                nuevoItem.className = "list-group-item d-flex justify-content-between align-items-center";
                nuevoItem.innerHTML = `
                    <span>${nombrePosterior}</span>
                    <button class="btn btn-sm btn-danger btn-eliminar-posterior" data-nombre="${nombrePosterior}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                listaPosteriores.appendChild(nuevoItem);

                nuevoItem.querySelector(".btn-eliminar-posterior").addEventListener("click", function() {
                    const nombre = this.getAttribute("data-nombre");
                    this.closest("li").remove();
                    
                    configuraciones[estadoActualSeleccionado].posteriores = 
                        configuraciones[estadoActualSeleccionado].posteriores.filter(p => {
                            if (typeof p === 'string') return p !== nombre;
                            return p.nombre !== nombre;
                        });
                });
            }
        });

        document.getElementById("puede-rechazar").checked = config.puede_rechazar === 1;
        document.getElementById("puede-doc").checked = config.puede_pedir_documentacion === 1;
        document.getElementById("tiene-expediente").checked = config.tiene_expediente === 1;

        document.querySelectorAll('.usuario-checkbox').forEach(cb => cb.checked = false);
        (config.asignaciones || []).forEach(asignacion => {
            const selector = `.usuario-checkbox[data-usuario-id="${asignacion.id_usuario_interno}"][data-grupo-id="${asignacion.id_grupo_interno}"]`;
            const checkbox = document.querySelector(selector);
            if (checkbox) checkbox.checked = true;
        });
    }

    document.getElementById("btn-nuevo-estado").addEventListener("click", function() {
        document.getElementById("form-nuevo-estado").style.display = "block";
        this.style.display = "none";
    });

    document.getElementById("btn-cancelar-estado").addEventListener("click", function() {
        document.getElementById("form-nuevo-estado").style.display = "none";
        document.getElementById("btn-nuevo-estado").style.display = "inline-block";
        document.getElementById("input-nuevo-estado").value = "";
    });

    document.getElementById("btn-confirmar-estado").addEventListener("click", function() {
        const nuevoNombre = document.getElementById("input-nuevo-estado").value.trim();
        if (!nuevoNombre) return;

        if (configuraciones[nuevoNombre]) {
            Swal.fire("Atención", "Este estado ya existe.", "warning");
            return;
        }

        configuraciones[nuevoNombre] = {
            nombre: nuevoNombre,
            tipo: nuevoNombre.toUpperCase().replace(/ /g, "_"),
            puede_rechazar: 0,
            puede_pedir_documentacion: 0,
            tiene_expediente: 0,
            posteriores: [],
            asignaciones: []
        };

        const tbody = document.querySelector("table tbody");
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${nuevoNombre}</td>
            <td>
                <button class="estado-btn btn btn-sm btn-info fa fa-search" data-estado="${nuevoNombre}"></button>
            </td>
        `;
        tbody.appendChild(row);

        const select = document.getElementById("select-estado");
        if (select) {
            const option = document.createElement("option");
            option.value = nuevoNombre;
            option.textContent = nuevoNombre;
            select.appendChild(option);
        }

        row.querySelector(".estado-btn").addEventListener("click", function() {
            seleccionarEstado(this);
        });

        document.getElementById("input-nuevo-estado").value = "";
        document.getElementById("form-nuevo-estado").style.display = "none";
        document.getElementById("btn-nuevo-estado").style.display = "inline-block";
    });

    document.getElementById("btn-agregar").addEventListener("click", function() {
        const selectEstado = document.getElementById("select-estado");
        const estadoSeleccionado = selectEstado.options[selectEstado.selectedIndex].text;

        if (estadoSeleccionado === "Seleccionar..." || !estadoActualSeleccionado) return;

        const lista = document.getElementById("lista-posteriores");
        const yaExiste = Array.from(lista.children).some(item => 
            item.querySelector('span').textContent === estadoSeleccionado
        );
        
        if (yaExiste) return;

        const nuevoItem = document.createElement("li");
        nuevoItem.className = "list-group-item d-flex justify-content-between align-items-center";
        nuevoItem.innerHTML = `
            <span>${estadoSeleccionado}</span>
            <button class="btn btn-sm btn-danger btn-eliminar-posterior" data-nombre="${estadoSeleccionado}">
                <i class="fas fa-times"></i>
            </button>
        `;
        lista.appendChild(nuevoItem);

        if (!configuraciones[estadoActualSeleccionado]) {
            configuraciones[estadoActualSeleccionado] = {
                nombre: estadoActualSeleccionado,
                tipo: estadoActualSeleccionado.toUpperCase().replace(/ /g, "_"),
                puede_rechazar: 0,
                puede_pedir_documentacion: 0,
                tiene_expediente: 0,
                posteriores: [],
                asignaciones: []
            };
        }

        configuraciones[estadoActualSeleccionado].posteriores.push({ nombre: estadoSeleccionado });

        nuevoItem.querySelector(".btn-eliminar-posterior").addEventListener("click", function() {
            const nombre = this.getAttribute("data-nombre");
            this.closest("li").remove();
            
            configuraciones[estadoActualSeleccionado].posteriores = 
                configuraciones[estadoActualSeleccionado].posteriores.filter(p => p.nombre !== nombre);
        });
    });

    document.getElementById("btn-guardar-configuracion").addEventListener("click", function() {
        if (!Object.keys(configuraciones).length) {
            Swal.fire("Error", "No se ha configurado ningún estado.", "error");
            return;
        }

        if (estadoActualSeleccionado) {
            actualizarConfiguracionEstado(estadoActualSeleccionado);
        }

        document.getElementById("loader").style.display = "flex";

        const payload = Object.values(configuraciones).map(config => ({
            estado_actual: config.nombre,
            tipo: config.tipo,
            puede_rechazar: config.puede_rechazar,
            puede_pedir_documentacion: config.puede_pedir_documentacion,
            estado_tiene_expediente: config.tiene_expediente,
            posteriores: config.posteriores,
            asignaciones: config.asignaciones
        }));

        fetch("{{ route('workflow.guardarEdicion', ['id' => $tipoTramite->id_tipo_tramite_multinota]) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ configuraciones: payload })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("loader").style.display = "none";
            if (data.success) {
                Swal.fire({
                    title: "Éxito",
                    text: data.message,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
                setTimeout(() => window.location.href = "/estados", 2000);
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error", "No se pudo guardar la configuración", "error");
        });
    });

    document.getElementById("btn-guardar-borrador").addEventListener("click", function() {
        if (!Object.keys(configuraciones).length) {
            Swal.fire("Error", "No se ha configurado ningún estado.", "error");
            return;
        }

        if (estadoActualSeleccionado) {
            actualizarConfiguracionEstado(estadoActualSeleccionado);
        }

        const payload = Object.values(configuraciones).map(config => ({
            estado_actual: config.nombre,
            tipo: config.tipo,
            puede_rechazar: config.puede_rechazar,
            puede_pedir_documentacion: config.puede_pedir_documentacion,
            estado_tiene_expediente: config.tiene_expediente,
            posteriores: config.posteriores,
            asignaciones: config.asignaciones
        }));

        fetch("{{ route('workflow.guardarBorrador', ['id' => $tipoTramite->id_tipo_tramite_multinota]) }}", {
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
                    title: "Borrador guardado",
                    text: data.message,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error", "No se pudo guardar el borrador", "error");
        });
    });

    document.querySelectorAll(".estado-btn").forEach(button => {
        button.addEventListener("click", function() {
            seleccionarEstado(this);
        });
    });
});
</script>
@endsection

<style>
#loader {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(255, 255, 255, 0.7);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>