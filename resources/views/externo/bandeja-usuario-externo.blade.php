@extends('navbarExterno')

@section('heading')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">

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
    <div class="table-container">
        <table id="tramitesTable" class="min-w-full">
            <thead>
                <tr>
                    <th colspan="6" class="p-0">
                        <h3 class="text-center text-2xl font-semibold text-gray-900 py-6 select-none">Bandeja de Trámites</h3>
                    </th>
                </tr>
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
                <!-- Los datos se llenan con DataTables -->
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
      $('#tramitesTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: function(data, callback, settings) {
        $.ajax({
            url: "{{ route('bandeja-usuario-externo') }}",
            type: 'GET',
            data: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest' 
            },
            success: function(response) {
                callback(response);
            }
        });
    },
    columns: [
        { data: 'id_tramite' },
        { data: 'nombre_categoria' },
        { data: 'fecha_alta' },
        { data: 'fecha_modificacion' },
        { data: 'correo' },
        { data: 'cuit_contribuyente' }
    ],
    language: {
        paginate: {
            previous: "<",
            next: ">"
        },
        processing: "<div id='custom-processing'>Cargando, por favor espera...</div>"
    },
    lengthMenu: [10, 25, 50],
    pageLength: 10,
    order: [[2, 'desc']]
});
    });
</script>
@endpush
