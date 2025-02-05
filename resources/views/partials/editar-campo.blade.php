<div>
    <h3>Datos del Campo</h3>
    <div class="border-separate border border-slate-400 p-4" style="display: flex; flex-direction: column; gap: 15px;">
        <form id="form-actualizar-datos-campo" method="POST" action="{{ route('secciones-multinota.actualizarDatosCampo') }}">
        @csrf
            <input type="hidden" name="input-titulo-seccion-backup" id="input-titulo-seccion-backup">
            <div style="display: flex; justify-content: space-between;">
                <x-text-input id="nombre" class="block mt-1" style="width: 48%;" type="text" name="nombre" :value="__($campoSelected->nombre)" required />
                <select id="select-tipos" name="select-tipos" class="block mt-1" style="width: 48%;">
                    @foreach ($tipos as $t)
                        <option value="{{ $t->tipo }}" @selected($campoSelected->tipo == $t->tipo)>
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
                <x-text-input id="tamaño" class="block mt-1" style="width: 48%;" type="number" min="1" max="12" name="tamaño" :value="__($campoSelected->dimension)" required /><label>(solo valores de 1 a 12)</label>
            </div>
            <label>
                Obligatorio
                <input type="checkbox" name="obligatorio" {{ $campoSelected->obligatorio == 1 ? 'checked' : '' }}>
            </label>
            @if($campoSelected->tipo == 'STRING')
                <div>
                    <div>
                        <label for="lleva-mascara">Lleva máscara</label>
                        <input type="checkbox" id="lleva-mascara" name="lleva-mascara" {{ $campoSelected->mascara != null ? 'checked' : '' }}>
                    </div>
                    <div id="lleva-mascara-input-container" style="display: {{ $campoSelected->mascara != null ? 'block' : 'none' }};">
                        <input {{ $campoSelected->mascara != null ? 'required' : '' }} id="lleva-mascara-input" type="text" value="{{ old('mascara', $campoSelected->mascara) }}" name="lleva-mascara-input-container" placeholder="Máscara">
                    </div>
                </div>
            @endif

            @if($campoSelected->tipo == 'STRING' || $campoSelected->tipo == 'INTEGER' || $campoSelected->tipo == 'TEXTAREA')
                <div>
                    <div>
                        <label for="limitar-caracteres">Limitar caractéres</label>
                        <input type="checkbox" id="limitar-caracteres" name="limitar-caracteres"
                            {{ ($campoSelected->limite_minimo != null && $campoSelected->limite_maximo != null) ? 'checked' : '' }}>
                    </div>
                    <div id="limitar-caracteres-input-container" 
                        style="display: {{ ($campoSelected->limite_minimo != null && $campoSelected->limite_maximo != null) ? 'block' : 'none' }};">
                            <input {{ $campoSelected->limite_minimo != null ? 'required' : '' }} min="1" max="9999" type="number" value="{{ old('limite_minimo', $campoSelected->limite_minimo) }}" id="limitar-caracteres-input-min" name="limitar-caracteres-input-min" placeholder="Mínimo">
                            <input {{ $campoSelected->limite_maximo != null ? 'required' : '' }} min="1" max="9999" type="number" value="{{ old('limite_maximo', $campoSelected->limite_maximo) }}" id="limitar-caracteres-input-max" name="limitar-caracteres-input-max" placeholder="Máximo">
                    </div>
                </div>
            @endif
            
            @if($campoSelected->tipo == 'TEXTAREA_FIJO')
                <div style="display: flex; flex-direction: column;">
                    <textarea maxlength="500" placeholder="Ingrese un texto...">

                    </textarea>
                </div>
            @endif

            @if($campoSelected->tipo == 'LISTA' || $campoSelected->tipo == 'CAJAS_SELECCION')
                <h3>Listas / Cajas de selección</h3>
                <div style="display: flex; justify-content: space-between;">
                    {{-- TO-DO - Revisar todos los inputs y ver si usar input de HTML o x-text-input --}}
                    {{-- <x-text-input id="nueva-opcion-input" class="block mt-1" type="text" aria-placeholder="Nueva opción..." name="nueva-opcion-input" :value="__($campoSelected->nueva_opcion)" required /> --}}
                    <input 
                        id="nueva-opcion"
                        class="block mt-1"
                        type="text"
                        placeholder="Nueva opción..."
                        name="nueva-opcion"
                        style="width: -webkit-fill-available; margin-right: 10px;"
                    />
                    <button id="boton-nueva-opcion" class="btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-square-fill" viewBox="0 0 16 16">
                            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm6.5 4.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3a.5.5 0 0 1 1 0"/>
                        </svg>
                    </button>
                </div>
                <div id="opciones-campo-container" style="margin-top: 1rem; margin-bottom: 1rem;"></div>
            @endif
            <div style="display: flex; justify-content: end;">
                <button type="submit" name="actualizar-datos-campo" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                        <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var campoSelected = @json($campoSelected);

    $(document).ready(function() {
        if(document.getElementById("select-tipos").value == 'LISTA' || document.getElementById("select-tipos").value == 'CAJAS_SELECCION') {
            fetch('/secciones-multinota/getOpcionesCampo')
                .then(response => response.text())
                .then(data => {
                    document.getElementById("opciones-campo-container").innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }

        $('#select-tipos').on('change', function() {
            var selectedValue = $(this).val(); // Get the selected value using jQuery
            fetch('/secciones-multinota/getOpcionesFormTipoCampo/' + selectedValue)
                .then(response => response.text())
                .then(data => {
                    $("#editar-campo-container").html(data); // Update the container with the fetched data
                })
                .catch(error => console.error('Error:', error));
        });

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
                fetch(`/secciones-multinota/addOpcionCampo/${nuevaOpcion}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("opciones-campo-container").innerHTML = data;
                        document.getElementById('nueva-opcion').value = '';
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

        $('#opciones-campo-container').on('click', '.boton-eliminar-opcion', function() {
            let idOpcionCampo = $(this).attr('data-row-id');

            fetch(`/secciones-multinota/deleteOpcionCampo/${idOpcionCampo}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("opciones-campo-container").innerHTML = data;
                    document.getElementById("opciones-div").innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        });
    });

    document.getElementById('form-actualizar-datos-campo').addEventListener('submit', function () {
        const inputTituloSeccion = document.getElementById('input-titulo-seccion').value;
        console.log('aver: ', inputTituloSeccion);
        document.getElementById('input-titulo-seccion-backup').value = inputTituloSeccion;
    });

    document.addEventListener('click', function(event) {
        if (event.target.id === 'boton-ordenar-opciones') {
            fetch(`/secciones-multinota/getOpcionesCampoAlfabeticamente`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("opciones-campo-container").innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }
    });

    if(campoSelected.tipo === 'STRING') {
        document.getElementById('lleva-mascara').addEventListener('click', function() {
            let inputContainer = document.getElementById('lleva-mascara-input-container');
            let input = document.getElementById('lleva-mascara-input');

            if (this.checked) {
                inputContainer.style.display = 'block';
                input.setAttribute('required', 'required');
            } else {
                inputContainer.style.display = 'none';
                input.removeAttribute('required');
            }
        });
    }

    function preventNegativeNumbers(inputId) {
        document.getElementById(inputId).addEventListener('keypress', function(event) {
            if (event.key === '-' || event.key === 'e' || event.key === 'E') {
                event.preventDefault(); // Prevent typing negative sign and scientific notation
            }
        });
    }

    if(campoSelected.tipo === 'STRING' || campoSelected.tipo === 'INTEGER' || campoSelected.tipo === 'TEXTAREA') {
        document.getElementById('limitar-caracteres').addEventListener('click', function() {
            let inputContainer = document.getElementById('limitar-caracteres-input-container');
            let inputMinimo = document.getElementById('limitar-caracteres-input-min');
            let inputMaximo = document.getElementById('limitar-caracteres-input-max');
            
            if (this.checked) {
                inputContainer.style.display = 'block';
                inputMinimo.setAttribute('required', 'required');
                inputMaximo.setAttribute('required', 'required');
            } else {
                inputContainer.style.display = 'none';
                inputMinimo.removeAttribute('required');
                inputMaximo.removeAttribute('required');
            }
        });

        preventNegativeNumbers('limitar-caracteres-input-min');
        preventNegativeNumbers('limitar-caracteres-input-max');
    }

    preventNegativeNumbers('tamaño');

    /* document.getElementById('icon-container').addEventListener('mouseover', function() {
        document.getElementById('tooltip').style.display = 'block';
    });

    document.getElementById('icon-container').addEventListener('mouseout', function() {
        document.getElementById('tooltip').style.display = 'none';
    }); */
</script>