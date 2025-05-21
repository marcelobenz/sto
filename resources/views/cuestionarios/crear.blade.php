@extends('navbar')

@section('heading')
<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .table-container { margin-left: 20px; }
    .toggle-icon {
        cursor: pointer;
        margin-right: 5px;
    }
    .usuarios-list {
        display: none; /* Ocultamos la lista de usuarios por defecto */
    }
    .grupo-label {
        cursor: pointer;
        font-weight: bold;
    }
</style>
@endsection

@section('contenidoPrincipal')
<div class="container mt-5">
    <h1>Crear Nuevo Cuestionario</h1>
    <form action="{{ route('cuestionarios.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <h4>Nueva Pregunta</h4>
            <div class="input-group">
                <input type="text" id="preguntaInput" class="form-control" placeholder="Escribe la pregunta aquí">
                <button type="button" class="btn btn-success" onclick="agregarPregunta()">Agregar Pregunta</button>
            </div>
        </div>

        
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
               
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Guardar Cuestionario</button>
    </form>
</div>
@endsection


@push('scripts')
<script>
    let preguntas = [];
    let contador = 1;

    function agregarPregunta() {
        const preguntaTexto = document.getElementById('preguntaInput').value.trim();
        
        if (preguntaTexto === '') {
            alert('Por favor, escribe una pregunta antes de agregar.');
            return;
        }

        
        const pregunta = {
            id: contador,
            texto: preguntaTexto,
            siDetalle: false,
            noDetalle: false,
            finalizaSi: false,
            rechazaNo: false
        };
        preguntas.push(pregunta);

       
        actualizarTablaPreguntas();

       
        document.getElementById('preguntaInput').value = '';
        contador++;
    }

    function actualizarTablaPreguntas() {
        const tablaPreguntas = document.getElementById('tablaPreguntas').getElementsByTagName('tbody')[0];
        tablaPreguntas.innerHTML = '';

        preguntas.forEach((pregunta, index) => {
            const nuevaFila = tablaPreguntas.insertRow();

            const celdaNumero = nuevaFila.insertCell(0);
            const celdaPregunta = nuevaFila.insertCell(1);
            const celdaSiDetalle = nuevaFila.insertCell(2);
            const celdaNoDetalle = nuevaFila.insertCell(3);
            const celdaFinalizaSi = nuevaFila.insertCell(4);
            const celdaRechazaNo = nuevaFila.insertCell(5);
            const celdaAcciones = nuevaFila.insertCell(6);

            celdaNumero.innerText = pregunta.id;
            celdaPregunta.innerText = pregunta.texto;

            celdaSiDetalle.innerHTML = `<input type="checkbox" ${pregunta.siDetalle ? 'checked' : ''} onchange="toggleCheckbox(${index}, 'siDetalle')">`;
            celdaNoDetalle.innerHTML = `<input type="checkbox" ${pregunta.noDetalle ? 'checked' : ''} onchange="toggleCheckbox(${index}, 'noDetalle')">`;
            celdaFinalizaSi.innerHTML = `<input type="checkbox" ${pregunta.finalizaSi ? 'checked' : ''} onchange="toggleCheckbox(${index}, 'finalizaSi')">`;
            celdaRechazaNo.innerHTML = `<input type="checkbox" ${pregunta.rechazaNo ? 'checked' : ''} onchange="toggleCheckbox(${index}, 'rechazaNo')">`;

            celdaAcciones.innerHTML = `<button type="button" class="btn btn-danger btn-sm" onclick="eliminarPregunta(${index})">Eliminar</button>`;
        });
    }

    function toggleCheckbox(index, field) {
        preguntas[index][field] = !preguntas[index][field];
    }

    function eliminarPregunta(index) {
        preguntas.splice(index, 1);
        actualizarTablaPreguntas();
    }

    
    document.querySelector('form').addEventListener('submit', function (event) {
        const inputPreguntas = document.createElement('input');
        inputPreguntas.type = 'hidden';
        inputPreguntas.name = 'preguntas';
        inputPreguntas.value = JSON.stringify(preguntas);
        this.appendChild(inputPreguntas);
    });
</script>
@endpush