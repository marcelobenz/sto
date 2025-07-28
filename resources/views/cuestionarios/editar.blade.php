@extends("layouts.app")

@push("styles")
    <style>
        .table-container {
            margin-left: 20px;
        }
        .toggle-icon {
            cursor: pointer;
            margin-right: 5px;
        }
        .usuarios-list {
            display: none;
        }
        .grupo-label {
            cursor: pointer;
            font-weight: bold;
        }
        .card-header .btn-link {
            text-decoration: none;
            width: 100%;
            text-align: left;
        }
    </style>
@endpush

@section("content")
    <br />
    <br />
    <div class="container mt-5">
        <h1>Editar Cuestionario</h1>
        <div class="row">
            {{-- Formulario principal --}}
            <div class="col-md-9">
                <form
                    action="{{ route("cuestionarios.update", $cuestionario->id_cuestionario) }}"
                    method="POST"
                >
                    @csrf
                    @method("PUT")

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input
                            type="text"
                            name="titulo"
                            class="form-control"
                            value="{{ $cuestionario->titulo }}"
                            required
                        />
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            Descripción
                        </label>
                        <textarea name="descripcion" class="form-control">
{{ $cuestionario->descripcion }}</textarea
                        >
                    </div>

                    <div class="mb-3">
                        <h4>Nueva Pregunta</h4>
                        <div class="input-group mb-2">
                            <input
                                type="text"
                                id="preguntaInput"
                                class="form-control"
                                placeholder="Escribe la pregunta aquí"
                            />
                            <button
                                type="button"
                                class="btn btn-success"
                                onclick="agregarPregunta()"
                            >
                                Agregar Pregunta
                            </button>
                        </div>
                    </div>

                    <h4>Preguntas</h4>
                    <table class="table table-striped mt-3" id="tablaPreguntas">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pregunta</th>
                                <th>(Si) con detalle</th>
                                <th>(No) con detalle</th>
                                <th>Finaliza con Si</th>
                                <th>Rechaza con No</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cuestionario->preguntas as $pregunta)
                                <tr>
                                    <td>{{ $pregunta->id_pregunta }}</td>
                                    <td>
                                        <input
                                            type="text"
                                            name="preguntas[{{ $pregunta->id_pregunta }}][descripcion]"
                                            value="{{ $pregunta->descripcion }}"
                                            class="form-control"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="preguntas[{{ $pregunta->id_pregunta }}][siDetalle]"
                                            {{ $pregunta->flag_detalle_si ? "checked" : "" }}
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="preguntas[{{ $pregunta->id_pregunta }}][noDetalle]"
                                            {{ $pregunta->flag_detalle_no ? "checked" : "" }}
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="preguntas[{{ $pregunta->id_pregunta }}][finalizaSi]"
                                            {{ $pregunta->flag_finalizacion_si ? "checked" : "" }}
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="preguntas[{{ $pregunta->id_pregunta }}][rechazaNo]"
                                            {{ $pregunta->flag_rechazo_no ? "checked" : "" }}
                                        />
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            onclick="eliminarPreguntaExistente(this)"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary mt-3">
                        Guardar Cambios
                    </button>

                    {{-- Sección lateral: tipo_tramite_multinota y estados --}}
                    <div class="col-md-3">
                        <h5>Tipo de Trámite Multinota</h5>

                        {{-- Filtro --}}
                        <input
                            type="text"
                            id="filtroTramites"
                            class="form-control mb-2"
                            placeholder="Buscar tipo..."
                        />

                        {{-- Contenedor scrollable --}}
                        <div
                            id="listaTramites"
                            style="max-height: 500px; overflow-y: auto"
                        >
                            <div class="accordion" id="accordionTipos">
                                @foreach ($agrupado as $tipoId => $estados)
                                    <div
                                        class="card mb-2 tipo-tramite"
                                        data-nombre="{{ strtolower($tipos[$tipoId] ?? "desconocido") }}"
                                    >
                                        <div
                                            class="card-header p-2"
                                            id="heading{{ $tipoId }}"
                                        >
                                            <h2 class="mb-0">
                                                <button
                                                    class="btn btn-link grupo-label"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $tipoId }}"
                                                >
                                                    {{ $tipos[$tipoId] ?? "Desconocido" }}
                                                </button>
                                            </h2>
                                        </div>
                                        <div
                                            id="collapse{{ $tipoId }}"
                                            class="collapse"
                                            data-bs-parent="#accordionTipos"
                                        >
                                            <div class="card-body">
                                                @foreach ($estados as $estado)
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            name="tipo_tramite_multinota[{{ $tipoId }}][]"
                                                            value="{{ $estado["id_estado_tramite"] }}"
                                                            id="estado_{{ $tipoId }}_{{ $estado["id_estado_tramite"] }}"
                                                            {{ in_array($estado["id_estado_tramite"], $estadosSeleccionados) ? "checked" : "" }}
                                                        />

                                                        <label
                                                            class="form-check-label"
                                                            for="estado_{{ $tipoId }}_{{ $estado["id_estado_tramite"] }}"
                                                        >
                                                            {{ $estado["nombre_estado"] }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        let contador = {{ $cuestionario->preguntas->count() + 1 }};

        function agregarPregunta() {
            const preguntaTexto = document
                .getElementById('preguntaInput')
                .value.trim();

            if (preguntaTexto === '') {
                alert('Por favor, escribe una pregunta antes de agregar.');
                return;
            }

            const tablaPreguntas = document
                .getElementById('tablaPreguntas')
                .getElementsByTagName('tbody')[0];
            const nuevaFila = tablaPreguntas.insertRow();

            const celdaNumero = nuevaFila.insertCell(0);
            const celdaPregunta = nuevaFila.insertCell(1);
            const celdaSiDetalle = nuevaFila.insertCell(2);
            const celdaNoDetalle = nuevaFila.insertCell(3);
            const celdaFinalizaSi = nuevaFila.insertCell(4);
            const celdaRechazaNo = nuevaFila.insertCell(5);
            const celdaAcciones = nuevaFila.insertCell(6);

            celdaNumero.innerText = contador;
            celdaPregunta.innerHTML = `<input type="text" name="nuevas_preguntas[${contador}][descripcion]" value="${preguntaTexto}" class="form-control">`;
            celdaSiDetalle.innerHTML = `<input type="checkbox" name="nuevas_preguntas[${contador}][siDetalle]">`;
            celdaNoDetalle.innerHTML = `<input type="checkbox" name="nuevas_preguntas[${contador}][noDetalle]">`;
            celdaFinalizaSi.innerHTML = `<input type="checkbox" name="nuevas_preguntas[${contador}][finalizaSi]">`;
            celdaRechazaNo.innerHTML = `<input type="checkbox" name="nuevas_preguntas[${contador}][rechazaNo]">`;
            celdaAcciones.innerHTML = `<button type="button" class="btn btn-danger btn-sm" onclick="eliminarPreguntaNueva(this)">Eliminar</button>`;

            document.getElementById('preguntaInput').value = '';
            contador++;
        }

        function eliminarPreguntaExistente(button) {
            const fila = button.closest('tr');
            fila.remove();
        }

        function eliminarPreguntaNueva(button) {
            const fila = button.closest('tr');
            fila.remove();
        }
    </script>

    <script>
        // Filtro en tiempo real para tipo_tramite_multinota
        document
            .getElementById('filtroTramites')
            .addEventListener('input', function () {
                const filtro = this.value.toLowerCase();
                const tramites = document.querySelectorAll('.tipo-tramite');

                tramites.forEach(function (tramite) {
                    const nombre = tramite.getAttribute('data-nombre');
                    tramite.style.display = nombre.includes(filtro)
                        ? ''
                        : 'none';
                });
            });
    </script>
@endpush
