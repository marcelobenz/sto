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
                            <h2>Multinotas</h2>
            </div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 5rem;">
            <div id="filtros-multinota" style="background-color: #ededed; padding: 40px 10px;">
                <h3>FILTROS</h1>
                <div style="display: flex; width: 100%; justify-content: space-between; gap: 50px;">
                    <div style="display: flex; flex-direction: column; flex-grow: 1;">
                        <label style="font-weight: bold;">Nombre</label>
                        <input id="nombre" type="text" placeholder="Buscar..." />
                    </div>
                    <div style="display: flex; flex-direction: column; flex-grow: 1;">
                        <label style="font-weight: bold;">Categorías</label>
                        <select id="categoria">
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: flex; flex-direction: column; flex-grow: 1;">
                        <label style="font-weight: bold;">Públicas</label>
                        <select id="publicas">
                            <option value="Todas">Todas</option>
                            <option value="Públicas">Públicas</option>
                            <option value="No públicas">No públicas</option>
                        </select>
                    </div>
                    <div style="display: flex; flex-direction: column; flex-grow: 1;">
                        <label style="font-weight: bold;">Mensaje Inicial</label>
                        <select id="mensaje-inicial">
                            <option value="Todas">Todas</option>
                            <option value="Muestran mensaje inicial">Muestran mensaje inicial</option>
                            <option value="No muestran mensaje inicial">No muestran mensaje inicial</option>
                        </select>
                    </div>
                    <div style="display: flex; flex-direction: column; flex-grow: 1;">
                        <label style="font-weight: bold;">Expediente</label>
                        <select id="expediente">
                            <option value="Todas">Todas</option>
                            <option value="Llevan expediente">Llevan expediente</option>
                            <option value="No llevan expediente">No llevan expediente</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="background-color: #ededed; padding: 40px 10px;">
                 {{-- <form method="GET" action="{{ route('multinotas.crearNuevaMultinota') }}">
                @csrf --}}
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" id="boton-crear-multinota" class="btn btn-primary">Crear</button>
                </div>
                {{-- </form> --}}
                <table id="multinotas-table" class="table table-bordered table-striped table-hover">
                    <h3>LISTADO</h1>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Trámite</th>
                            <th>Categoría</th>
                            <th>Pública</th>
                            <th>Con Mensaje</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
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
            $('#multinotas-table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('multinotas.index') }}",
                    "data": function(d) {
                        d.nombre = $('#nombre').val();
                        d.categoria = $('#categoria').val();
                        d.publicas = $('#publicas').val();
                        d.mensajeInicial = $('#mensaje-inicial').val();
                        d.expediente = $('#expediente').val();
                    }
                },
                "columns": [
                    { "data": "codigo" },
                    { "data": "nombre" },
                    { "data": "nombre_categoria" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <input type="checkbox" id="publico" name="publico" ${row.publico === 1 ? 'checked' : ''}>
                            `;
                        }
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <input type="checkbox" id="muestra_mensaje" name="muestra_mensaje" ${row.muestra_mensaje === 1 ? 'checked' : ''}>
                            `;
                        }
                    },
                    { "data": "fecha_alta" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-secondary" onclick="verMultinota(${row.id_tipo_tramite_multinota})">Ver</button>
                                <button class="btn btn-sm btn-primary" onclick="editarMultinota(${row.id_tipo_tramite_multinota})">Editar</button>
                                <button class="btn btn-sm btn-danger" onclick="confirmarEliminar(${row.id_tipo_tramite_multinota})">Eliminar</button>
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

            var table = $('#multinotas-table').DataTable();

            $('#nombre, #categoria, #publicas, #mensaje-inicial, #expediente').on('keyup change', function() {
                table.draw();
            });
        });

        function verMultinota(id) {
            window.location.href = `/multinotas/${id}/view`;
        }

        function editarMultinota(id) {
            window.location.href = `/multinotas/${id}/edit`;
        }

        function confirmarEliminar(id) {
            Swal.fire({
                title: '¿Está seguro que desea eliminar la multinota?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/multinotas/' + id + '/desactivar',
                        type: 'PUT', 
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            console.log('Sección desactivada correctamente.');
                            Swal.fire(
                                'Eliminado!',
                                'La multinota ha sido eliminada.',
                                'success'
                            )
                            // Recargar la tabla para reflejar el cambio
                            $('#multinotas-table').DataTable().ajax.reload(null, false); // false mantiene la página actual
                        },
                        error: function(err) {
                            console.error('Error al desactivar la multinota.');
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al eliminar la multinota.',
                                'error'
                            )
                        }
                    });
                } else {
                    console.log('Se canceló desactivación de multinota con ID: ' + id);
                }
            });
        }
    </script>
@endsection