<div>
    <h3>Datos del Campo</h3>
    <div class="border-separate border border-slate-400 p-4" style="display: flex; flex-direction: column; gap: 15px;">
        <div style="display: flex; justify-content: space-between;">
            <x-text-input id="nombre" class="block mt-1" style="width: 48%;" type="text" name="nombre" :value="__($campos[0]->nombre)" required />
            <select id="select-tipos" name="select-tipos" class="block mt-1" style="width: 48%;">
                @foreach ($tipos as $t)
                    <option value="{{ $t->tipo }}" @selected($campos[0]->tipo == $t->tipo)>
                        @if ($t->tipo == 'STRING')
                            Texto
                        @elseif ($t->tipo == 'INTEGER')
                            Número
                        @elseif ($t->tipo == 'LISTA')
                            Lista desplegable
                        @elseif ($t->tipo == 'CAJAS_SELECCION')
                            Caja de selección múltiple
                        @elseif ($t->tipo == 'DATE')
                            Fecha
                        @elseif ($t->tipo == 'TEXTAREA_FIJO')
                            Área de texto fijo
                        @endif
                    </option> 
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
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; gap: 2rem; height: 30px;">
                    <div style="display: flex; gap: 10px;">
                        <label style="margin-bottom: 0px !important;" for="lleva-mascara">Lleva máscara</label>
                        <input type="checkbox" id="lleva-mascara" name="lleva-mascara">
                    </div>
                    <div id="lleva-mascara-input-container" style="display: none;">
                        <input type="text" name="lleva-mascara-input-container" placeholder="Máscara">
                    </div>
                </div>

                <div style="display: flex; gap: 2rem; height: 30px;">
                    <div style="display: flex; gap: 10px;">
                        <label style="margin-bottom: 0px !important;" for="limitar-caracteres">Limitar caractéres</label>
                        <input type="checkbox" id="limitar-caracteres" name="limitar-caracteres">
                    </div>
                    <div id="limitar-caracteres-input-container" style="display: none;">
                        <input type="number" name="limitar-caracteres-input-min" placeholder="Mínimo">
                        <input type="number" name="limitar-caracteres-input-max" placeholder="Máximo">
                    </div>
                </div>
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
            <div style="display: flex; justify-content: space-between;">
                {{-- TO-DO - Revisar todos los inputs y ver si usar input de HTML o x-text-input --}}
                {{-- <x-text-input id="nueva-opcion-input" class="block mt-1" type="text" aria-placeholder="Nueva opción..." name="nueva-opcion-input" :value="__($campos[0]->nueva_opcion)" required /> --}}
                <input 
                    id="nueva-opcion"
                    class="block mt-1"
                    type="text"
                    placeholder="Nueva opción..."
                    name="nueva-opcion"
                    style="width: -webkit-fill-available; margin-right: 10px;"
                    required
                />
                <button id="boton-nueva-opcion" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-square-fill" viewBox="0 0 16 16">
                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6.5 4.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3a.5.5 0 0 1 1 0"/>
                    </svg>
                </button>
            </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        $('#boton-nueva-opcion').on('click', function(event) {
            event.preventDefault();
            const nuevaOpcion = document.getElementById('nueva-opcion').value;

            if(nuevaOpcion === '') {
                Swal.fire(
                    'Error!',
                    'No se puede agregar una opción vacía',
                    'error'
                )
            } else {
                fetch(`/secciones-multinota/addOpcionCampo/${campos[0].id_campo}/${nuevaOpcion}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("opciones-campo-container").innerHTML = data;
                        document.getElementById('nueva-opcion').value = '';
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });

    document.addEventListener('click', function(event) {
        if (event.target.id === 'boton-ordenar-opciones') {
            fetch(`/secciones-multinota/getOpcionesCampoAlfabeticamente/${campos[0].id_campo}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("opciones-campo-container").innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }

        if (event.target.classList.contains('boton-eliminar-opcion')) {
            let idOpcionCampo = event.target.getAttribute('data-row-id');

            fetch(`/secciones-multinota/deleteOpcionCampo/${idOpcionCampo}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("opciones-campo-container").innerHTML = data;
                    document.getElementById("opciones-div").innerHTML = data;
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

    document.getElementById('lleva-mascara').addEventListener('click', function() {
        var inputContainer = document.getElementById('lleva-mascara-input-container');
        if (this.checked) {
            inputContainer.style.display = 'block';
        } else {
            inputContainer.style.display = 'none';
        }
    });

    document.getElementById('limitar-caracteres').addEventListener('click', function() {
        var inputContainer = document.getElementById('limitar-caracteres-input-container');
        if (this.checked) {
            inputContainer.style.display = 'block';
        } else {
            inputContainer.style.display = 'none';
        }
    });

    /* document.getElementById('icon-container').addEventListener('mouseover', function() {
        document.getElementById('tooltip').style.display = 'block';
    });

    document.getElementById('icon-container').addEventListener('mouseout', function() {
        document.getElementById('tooltip').style.display = 'none';
    }); */
</script>