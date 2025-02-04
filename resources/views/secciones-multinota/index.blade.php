@extends('navbar')

@section('heading')

    <!-- Incluye DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Estilos personalizados para los iconos de flecha */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: 12px; /* Ajusta el tamaño de la fuente */
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link {
            padding: 0.5rem 0.75rem; /* Ajusta el padding */
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button .page-link svg {
            width: 16px; /* Ajusta el ancho del icono */
            height: 16px; /* Ajusta el alto del icono */
        }
    </style>
@endsection

@section('contenidoPrincipal')
    <div class="container-fluid px-3">
        <div class="row mb-3 px-3" style="justify-content: end;">
            <div class="col-md-12">
                <br/>
                    <br/>
                        <br/>
                            <h2>Secciones Multinota</h2>
            </div>
            <button class="btn btn-primary">+</button>
        </div>
        <table id="secciones-multinota-table" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Campos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@section('scripting')
    <!-- Incluye jQuery y DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#secciones-multinota-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('secciones-multinota.index') }}",
                "columns": [
                    { "data": "titulo" },
                    { "data": "campos" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary" onclick="editarSeccionMultinota(${row.id_seccion})">Editar</button>
                                <button class="btn btn-sm btn-danger" onclick="confirmarEliminar(${row.id_seccion})">Eliminar</button>
                            `;
                        }
                    }
                ],
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                }
            });
        });

        function editarSeccionMultinota(id) {
            window.location.href = `/secciones-multinota/${id}/edit`;
        }

        function confirmarEliminar(id) {
            Swal.fire({
                title: '¿Está seguro que desea eliminar la sección?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llamada AJAX para actualizar el valor de flag_activo a 0
                    $.ajax({
                        url: '/secciones-multinota/' + id + '/desactivar',
                        type: 'PUT', 
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            console.log('Sección desactivada correctamente.');
                            Swal.fire(
                                'Eliminado!',
                                'La sección ha sido eliminada.',
                                'success'
                            )
                            // Recargar la tabla para reflejar el cambio
                            $('#secciones-multinota-table').DataTable().ajax.reload(null, false); // false mantiene la página actual
                        },
                        error: function(err) {
                            console.error('Error al desactivar la sección.');
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al eliminar la sección.',
                                'error'
                            )
                        }
                    });
                } else {
                    console.log('Se canceló desactivación de sección con ID: ' + id);
                }
            });
        }
    </script>
@endsection