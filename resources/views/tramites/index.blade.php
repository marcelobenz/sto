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

        .actions {
            display: flex; /* Cambiar a flexbox para alinearlos horizontalmente */
            justify-content: center; /* Centrar los botones dentro de la celda */
            gap: 5px; /* Espacio entre botones */
        }

        tr {
            position: relative;
        }

        #tramitesTable td:last-child {
            text-align: center; /* Centrar el contenido de la última columna */
            vertical-align: middle; /* Centrar verticalmente los botones */
        }
        /* Para poder setear el ancho de las columnas de la datatable */
        #tramitesTable th, #tramitesTable td {
        white-space: nowrap; /* Evita que el contenido se divida en varias líneas */
        #tramitesTable th:nth-child(1), #tramitesTable td:nth-child(1) {
        width: 50px;
        }
    }

    /* Truncar texto largo con "..." */
    .table td {
        max-width: 150px; /* Ajusta el ancho máximo de las celdas */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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
                    <th>ID</th>
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

        // Variable para filtro de bandeja tranites en curso
        var soloIniciados = {{ isset($soloIniciados) && $soloIniciados ? 'true' : 'false' }};

        $(document).ready(function() {
            var table = $('#tramitesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('tramites.data') }}",
                    type: 'GET',
                    data: function (d) {
                        d.soloIniciados = soloIniciados;
                    }
                },
                autoWidth: false, // Desactiva el ajuste automático
                columnDefs: [
                    { width: "5%", targets: 0, orderable: true },  // Habilitar orden en la columna ID
                    { width: "10%", targets: 1, orderable: true }, // Cuenta
                    { width: "20%", targets: 2, orderable: true }, // Categoría
                    { width: "15%", targets: 3, orderable: true }, // Tipo
                    { width: "10%", targets: 4, orderable: true }, // Estado
                    { width: "10%", targets: 5, orderable: true }, // Fecha Alta
                    { width: "10%", targets: 6, orderable: true }, // CUIT
                    { width: "15%", targets: 7, orderable: true }, // Nombre
                    { width: "15%", targets: 8, orderable: true }, // Operador
                    { width: "10%", targets: 9, orderable: false }  // Desactivar orden en controles
                ],
                columns: [
                    { data: 'id_tramite', name: 'id_tramite' },
                    { data: 'cuenta', name: 'cuenta' },
                    {
                        data: 'nombre_categoria',
                        name: 'nombre_categoria',
                        render: function(data, type, row) {
                            // Trunca el texto y añade el tooltip
                            return `<span title="${data}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: 150px;">${data}</span>`;
                        }
                    },
                    {
                        data: 'tipo_tramite',
                        name: 'tipo_tramite',
                        render: function(data, type, row) {
                            // Trunca el texto y añade el tooltip
                            return `<span title="${data}" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: 150px;">${data}</span>`;
                        }
                    },
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
                                <div class="actions text-center">
                                    <a href="/tramites/${row.id_tramite}/detalle" class="btn btn-primary btn-sm btn-action" title="Ver detalle"><i class="fas fa-eye"></i></a>
                                    <button class="btn btn-success btn-sm btn-action" title="Tomar trámite"><i class="fas fa-hand-paper"></i></button>
                                    <button class="btn btn-warning btn-sm btn-action" title="Reasignar"><i class="fas fa-exchange-alt"></i></button>
                                    <button class="btn btn-danger btn-sm btn-action" title="Dar de Baja" onclick="darDeBajaTramite( ${row.id_tramite} )"><i class="fas fa-trash"></i></button>
                                </div>`;
                        }
                    }
                ],
                order: [[0, 'desc']], // Orden predeterminado por la columna ID en forma ascendente
                pageLength: 10,
                language: {
                    search: "Buscar:",
                    paginate: {
                        previous: "<",
                        next: ">"
                    },
                    processing: "Cargando..."
                }
            });

            // Evento para el botón "Ver detalle"
            $('#tramitesTable tbody').on('click', '.view-details', function() {
                // Usa la instancia global `table`
                var data = table.row($(this).parents('tr')).data();

                // Construye el contenido del detalle
                var details = `
                    <strong>ID Trámite:</strong> ${data.id_tramite}<br>
                    <strong>Cuenta:</strong> ${data.cuenta}<br>
                    <strong>Categoría:</strong> ${data.nombre_categoria}<br>
                    <strong>Tipo:</strong> ${data.tipo_tramite}<br>
                    <strong>Estado:</strong> ${data.estado}<br>
                    <strong>Fecha Alta:</strong> ${data.fecha_alta}<br>
                    <strong>CUIT:</strong> ${data.cuit_contribuyente}<br>
                    <strong>Nombre:</strong> ${data.contribuyente}<br>
                    <strong>Operador:</strong> ${data.usuario_interno}
                `;

                // Muestra los detalles en el modal
                $('#tramiteDetails').html(details);
                $('#detailModal').modal('show');
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

        function darDeBajaTramite(idTramite) {
        if (confirm("¿Estás seguro de que deseas dar de baja este trámite?")) {
            fetch("{{ route('tramites.darDeBaja') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ idTramite: idTramite })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("El trámite ha sido dado de baja correctamente.");
                    //  location.reload(); // Recargar la página para ver cambios
                    $('#tramitesTable').DataTable().ajax.reload(null, false);
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    }

    </script>
@endsection