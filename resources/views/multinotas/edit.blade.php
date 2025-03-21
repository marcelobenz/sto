@extends('navbar')

@section('heading')
    <h1>Edición Multinota</h1>
@endsection

@section('contenidoPrincipal')
    <div class="container-xxl" style="margin-left: 20%; margin-right: 20%;">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        Edición Multinota
                        <button type="button" id="salir-editar-multinota" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-90deg-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.146 4.854a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H12.5A2.5 2.5 0 0 1 15 6.5v8a.5.5 0 0 1-1 0v-8A1.5 1.5 0 0 0 12.5 5H2.707l3.147 3.146a.5.5 0 1 1-.708.708z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="seccion-datos-principales" class="card-body">
                        <div style="display: flex; flex-direction: column; gap: 30px;">
                            <div style="display: flex; width: 100%; justify-content: space-between; gap: 50px;">
                                <div style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <label style="font-weight: bold;">Categoría</label>
                                    <select id="categorias">
                                        <option value="">Seleccione...</option>
                                        <option selected value="{{ $multinotaSelected->nombre_categoria_padre }}">{{ $multinotaSelected->nombre_categoria_padre }}</option>
                                    </select>
                                </div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <label style="font-weight: bold;">Subcategorías</label>
                                    <select id="subcategorias">
                                        <option value="">Seleccione...</option>
                                        @foreach($categorias as $cat)
                                            @if($cat->id_categoria === $multinotaSelected->id_categoria)
                                                <option selected value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                                            @else
                                                <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <label style="font-weight: bold;">Código del trámite</label>
                                    <input id="codigo" type="text" disabled value="{{ $multinotaSelected->codigo }}" />
                                </div>
                                <div style="display: flex; flex-direction: column; flex-grow: 1;">
                                    <label style="font-weight: bold;">Nombre del Trámite</label>
                                    <input id="nombre-tramite" type="text" value="{{ $multinotaSelected->nombre }}" />
                                </div>
                            </div>
                            <div style="display: flex; width: 100%; gap: 50px;">
                                <div style="display: flex; gap: 15px;">
                                    <label style="font-weight: bold;">Público</label>
                                    <input id="publico" type="checkbox" {{ $multinotaSelected->publico == 1 ? 'checked' : '' }}>
                                </div>
                                <div style="display: flex; gap: 15px;">
                                    <label style="font-weight: bold;">Lleva documentación</label>
                                    <input id="lleva-documentacion" type="checkbox" {{ $multinotaSelected->lleva_documentacion == 1 ? 'checked' : '' }}>
                                </div>
                                <div style="display: flex; gap: 15px;">
                                    <label style="font-weight: bold;">Mensaje inicial</label>
                                    <input id="muestra-mensaje" type="checkbox" {{ $multinotaSelected->muestra_mensaje == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="seccion-mensaje-inicial" class="card-body">
                        <form method="post">
                        @csrf
                            <textarea id="myeditorinstance">{{ $mensajeInicial ?? '' }}</textarea>
                        </form>
                    </div>
                    <div id="secciones-container" class="card-body flex flex-col">
                        <label>Secciones precargadas</label>
                        @include('partials.secciones-container', ['seccionesAsociadas' => $seccionesAsociadas, 'todasLasSecciones' => $todasLasSecciones])
                    </div>
                    <div id="seccion-boton-previsualizar" class="flex justify-flex-end">
                        <button id="boton-previsualizar-cambios" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 448 512">
                                <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="detalle-multinota" class="hidden">
                        @include('partials.detalle-multinota', ['seccionesAsociadas' => $seccionesAsociadas, 'multinotaSelected' => $multinotaSelected])
                    </div>
                    <div id="seccion-boton-volver-editar" class="hidden">
                        <div class="flex justify-flex-start">
                            <button id="boton-volver-editar" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 448 512">
                                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modal-confirmacion-salir', ['path' => '/multinotas'])
@endsection

@section('scripting')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Add section handler
        $('#secciones-container').on('click', '#boton-agregar-seccion', function(event) {
            event.preventDefault();
            const idSeccionSeleccionada = $('#secciones-precargadas').val();

            fetch("{{ route('multinotas.agregarSeccion') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: idSeccionSeleccionada })
            })
            .then(response => response.json())
            .then(data => {
                $('#secciones-container').html(data.html);
            })
            .catch(error => console.error('Error adding section:', error));
        });

        // Remove section handler
        $('#secciones-container').on('click', '.boton-quitar-seccion', function(event) {
            event.preventDefault();
            const idSeccion = $(this).closest('.seccion').data('id');

            fetch("{{ route('multinotas.quitarSeccion') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: idSeccion })
            })
            .then(response => response.json())
            .then(data => {
                $('#secciones-container').html(data.html);
            })
            .catch(error => console.error('Error removing section:', error));
        });

        $('#boton-previsualizar-cambios').on('click', function() {
            fetch("{{ route('multinotas.previsualizarCambiosMultinota') }}", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                $('#detalle-multinota').html(data.html);
            })
            .catch(error => console.error('Error:', error));
        });
    });

    const div1 = document.getElementById('seccion-datos-principales');
    const div2 = document.getElementById('seccion-mensaje-inicial');
    const div3 = document.getElementById('secciones-container');
    const div4 = document.getElementById('seccion-boton-previsualizar');
    const divBotonVolver = document.getElementById('seccion-boton-volver-editar');
    const divDetalle = document.getElementById('detalle-multinota');

    document.getElementById('boton-previsualizar-cambios').addEventListener('click', function() {
        div1.classList.add('hidden');
        div2.classList.add('hidden');
        div3.classList.add('hidden');
        div4.classList.add('hidden');
        divBotonVolver.classList.remove('hidden');
        divDetalle.classList.remove('hidden');
    });

    document.getElementById('boton-volver-editar').addEventListener('click', function() {
        div1.classList.remove('hidden');
        div2.classList.remove('hidden');
        div3.classList.remove('hidden');
        div4.classList.remove('hidden');
        divBotonVolver.classList.add('hidden');
        divDetalle.classList.add('hidden');
    });

    var salirButton = document.getElementById("salir-editar-multinota");
    var cancelButton = document.getElementById("cancel");
    var modal = document.getElementById("modal-salir");

    // Update button opens a modal dialog
    salirButton.addEventListener("click", function () {
        modal.showModal();
    });

    // Form cancel button closes the dialog box
    cancelButton.addEventListener("click", function () {
        modal.close();
    });

    let draggedRow = null;

    function handleDragSeccionesStart(event) {
        draggedRow = event.target.closest('.seccion');
        event.dataTransfer.effectAllowed = 'move';
    }

    function handleDragSeccionesOver(event) {
        event.preventDefault();

        const container = document.querySelector('.secciones-container');
        const targetRow = event.target.closest('.seccion');

        if (!targetRow || targetRow === draggedRow) return;

        const rows = Array.from(container.children);

        // Get bounding rectangles to check cursor position
        const targetRect = targetRow.getBoundingClientRect();
        const offset = event.clientY - targetRect.top;

        if (offset < targetRect.height / 2) {
            targetRow.before(draggedRow); // Move up (including to the first position)
        } else {
            targetRow.after(draggedRow); // Move down
        }
    }

    function handleDropSecciones(event) {
        event.preventDefault();

        const container = document.querySelector('.secciones-container');
        const rows = Array.from(container.children);

        // Optional: Sync order to array or server if needed
        arraySecciones = rows.map(row => row.dataset.id);
        console.log('Updated array:', arraySecciones);

        draggedRow = null; // Reset
    }

    // Ensure empty spaces allow drops (for dragging below or above all elements)
    function handleDragEnter(event) {
        event.preventDefault();
        const container = document.querySelector('.secciones-container');
        const isEmptySpace = !event.target.closest('.seccion');

        if (isEmptySpace && draggedRow) {
            const rows = Array.from(container.children);

            // Check if dragging above the first element
            if (event.clientY < rows[0].getBoundingClientRect().top) {
                container.prepend(draggedRow); // Move to the first position
            } else {
                container.appendChild(draggedRow); // Move to the last position
            }
        }
    }

    // Attach event listeners
    document.querySelectorAll('.seccion').forEach(seccion => {
        seccion.addEventListener('dragstart', handleDragSeccionesStart);
        seccion.addEventListener('dragover', handleDragSeccionesOver);
        seccion.addEventListener('drop', handleDropSecciones);
    });

    document.querySelector('.secciones-container').addEventListener('dragover', handleDragEnter);
</script>
<style>
    .secciones-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
        border: 2px dashed #ccc;
        padding: 10px;
    }

    .seccion {
        background-color: #ededed;
        border: 1px solid black;
        border-radius: 10px;
        padding: 10px;
        cursor: grab;
        transition: all 0.2s ease;
        position: relative;
        display: inline-block;
    }

    .seccion:active {
        cursor: grabbing;
        opacity: 0.7;
    }

    .seccion.dragging {
        opacity: 0.5;
    }

    .seccion:hover {
        background-color: #b9b7b7;
    }

    /* Hidden button by default */
    .boton-quitar-seccion {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: #ff4d4d;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;

        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }

    /* Show button on hover */
    .seccion:hover .boton-quitar-seccion {
        opacity: 1;
        visibility: visible;
    }

    .boton-quitar-seccion:hover {
        background-color: #e63131;
    }

    #boton-previsualizar-cambios {
        margin: 0 1.25rem 1.25rem 0;
    }

    #boton-volver-editar {
        margin: 0 0 1.25rem 1.25rem;
    }

    .flex {
        display: flex;
    }

    .flex-col {
        flex-direction: column;
    }

    .justify-flex-end {
        justify-content: flex-end;
    }

    .justify-flex-start {
        justify-content: flex-start;
    }

    .hidden {
        display: none;
    }
</style>
@endsection
