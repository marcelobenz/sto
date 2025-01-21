<div>
    <h3>Datos del Campo</h3>
    <div class="border-separate border border-slate-400 p-4" style="display: flex; flex-direction: column; gap: 15px;">
        <div style="display: flex; justify-content: space-between;">
            <x-text-input id="nombre" class="block mt-1" style="width: 48%;" type="text" name="nombre" :value="__($campos[0]->nombre)" required />
            <select id="select-tipos" name="select-tipos" class="block mt-1" style="width: 48%;">
                @foreach ($tipos as $t)
                    <option value="{{ $t->tipo }}" @selected($campos[0]->tipo == $t->tipo)>{{ $t->tipo }}</option> 
                @endforeach
            </select>
        </div>
        <div>
            <h6>Tamaño</h6>
            <x-text-input id="tamaño" class="block mt-1" style="width: 48%;" type="number" min="1" max="12" name="tamaño" :value="__($campos[0]->dimension)" required /><label>(solo valores de 1 a 12)</label>
        </div>
        <label>
            Obligatorio
            {{-- TO-DO - Revisar --}}<input type="checkbox" name="obligatorio" value="active">
        </label>
        @if($campos[0]->tipo == 'STRING')
            <div style="display: flex; flex-direction: column;">
                <label>
                    Lleva máscara
                    {{-- TO-DO - Revisar --}}<input type="checkbox" name="llevaMascara" value="active">
                </label>
                <label>
                    Limitar caractéres
                    {{-- TO-DO - Revisar --}}<input type="checkbox" name="limitarCaracteres" value="active">
                </label>
            </div>
        @elseif($campos[0]->tipo == 'INTEGER')
            <div style="display: flex; flex-direction: column;">
                <label>
                    Limitar caractéres
                    {{-- TO-DO - Revisar --}}<input type="checkbox" name="limitarCaracteres" value="active">
                </label>
            </div>
        @elseif($campos[0]->tipo == 'TEXTAREA')
            <div style="display: flex; flex-direction: column;">
                <label>
                    Limitar caractéres
                    {{-- TO-DO - Revisar --}}<input type="checkbox" name="limitarCaracteres" value="active">
                </label>
            </div>
        @elseif($campos[0]->tipo == 'TEXTAREA_FIJO')
            <div style="display: flex; flex-direction: column;">
                <textarea maxlength="500" placeholder="Ingrese un texto...">

                </textarea>
            </div>
        @elseif($campos[0]->tipo == 'LISTA' || $campos[0]->tipo == 'CAJAS_SELECCION')
            <h3>Listas / Cajas de selección</h3>
            <div id="opciones-campo-container"></div>
        @endif
        <div style="display: flex; justify-content: end;">
            <button class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    var campos = @json($campos);

    $(document).ready(function() {
        if(document.getElementById("select-tipos").value == 'LISTA') {
            fetch('/secciones-multinota/getOpcionesCampo/' + campos[0].id_campo + '/' + document.getElementById("select-tipos").value)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("opciones-campo-container").innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }
    });

    document.addEventListener('change', function(event) {
        if (event.target.id === 'select-tipos') {
            var selectedValue = event.target.value;
            fetch('/secciones-multinota/getOpcionesFormTipoCampo/' + campos[0].id_campo + '/' + selectedValue)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("editar-campo-container").innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>