@extends("layouts.app")

@push("styles")
    <style>
        /* Estilos personalizados para los iconos de flecha */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: 12px; /* Ajusta el tamaño de la fuente */
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
            padding: 0.5rem 0.75rem; /* Ajusta el padding */
        }
        .dataTables_wrapper
            .dataTables_paginate
            .paginate_button
            .page-link
            svg {
            width: 16px; /* Ajusta el ancho del icono */
            height: 16px; /* Ajusta el alto del icono */
        }

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
        <div class="row mb-3">
            <div class="col-md-12">
                <br />
                <br />
                <br />
                <a
                    href="{{ route("categorias.create") }}"
                    class="btn btn-primary"
                >
                    Crear Categoría
                </a>
            </div>
        </div>
        <table
            id="categoriasTable"
            class="table table-bordered table-striped table-hover"
        >
            <thead>
                <tr>
                    <th>ID Categoria</th>
                    <th>Nombre</th>
                    <th>Fecha de Alta</th>
                    <th>Nombre Categoria Padre</th>
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
            $('#categoriasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("categorias.index") }}',
                columns: [
                    { data: 'id_categoria' },
                    { data: 'nombre' },
                    { data: 'fecha_alta' },
                    { data: 'parent_nombre' },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary fa fa-edit" onclick="editarCategoria(${row.id_categoria})" title="Editar Categoria"></button>
                                <button class="btn btn-sm btn-danger fa fa-trash" onclick="confirmarEliminar(${row.id_categoria})" title="Eliminar"></button>
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

        function editarCategoria(id) {
            window.location.href = `/categorias/${id}/edit`;
        }

        function confirmarEliminar(id) {
            Swal.fire({
                title: '¿Está seguro de que desea eliminar esta categoría?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llamada AJAX para actualizar el valor de flag_activo a 0
                    $.ajax({
                        url: '/categorias/' + id + '/desactivar',
                        type: 'PUT', // PUT o PATCH dependiendo de tu configuración de rutas
                        data: {
                            _token: '{{ csrf_token() }}', // Asegúrate de incluir el token CSRF
                        },
                        success: function (response) {
                            console.log('Categoría desactivada correctamente.');
                            Swal.fire(
                                'Eliminado!',
                                'La categoría ha sido eliminada.',
                                'success',
                            );
                            // Recargar la tabla para reflejar el cambio
                            $('#categoriasTable')
                                .DataTable()
                                .ajax.reload(null, false); // false mantiene la página actual
                        },
                        error: function (err) {
                            console.error('Error al desactivar la categoría.');
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al eliminar la categoría.',
                                'error',
                            );
                        },
                    });
                } else {
                    console.log(
                        'Cancelar desactivación de categoría con ID: ' + id,
                    );
                }
            });
        }
    </script>
@endpush
