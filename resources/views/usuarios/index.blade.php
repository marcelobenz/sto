@extends('navbar')

@section('heading')

    <!-- Incluye DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

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
@endsection

@section('contenidoPrincipal')
<div class="container-fluid px-3">
        <h1 class="mb-4">Lista de Usuarios Internos</h1>
        <div class="row mb-3">
            <div class="col-md-12">
                <br/>
                    <br/>
                        <br/>
                            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Crear Usuario</a>
            </div>
        </div>
        <table id="usuariosTable" class="table table-bordered table-striped table-hover">
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
            $('#usuariosTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('usuarios.index') }}",
                "columns": [
                    { "data": "id_usuario_interno" },
                    { "data": "legajo" },
                    { "data": "nombre" },
                    { "data": "apellido" },
                    { "data": "correo_municipal" },
                    { "data": "grupo_descripcion" },
                    { "data": "oficina_descripcion" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary fa fa-edit" onclick="editarUsuario(${row.id_usuario_interno})"></button>
                                <button class="btn btn-sm btn-warning  fa fa-calendar-plus" onclick="editarLicencia(${row.id_usuario_interno})"></button>
                                <button class="btn btn-sm btn-danger fa fa-trash" onclick="deshabilitarUsuario(${row.id_usuario_interno})"></button>
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


    </script>
@endsection
