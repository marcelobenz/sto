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
<div class="container">
    <div class="d-flex">
        <div class="treeview" style="width: 30%;">
            <h1>Usuarios por Grupo</h1>
            <ul id="treeview">
                @foreach ($grupos as $grupo)
                    <li>
                        <!-- Ícono de expandir/colapsar y nombre del grupo -->
                        <span class="toggle-icon">+</span>
                        <span class="grupo-label" data-id="{{ $grupo->id_grupo_interno }}">{{ $grupo->descripcion }}</span>
                        <ul class="usuarios-list">
                            @foreach ($grupo->usuarios as $usuario)
                                <li>
                                    <input type="checkbox" class="usuario-checkbox" id="usuario-{{ $usuario->id_usuario_interno }}" data-nombre="{{ $usuario->nombre }}" value="{{ $usuario->id_usuario_interno }}">
                                    <label for="usuario-{{ $usuario->id_usuario_interno }}">{{ $usuario->nombre }} (Legajo: {{ $usuario->legajo }})</label>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="table-container">
            <h2>Elementos seleccionados</h2>
            <table class="table table-striped" id="selected-items-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Límite</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <button id="save-button" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</div>
@endsection

@section('scripting')
<script>
    $(document).ready(function() {
        console.log("DOM completamente cargado con jQuery"); 

        // Lógica de expandir/colapsar para los grupos al hacer clic en el nombre del grupo
        $('#treeview').on('click', '.grupo-label', function() {
            const icon = $(this).prev('.toggle-icon'); // Ícono de expansión/colapso
            const usuariosList = $(this).next('.usuarios-list'); // Lista de usuarios asociada

            if (usuariosList.is(':visible')) {
                usuariosList.hide(); // Ocultar la lista de usuarios
                icon.text('+'); // Cambiar el ícono a "+"
            } else {
                usuariosList.show(); // Mostrar la lista de usuarios
                icon.text('-'); // Cambiar el ícono a "-"
            }
        });

        // Manejo de selección de checkboxes de los usuarios
        $('#treeview').on('change', '.usuario-checkbox', function() {
            const checkbox = $(this);
            console.log("Usuario seleccionado:", checkbox.val(), "Nombre:", checkbox.data('nombre'), "Estado:", checkbox.is(':checked')); // Depuración

            const id = checkbox.val();
            const nombre = checkbox.data('nombre');
            const tipo = 'Usuario';

            if (checkbox.is(':checked')) {
                addItemToTable(id, nombre, tipo);
            } else {
                removeItemFromTable(id);
            }
        });

        function addItemToTable(id, nombre, tipo) {
            if ($('#selected-item-' + id).length) {
                return; 
            }
            const newRow = `<tr id="selected-item-${id}">
                <td>${id}</td>
                <td>${nombre}</td>
                <td>${tipo}</td>
                <td>
                    <input type="number" min="0" style="width: 100%;" placeholder="Ingrese límite" />
                </td>
            </tr>`;
            $('#selected-items-table tbody').append(newRow);
        }

        window.removeItemFromTable = function(id) {
            $('#selected-item-' + id).remove(); 
        };
    });

    // Lógica para guardar los límites
    $('#save-button').on('click', function() {
        const data = [];

        $('#selected-items-table tbody tr').each(function() {
            const id = $(this).attr('id').split('-')[2]; 
            const limite = $(this).find('input[type="number"]').val(); 

            if (limite) {
                data.push({
                    id_usuario_interno: id,
                    limite: limite
                });
            }
        });

        if (data.length > 0) {
            $.ajax({
                url: '{{ route("guardar.limite") }}', 
                method: 'POST',
                data: {
                    limites: data,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    alert(response.message); 
                },
                error: function(xhr) {
                    alert('Ocurrió un error al guardar los límites: ' + xhr.responseJSON.message);
                    console.error(xhr.responseJSON); 
                }
            });
        } else {
            alert('No hay límites para guardar');
        }
    });
</script>
@endsection
