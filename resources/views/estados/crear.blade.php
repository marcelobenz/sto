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

<div class="mt-3 text-right">
    <button id="btn-guardar-configuracion" class="btn btn-success">
        <i class="fas fa-save"></i> Guardar Configuración
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

            <div class="col-md-8">
                <div id="seccion-relaciones" class="card" style="display: none;">
                    <div class="card-header">
                        <h5>Relaciones - <span id="estado-actual-titulo"></span></h5>
                    </div>
                    <div class="card-body">
                        <label><input type="hidden" name="relacion" value="posterior"> </label>
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
    let seccionRelaciones = document.getElementById("seccion-relaciones");
    let seccionRestricciones = document.getElementById("seccion-restricciones");
    let seccionResponsables = document.getElementById("seccion-responsables");

    seccionRelaciones.style.display = "none";
    seccionRestricciones.style.display = "none";
    seccionResponsables.style.display = "none";

    let configuraciones = {};

    let estadoActualSeleccionado = null;

    let botonesEstado = document.querySelectorAll(".estado-btn");

botonesEstado.forEach(button => {
    button.addEventListener("click", function () {
        if (estadoActualSeleccionado) {
             actualizarRestricciones(estadoActualSeleccionado);
           const usuarios = Array.from(document.querySelectorAll('.usuario-checkbox'))
            .filter(cb => cb.checked)
            .map(cb => ({
            id_usuario_interno: cb.dataset.usuarioId,
            id_grupo_interno: cb.dataset.grupoId
        }));


            configuraciones[estadoActualSeleccionado] = configuraciones[estadoActualSeleccionado] || {};
            configuraciones[estadoActualSeleccionado].asignaciones = usuarios;

            console.log("Guardando asignaciones para estado:", estadoActualSeleccionado, usuarios);
        }

        estadoActualSeleccionado = this.getAttribute("data-estado");
        document.getElementById('estado-actual-titulo').textContent = estadoActualSeleccionado;

        let selectEstado = document.getElementById("select-estado");
        selectEstado.innerHTML = '<option>Seleccionar...</option>';

        @foreach($estados as $estado)
        if ("{{ $estado['actual'] }}" !== "En Creación" && "{{ $estado['actual'] }}" !== estadoActualSeleccionado) {
            let option = document.createElement("option");
            option.value = "{{ $estado['actual'] }}";
            option.text = "{{ $estado['nuevo'] }}";
            selectEstado.appendChild(option);
        }
        @endforeach

        seccionRelaciones.style.display = "block";
        seccionResponsables.style.display = "block";
        seccionRestricciones.style.display = (estadoActualSeleccionado === "En Creación") ? "none" : "block";

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

       
            document.querySelectorAll('.usuario-checkbox').forEach(cb => {
                cb.checked = false;
            });


            console.log("Asignaciones para estado seleccionado:", estadoActualSeleccionado, config.asignaciones);

        if (config.asignaciones) {
            config.asignaciones.forEach(({ id_usuario_interno, id_grupo_interno }) => {
               const selector = `.usuario-checkbox[data-usuario-id="${id_usuario_interno}"][data-grupo-id="${id_grupo_interno}"]`;
               const checkbox = document.querySelector(selector);
                if (checkbox) checkbox.checked = true;
            });
        }
    });
});


function actualizarRestricciones(estado) {
    if (!estado) return;

    configuraciones[estado] = configuraciones[estado] || {
        posteriores: [],
        nombre: estado,
        tipo: estado.toUpperCase().replace(" ", "_"),
    };

    configuraciones[estado].puede_rechazar = document.querySelector('#puede-rechazar')?.checked ? 1 : 0;
    configuraciones[estado].puede_pedir_documentacion = document.querySelector('#puede-doc')?.checked ? 1 : 0;
    configuraciones[estado].tiene_expediente = document.querySelector('#tiene-expediente')?.checked ? 1 : 0;
}


document.getElementById("btn-agregar").addEventListener("click", function () {
    let selectEstado = document.getElementById("select-estado");
    let estadoSeleccionado = selectEstado.options[selectEstado.selectedIndex].text;

    if (estadoSeleccionado !== "Seleccionar..." && estadoActualSeleccionado) {
        configuraciones[estadoActualSeleccionado] = configuraciones[estadoActualSeleccionado] || {
            posteriores: [],
            nombre: estadoActualSeleccionado,
            tipo: estadoActualSeleccionado.toUpperCase().replace(" ", "_"),
            puede_rechazar: document.querySelector('#puede-rechazar')?.checked ? 1 : 0,
            puede_pedir_documentacion: document.querySelector('#puede-doc')?.checked ? 1 : 0,
            tiene_expediente: document.querySelector('#tiene-expediente')?.checked ? 1 : 0,
        };

        let lista = document.getElementById("lista-posteriores");

        let yaExiste = Array.from(lista.children).some(item => item.textContent === estadoSeleccionado);
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
        
        configuraciones[estadoActualSeleccionado].posteriores.push(estadoSeleccionado);
    }
});



document.getElementById("btn-guardar-configuracion").addEventListener("click", function () {
    if (!Object.keys(configuraciones).length) {
        Swal.fire("Error", "No se ha configurado ningún estado.", "error");
        return;
    }

    const payload = [];

    Object.entries(configuraciones).forEach(([estadoActual, config]) => {
       const usuariosSeleccionados = config.asignaciones || [];


        payload.push({
            estado_actual: estadoActual,
            tipo: config.tipo,
            puede_rechazar: config.puede_rechazar ?? 0,
            puede_pedir_documentacion: config.puede_pedir_documentacion ?? 0,
            estado_tiene_expediente: config.tiene_expediente ?? 0,
            posteriores: config.posteriores.map(nombre => ({ nombre })),
            asignaciones: usuariosSeleccionados
        });
    });

    const todosLosEstados = @json(array_column($estados, 'actual'));
    todosLosEstados.forEach(nombreEstado => {
        const yaEsActual = payload.some(conf => conf.estado_actual === nombreEstado);
        const esPosteriorDeAlguien = payload.some(conf =>
            conf.posteriores.some(p => p.nombre === nombreEstado)
        );

        if (!yaEsActual && esPosteriorDeAlguien) {
            payload.push({
                estado_actual: nombreEstado,
                posteriores: [] 
            });
        }
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