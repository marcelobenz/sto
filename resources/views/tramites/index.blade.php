@extends('navbar')

@section('heading')

    <!-- Incluye DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
         /* Estilos personalizados para el mensaje de procesamiento */
         #custom-processing {
            font-size: 16px;
            color: black;
            font-weight: bold;
            background-color: #f0f8ff;
            padding: 10px;
            border: 1px solid blue;
            border-radius: 5px;
            text-align: center;
        }
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
        <table id="tramitesTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr colspan=5><h3 class="text-center" style="background-color: #f0f0f0; padding: 10px;">Todos los Trámites</h3></tr>
                <tr>
                    <th>ID Trámite</th>
                    <th>Categoría</th>
                    <th>Fecha de Alta</th>
                    <th>Fecha de Modificación</th>
                    <th>Correo</th>
                    <th>CUIT Contribuyente</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@section('scripting')
    <!-- Incluye DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tramitesTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('tramites.index') }}",
                "columns": [
                    { "data": "id_tramite" },
                    { "data": "nombre_categoria" },
                    { "data": "fecha_alta" },
                    { "data": "fecha_modificacion" },
                    { "data": "correo" },
                    { "data": "cuit_contribuyente" }
                ],
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    },
                    "processing": "<div id='custom-processing'>Cargando, por favor espera...</div>"
                }
            });
        });
    </script>
@endsection