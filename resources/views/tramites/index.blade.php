@extends('navbar')

@section('heading')

    <!-- Incluye DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

        /* Para el render de los botones flotantes */
        /* Ocultar los botones de acción por defecto */
        .actions {
            display: none;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }
        /* Mostrar los botones de acción cuando el mouse está sobre la fila */
        tr:hover .actions {
            display: inline-block;
        }
        /* Asegurar que las filas tengan position relative para que las acciones sean absolutas respecto a ellas */
        tr {
            position: relative;
        }

        /* Para poder setear el ancho de las columnas de la datatable */
        #tramitesTable th, #tramitesTable td {
        white-space: nowrap; /* Evita que el contenido se divida en varias líneas */
        #tramitesTable th:nth-child(1), #tramitesTable td:nth-child(1) {
        width: 50px;
    }
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
                    <th>Cuenta</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Fecha Alta</th>
                    <th>CUIT </th>
                    <th>Nombre</th>
                    <th>Operador</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <!-- Modal para mostrar detalles del trámite -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detalle del Trámite</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se mostrará el contenido del trámite -->
                    <p id="tramiteDetails"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripting')
    <!-- Incluye DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tramitesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('tramites.data') }}", // Ruta al método que devuelve los datos
                    type: 'GET'
                },
                columns: [
                    { data: 'id_tramite', name: 'id_tramite' },
                    { data: 'cuenta', name: 'cuenta' },
                    { data: 'nombre_categoria', name: 'nombre_categoria' },
                    { data: 'tipo_tramite', name: 'tipo_tramite' },
                    { data: 'estado', name: 'estado' },
                    { data: 'fecha_alta', name: 'fecha_alta' },
                    { data: 'cuit_contribuyente', name: 'cuit_contribuyente' },
                    { data: 'contribuyente', name: 'contribuyente' },
                    { data: 'usuario_interno', name: 'usuario_interno' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="actions">
                                    <button class="btn btn-primary btn-sm btn-action view-details" title="Ver detalle"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-success btn-sm btn-action" title="Tomar trámite"><i class="fas fa-hand-paper"></i></button>
                                    <button class="btn btn-warning btn-sm btn-action" title="Reasignar"><i class="fas fa-exchange-alt"></i></button>
                                    <button class="btn btn-danger btn-sm btn-action" title="Dar de Baja"><i class="fas fa-trash"></i></button>
                                </div>`;
                        }
                    }
                ],
                pageLength: 10, // Número de registros por página
                autoWidth: false,
                language: {
                    search: "Buscar:", // Etiqueta personalizada para la barra de búsqueda
                    paginate: {
                        previous: "<",
                        next: ">"
                    },
                    processing: "Cargando..."
                }
            });
        });


        // Añadir eventos para posicionar los botones de acción
        $('#tramitesTable tbody').on('mouseenter', 'tr', function(e) {
            var $actions = $(this).find('.actions');
            var cursorX = e.pageX - $(this).offset().left;
            $actions.css({
                left: cursorX + 'px'
            });
        });

        $('#tramitesTable tbody').on('mouseleave', 'tr', function() {
            $(this).find('.actions').css({
                left: ''
            });
        });        

        // Evento para el botón "Ver detalle"
        $('#tramitesTable tbody').on('click', '.view-details', function() {
                var data = table.row($(this).parents('tr')).data();
                // Puedes hacer una llamada AJAX aquí para obtener más detalles del trámite si es necesario
                // Por ahora, simplemente mostramos algunos datos en el modal
                var details = `
                    <strong>ID Trámite:</strong> ${data.id_tramite}<br>
                    <strong>ID Trámite:</strong> ${data.cuenta}<br>
                    <strong>Categoría:</strong> ${data.nombre_categoria}<br>
                    <strong>Categoría:</strong> ${data.tipo_tramite}<br>
                    <strong>Fecha Alta:</strong> ${data.fecha_alta}<br>
                    <strong>CUIT:</strong> ${data.cuit_contribuyente}<br>
                    <strong>Legajo:</strong> ${data.contribuyente}<br>
                    <strong>Usuario:</strong> ${data.usuario_interno}
                `;
                $('#tramiteDetails').html(details);
                $('#detailModal').modal('show');
            });

    </script>
@endsection