@extends('navbar')

@section('heading')
    <h1>Editar Sección Multinota</h1>
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
                        Editar Sección Multinota
                        <button type="button" id="salir-editar-seccion-multinota" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-90deg-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.146 4.854a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H12.5A2.5 2.5 0 0 1 15 6.5v8a.5.5 0 0 1-1 0v-8A1.5 1.5 0 0 0 12.5 5H2.707l3.147 3.146a.5.5 0 1 1-.708.708z"/>
                            </svg>
                        </button>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('secciones-multinota.editarSeccion', $seccion->id_seccion) }}">
                            @csrf

                            <h3>Título</h3>
                            <x-text-input id="input-titulo-seccion" style="border-radius: 0.375rem; width: 50%;" type="text" name="titulo" :value="__($seccion->titulo)" required autofocus />

                            <hr>
                            
                            <div class="p-2" style="display: flex; justify-content: space-between;">
                                <h3>Campos</h3>
                                <button id="boton-nuevo-campo" type="button" class="btn btn-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-square-fill" viewBox="0 0 16 16">
                                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6.5 4.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3a.5.5 0 0 1 1 0"/>
                                    </svg>
                                </button>
                            </div>
                            <table style="width: 100%;" class="border-separate border border-slate-400">
                                <thead>
                                    <th class="border border-slate-300 p-2">Etiqueta</th>
                                    <th class="border border-slate-300 p-2">Tipo de dato</th>
                                    <th class="border border-slate-300 p-2">Tamaño</th>
                                    <th class="border border-slate-300 p-2">Máscara</th>
                                    <th class="border border-slate-300 p-2">Mín./Máx. caractéres</th>
                                    <th class="border border-slate-300 p-2">Obligatorio</th>
                                    <th class="border border-slate-300 p-2">Acciones</th>
                                </thead>
                                <tbody id="tabla-campos">
                                    @if (count($campos) != 0)
                                        @foreach ($campos as $c)
                                            <tr class="fila-campo" draggable="true" data-id="{{ $c->id_campo }}"
                                                ondragstart="handleDragCamposStart(event)" 
                                                ondragover="handleDragCamposOver(event)" 
                                                ondrop="handleDropCampos(event)">
                                                <td class="border border-slate-300 p-2">{{ $c->nombre }}</td>
                                                <td class="border border-slate-300 p-2">
                                                    @if ($c->tipo == 'STRING')
                                                        Texto
                                                    @elseif ($c->tipo == 'INTEGER')
                                                        Número
                                                    @elseif ($c->tipo == 'LISTA')
                                                        Lista desplegable
                                                    @elseif ($c->tipo == 'CAJAS_SELECCION')
                                                        Caja de selección múltiple
                                                    @elseif ($c->tipo == 'DATE')
                                                        Fecha
                                                    @elseif ($c->tipo == 'TEXTAREA_FIJO')
                                                        Área de texto fijo
                                                    @endif
                                                </td>
                                                <td class="border border-slate-300 p-2">{{ $c->dimension }}</td>
                                                <td class="border border-slate-300 p-2">{{ ($c->mascara == null) ? '-' : $c->mascara }}</td>
                                                <td class="border border-slate-300 p-2">
                                                    @if ($c->tipo == 'INTEGER')
                                                        @if ($c->limite_minimo != null && $c->limite_maximo != null)
                                                            {{ 'Min ' . $c->limite_minimo . ' (' . $c->limite_minimo_num . ')' . ' / ' . 'Max ' . $c->limite_maximo . ' (' . $c->limite_maximo_num . ')' }}
                                                        @else
                                                            {{ '-' }}
                                                        @endif
                                                    @else
                                                        @if ($c->limite_minimo != null && $c->limite_maximo != null)
                                                            {{ 'Min ' . $c->limite_minimo . ' / ' . 'Max ' . $c->limite_maximo  }}
                                                        @else
                                                            {{ '-' }}
                                                        @endif
                                                    @endif
                                                </td class="border border-slate-300 p-2">
                                                <td class="border border-slate-300 p-2">{{ ($c->obligatorio == 1) ? 'Sí' : 'No' }}</td>
                                                <td class="border border-slate-300 p-2">
                                                    <form method="GET" action="{{ route('secciones-multinota.deleteCampo', $c->id_campo) }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-secondary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
                                                            </svg>
                                                        </button>
                                                    </form> 
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                            <hr>

                            <div id="editar-campo-container"></div>

                            <hr>

                            <h3>Sección</h3>
                            <div id="seccion-campos" style="display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0.75rem; margin: 1rem;">
                                @include('partials.seccion-campos', ['campos' => $campos]) <!-- Carga inicial -->
                            </div>
                            <div style="display: flex; gap: 0.5rem; justify-content: end;">
                                <button type="submit" id="boton-guardar-seccion" class="btn btn-primary">
                                    @if($isEditar)
                                        Actualizar Sección
                                    @elseif(!$isEditar)
                                        Crear Sección
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modal-confirmacion-salir', ['path' => '/secciones-multinota'])
@endsection

@push('scripts')
<script>
    (function () {
      var salirButton = document.getElementById("salir-editar-seccion-multinota");
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
    })();

    var row;
    /* var arrayCampos; */
    var arrayOpcionesCampo;
    var isDraggingOver = false;
    let debounceTimer;

    $(document).ready(function() {
        $('table tr').on('click', function() {
            var campoId = $(this).data('id'); // Assuming you have data-id attribute on each row
            $.ajax({
                type: 'GET',
                url: '/secciones-multinota/' + campoId + '/select',
                success: function(data) {
                    $('#editar-campo-container').html(data); // Replace #details-container with your actual container ID
                }
            });
        });

        $('#boton-nuevo-campo').on('click', function() {
            $.ajax({
                type: 'GET',
                url: '/secciones-multinota/nuevoCampo',
                success: function(data) {
                    $('#editar-campo-container').html(data); // Replace #details-container with your actual container ID
                }
            });
        });
    });

    //Drag handlers de campos

    let draggedRow = null;
    let isDragging = false;

    function handleDragCamposStart(event) {
        draggedRow = event.target.closest('.fila-campo');
        event.dataTransfer.effectAllowed = 'move';
    }

    function handleDragCamposOver(event) {
        event.preventDefault();

        const container = document.querySelector('#tabla-campos');
        const targetRow = event.target.closest('.fila-campo');

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

    function handleDropCampos(event) {
        event.preventDefault();

        const container = document.querySelector('#tabla-campos');
        const rows = Array.from(container.children);

        arrayCampos = rows.map(row => row.dataset.id);
        console.log('Updated array:', arrayCampos);

        // Se llama funcion del controlador para setear nuevo orden
        $.ajax({
            type: 'GET',
            url: `/secciones-multinota/setearNuevoOrdenCampos/${arrayCampos}`,
            success: function(data) {
                $('#seccion-campos').html(data.html);
            }
        });

        draggedRow = null; // Reset
    }

    function handleDragEnterCampo(event) {
        event.preventDefault();
        const container = document.querySelector('#tabla-campos');
        const isEmptySpace = !event.target.closest('.fila-campo');

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

    document.querySelectorAll('.fila-campo').forEach(filaCampo => {
        filaCampo.addEventListener('dragstart', handleDragCamposStart);
        filaCampo.addEventListener('dragover', handleDragCamposOver);
        filaCampo.addEventListener('drop', handleDropCampos);
    });

    document.querySelector('#tabla-campos').addEventListener('dragover', handleDragEnterCampo);

    //Drag handlers de opciones de campos

    function handleDragOpcionesCampoStart(event) {
        draggedRow = event.target.closest('.fila-opcion-campo');
        event.dataTransfer.effectAllowed = 'move';
    }

    function handleDragOpcionesCampoOver(event) {
        event.preventDefault();

        const container = document.querySelector('#tabla-opciones-campo');
        const targetRow = event.target.closest('.fila-opcion-campo');

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

    function handleDropOpcionesCampo(event) {
        event.preventDefault();
        
        if (isDragging) return; // prevent multiple parallel drops
        isDragging = true;
        lockUI();

        const container = document.querySelector('#tabla-opciones-campo');
        const rows = Array.from(container.children);

        arrayOpcionesCampo = rows.map(row => row.dataset.id);
        console.log('Updated array:', arrayOpcionesCampo);

        // Se llama funcion del controlador para setear nuevo orden
        $.ajax({
            type: 'GET',
            url: `/secciones-multinota/setearNuevoOrdenOpcionesCampo/${arrayOpcionesCampo}`,
            success: function(data) {
                $('#opciones-div').html(data.html);
            },
            complete: function () {
                unlockUI();
                draggedRow = null;
                isDragging = false; // unlock
            }
        });
    }

    function handleDragEnterOpcionesCampo(event) {
        event.preventDefault();
        const container = document.querySelector('#tabla-opciones-campo');
        const isEmptySpace = !event.target.closest('.fila-opcion-campo');

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

    document.querySelectorAll('.fila-opcion-campo').forEach(fila => {
        fila.addEventListener('dragstart', handleDragOpcionesCampoStart);
        fila.addEventListener('dragover', handleDragOpcionesCampoOver);
        fila.addEventListener('drop', handleDropOpcionesCampo);
    });

    document.querySelector('#tabla-opciones-campo').addEventListener('dragover', handleDragEnterOpcionesCampo);

    function lockUI() {
        document.querySelector('#tabla-opciones-campo').style.pointerEvents = 'none';
    }

    function unlockUI() {
        document.querySelector('#tabla-opciones-campo').style.pointerEvents = '';
    }
</script>
<style>
    .fila-campo, .fila-opcion-campo {
        background-color: #ededed;
        border: 1px solid black;
        padding: 10px;
        cursor: grab;
        transition: all 0.2s ease;
        position: relative;
    }

    .fila-campo:active, .fila-opcion-campo:active {
        cursor: grabbing;
        opacity: 0.7;
    }

    .fila-campo.dragging, .fila-opcion-campo.dragging {
        opacity: 0.5;
    }

    .fila-campo:hover, .fila-opcion-campo:hover {
        background-color: #b9b7b7;
    }
</style>
@endpush
