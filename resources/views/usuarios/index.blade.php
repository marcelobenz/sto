@extends("layouts.app")

@push("styles")
    <style>
        .rol-info-popup {
            display: none;
            position: absolute;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        /* Mostrar el popup cuando se pasa el mouse sobre el icono */
        .fa-info-circle:hover + .rol-info-popup {
            display: block;
        }
    </style>
@endpush

@section("content")
    <div class="container-fluid px-3">
        <br />
        <br />
        <br />
        <h1 class="mb-4">Lista de Usuarios Internos</h1>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between">
                <!-- Agrupamos los botones -->
                <div>
                    <a
                        href="{{ route("usuarios.create") }}"
                        class="btn btn-primary"
                    >
                        Crear Usuario
                    </a>
                </div>
                <div>
                    <a href="{{ route("roles.index") }}" class="btn btn-info">
                        Administrar Roles
                    </a>
                </div>
            </div>
        </div>
        <table
            id="usuariosTable"
            class="table table-bordered table-striped table-hover"
        >
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Legajo</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo Municipal</th>
                    <th>Grupo</th>
                    <th>Oficina</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@push("scripts")
    <script>
        $(document).ready(function () {
            $('#usuariosTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("usuarios.index") }}',
                columns: [
                    { data: 'id_usuario_interno' },
                    { data: 'legajo' },
                    { data: 'nombre' },
                    { data: 'apellido' },
                    { data: 'correo_municipal' },
                    { data: 'grupo_descripcion' },
                    { data: 'oficina_descripcion' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary fa fa-edit" onclick="editarUsuario(${row.id_usuario_interno})" title="Editar Usuario"></button>
                                <button class="btn btn-sm btn-warning  fa fa-calendar-plus" onclick="editarLicencia(${row.id_usuario_interno})" title="Editar Licencias"></button>
                                <button class="btn btn-sm btn-danger fa fa-trash" onclick="deshabilitarUsuario(${row.id_usuario_interno})" title="Deshabilitar Usuario"></button>
                            `;
                        },
                    },
                ],
                language: {
                    paginate: {
                        previous: '<',
                        next: '>',
                    },
                },
            });
        });

        function editarLicencia(id_usuario_interno) {
            // Redirigir al formulario de edición de licencias para el usuario seleccionado
            window.location.href = `/usuarios/${id_usuario_interno}/licencias`;
        }

        function editarUsuario(id) {
            window.location.href = `/usuarios/${id}/edit`;
        }
    </script>
@endpush
