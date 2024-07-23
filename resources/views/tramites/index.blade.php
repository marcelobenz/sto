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

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
        }

        #overlay div {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        #overlay img {
            display: block;
            margin: 0 auto;
        }

        #overlay p {
            margin-top: 10px; /* Ajusta este valor según sea necesario */
            font-size: 16px; /* Ajusta el tamaño de la fuente según sea necesario */
            color: #000; /* Ajusta el color del texto según sea necesario */
        }
</style>
@endsection

@section('contenidoPrincipal')
    <div id="overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:1000;">
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
            <!-- Aquí puedes poner un spinner o un mensaje de carga -->
            <img src="spinner.gif" alt="Cargando...">
            <p>Cargando ...</p>
        </div>
    </div>

    <div class="container-fluid px-3">
        <table id="tramitesTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr colspan=5><h3 class="text-center" style="background-color: #f0f0f0; padding: 10px;">Todos los Trámites</h3></tr>
                <tr>
                    <th>ID Trámite</th>
                    <th>Categoría</th>
                    <th>Fecha Alta</th>
                    <th>Fecha Modif</th>
                    <th>Correo</th>
                    <th>CUIT </th>
                    <th>Legajo</th>
                    <th>Usuario</th>
                </tr>
                <tr>
                    <th><input type="text" placeholder="Buscar ID"></th>
                    <th><input type="text" placeholder="Buscar Categoría"></th>
                    <th><input type="text" placeholder="Buscar Fecha Alta"></th>
                    <th><input type="text" placeholder="Buscar Fecha Modif"></th>
                    <th><input type="text" placeholder="Buscar Correo"></th>
                    <th><input type="text" placeholder="Buscar CUIT"></th>
                    <th><input type="text" placeholder="Buscar Legajo"></th>
                    <th><input type="text" placeholder="Buscar Usuario"></th>
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
        var table = $('#tramitesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('tramites.index') }}",
                beforeSend: function() {
                    $('#overlay').show();
                },
                complete: function() {
                    $('#overlay').hide();
                }
            },
            columns: [
                { data: 'id_tramite' },
                { data: 'nombre_categoria' },
                { data: 'fecha_alta' },
                { data: 'fecha_modificacion' },
                { data: 'correo' },
                { data: 'cuit_contribuyente' },
                { data: 'legajo' },
                { data: 'usuario' }
            ],
            pageLength: 5,
            language: {
                paginate: {
                    previous: "<",
                    next: ">"
                }
            }
        });

            // Configurar búsqueda en cada columna
            $('#tramitesTable thead input').on('keyup', function (e) {
                if (e.key === 'Enter') { // Ejecutar búsqueda solo cuando se presiona Enter
                    var columnIndex = $(this).parent().index();
                    table.column(columnIndex).search(this.value,false,true).draw();
                }
            });
            // Evitar que el DataTable se recargue con cada clic en los inputs
            $('#tramitesTable thead input').on('click', function (e) {
                e.stopPropagation(); // Previene la propagación del clic que podría estar causando la recarga
            });
        });
    </script>
@endsection