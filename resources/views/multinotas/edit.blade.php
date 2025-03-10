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
                        <a href="/multinotas" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-90deg-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1.146 4.854a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H12.5A2.5 2.5 0 0 1 15 6.5v8a.5.5 0 0 1-1 0v-8A1.5 1.5 0 0 0 12.5 5H2.707l3.147 3.146a.5.5 0 1 1-.708.708z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="card-body">
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
                                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
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
                    <div class="card-body">
                        <form method="post">
                        @csrf
                            <textarea id="myeditorinstance">{{ $mensajeInicial ?? '' }}</textarea>
                        </form>
                    </div>
                    <div class="card-body" style="display: flex; flex-direction: column;">
                        <label>Secciones precargadas</label>
                        <select style="width: 30%;">
                            <option value="">Seleccione...</option>
                        </select>
                        <div style="display: flex; width: 100%; padding-top: 20px;">
                            <div class="secciones-container" style="display: flex; flex-direction: column; width: 100%; gap: 10px;">
                                @foreach ($seccionesMultinota as $s)
                                    @if (count($s->campos) !== 0)
                                        <div class="seccion" draggable="true" data-id="{{ $s->id_seccion }}" 
                                             ondragstart="handleDragSeccionesStart(event)" 
                                             ondragover="handleDragSeccionesOver(event)" 
                                             ondrop="handleDropSecciones(event)">
                                            <h3>{{ $s->titulo }}</h3>
                                            <div style="display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0.75rem; margin: 1rem;">
                                                @include('partials.seccion-campos', ['campos' => $s->campos])
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripting')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
    }

    .seccion:active {
        cursor: grabbing;
        opacity: 0.7;
    }

    .seccion.dragging {
        opacity: 0.5;
    }
</style>
@endsection
