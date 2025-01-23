<div id="opciones-div">
    <table style="width: 100%;" class="border-separate border border-slate-400">
        <thead>
            <th class="border border-slate-300 p-2" style="display: flex; justify-content: space-between;">
                Opción <button class="btn btn-secondary" id="boton-ordenar-opciones" onclick="handleClick">Ordenar alfabéticamente</button>
            </th>
            <th class="border border-slate-300 p-2">Acciones</th>
        </thead>
        <tbody>
            @foreach ($opcionesCampo as $opc)
                <tr draggable="true" data-id="{{ $opc->id_opcion_campo }}" ondragstart="handleDragOpcionesCampoStart()" ondragover="handleDragOpcionesCampoOver()" ondragleave="handleDragOpcionesCampoLeave()">
                    <td class="border border-slate-300 p-2">{{ $opc->opcion }}</td>
                    <td class="border border-slate-300 p-2">
                        <form method="GET" action="{{ route('secciones-multinota.deleteOpcionCampo', $opc->id_opcion_campo) }}">
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
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    tr {
        cursor: grab;
    }
    tr:active {
       cursor: grabbing !important;
    }
</style>