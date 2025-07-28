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
            display: none; /* Ocultamos la lista de usuarios por defecto */
        }
        .grupo-label {
            cursor: pointer;
            font-weight: bold;
        }
    </style>
@endpush

@section("content")
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-5 mb-4">
                <div class="border rounded bg-light p-3 h-100">
                    <br />
                    <br />
                    <br />
                    <h1 class="h5">Usuarios por Grupo</h1>

                    <input
                        type="text"
                        id="user-search"
                        class="form-control mb-3"
                        placeholder="Buscar usuario..."
                    />

                    <ul id="treeview" class="list-unstyled">
                        @foreach ($grupos as $grupo)
                            <li class="mb-2">
                                <span class="toggle-icon font-weight-bold">
                                    +
                                </span>
                                <span
                                    class="grupo-label"
                                    data-id="{{ $grupo->id_grupo_interno }}"
                                >
                                    {{ $grupo->descripcion }}
                                </span>
                                <ul
                                    class="usuarios-list pl-4"
                                    style="display: none"
                                >
                                    @foreach ($grupo->usuarios as $usuario)
                                        <li class="usuario-item">
                                            <input
                                                type="checkbox"
                                                class="usuario-checkbox"
                                                id="usuario-{{ $usuario->id_usuario_interno }}"
                                                data-nombre="{{ $usuario->nombre }}"
                                                data-apellido="{{ $usuario->apellido }}"
                                                data-legajo="{{ $usuario->legajo }}"
                                                value="{{ $usuario->id_usuario_interno }}"
                                            />
                                            <label
                                                for="usuario-{{ $usuario->id_usuario_interno }}"
                                            >
                                                {{ $usuario->nombre }}
                                                {{ $usuario->apellido }}
                                                (Legajo:
                                                {{ $usuario->legajo }})
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-lg-8 col-md-7">
                <div class="table-container">
                    <br />
                    <br />
                    <br />
                    <h2 class="h5">Elementos seleccionados</h2>
                    <table
                        class="table table-striped"
                        id="selected-items-table"
                    >
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Límite</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <button id="save-button" class="btn btn-primary mt-2">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("scripts")
    <script>
        $(document).ready(function () {
            console.log('DOM completamente cargado con jQuery');

            $('#treeview').on('click', '.grupo-label', function () {
                const icon = $(this).prev('.toggle-icon');
                const usuariosList = $(this).next('.usuarios-list');

                if (usuariosList.is(':visible')) {
                    usuariosList.hide();
                    icon.text('+');
                } else {
                    usuariosList.show();
                    icon.text('-');
                }
            });

            $('#treeview').on('change', '.usuario-checkbox', function () {
                const checkbox = $(this);
                console.log(
                    'Usuario seleccionado:',
                    checkbox.val(),
                    'Nombre:',
                    checkbox.data('nombre'),
                    'Apellido:',
                    checkbox.data('apellido'),
                    'Estado:',
                    checkbox.is(':checked'),
                );

                const id = checkbox.val();
                const nombre = checkbox.data('nombre');
                const apellido = checkbox.data('apellido');

                if (checkbox.is(':checked')) {
                    addItemToTable(id, nombre, apellido);
                } else {
                    removeItemFromTable(id);
                }
            });

            function addItemToTable(id, nombre, apellido) {
                if ($('#selected-item-' + id).length) {
                    return;
                }
                const newRow = `<tr id="selected-item-${id}">
            <td>${id}</td>
            <td>${nombre} ${apellido}</td>
            <td>
                <input type="number" min="0" style="width: 100%;" placeholder="Ingrese límite" />
            </td>
        </tr>`;
                $('#selected-items-table tbody').append(newRow);
            }

            window.removeItemFromTable = function (id) {
                $('#selected-item-' + id).remove();
            };

            $('#user-search').on('keyup', function () {
                const searchTerm = $(this).val().toLowerCase();

                $('.usuarios-list').hide();
                $('.toggle-icon').text('+');

                $('.usuario-item').each(function () {
                    const usuarioCheckbox = $(this).find('.usuario-checkbox');
                    const usuarioNombre = usuarioCheckbox
                        .data('nombre')
                        .toLowerCase();
                    const usuarioApellido = usuarioCheckbox
                        .data('apellido')
                        .toLowerCase();
                    const usuarioLegajo = usuarioCheckbox
                        .data('legajo')
                        .toString();
                    const grupo = $(this).closest('ul.usuarios-list');
                    const grupoIcon = grupo.prev('.toggle-icon');

                    if (
                        usuarioNombre.includes(searchTerm) ||
                        usuarioApellido.includes(searchTerm) ||
                        usuarioLegajo.includes(searchTerm)
                    ) {
                        $(this).show();
                        grupo.show();
                        grupoIcon.text('-');
                    } else {
                        $(this).hide();
                    }
                });
            });

            $('#save-button').on('click', function () {
                const data = [];

                $('#selected-items-table tbody tr').each(function () {
                    const id = $(this).attr('id').split('-')[2];
                    const limite = $(this).find('input[type="number"]').val();

                    if (limite) {
                        data.push({
                            id_usuario_interno: id,
                            limite: limite,
                        });
                    }
                });

                if (data.length > 0) {
                    $.ajax({
                        url: '{{ route("guardar.limite") }}',
                        method: 'POST',
                        data: {
                            limites: data,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function (response) {
                            alert(response.message);
                        },
                        error: function (xhr) {
                            alert(
                                'Ocurrió un error al guardar los límites: ' +
                                    xhr.responseJSON.message,
                            );
                            console.error(xhr.responseJSON);
                        },
                    });
                } else {
                    alert('No hay límites para guardar');
                }
            });
        });
    </script>
@endpush
