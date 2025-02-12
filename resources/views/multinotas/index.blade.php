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
                    <input id="nombre" type="text" placeholder="Buscar..." style="flex-grow: 1;" />
                    <select name="categorias" id="select-categorias" style="flex-grow: 1;">
                        {{-- TO-DO - Recuperar categorias y cargar opciones --}}
                        <option value="1">1</option>
                    </select>
                    <select name="publicas" id="select-publicas" style="flex-grow: 1;">
                        {{-- TO-DO - Cargar opciones correspondientes --}}
                        <option value="1">1</option>
                    </select>
                    <select name="mensaje-inicial" id="select-mensaje-inicial" style="flex-grow: 1;">
                        {{-- TO-DO - Cargar opciones correspondientes --}}
                        <option value="1">1</option>
                    </select>
                    <select name="expediente" id="select-expediente" style="flex-grow: 1;">
                        {{-- TO-DO - Cargar opciones correspondientes --}}
                        <option value="1">1</option>
                    </select>
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
                "ajax": "{{ route('multinotas.index') }}",
                "columns": [
                    { "data": "codigo" },
                    { "data": "nombre" },
                    { "data": "nombre_categoria" },
                    { "data": "publico" },
                    { "data": "muestra_mensaje" },
                    { "data": "fecha_alta" },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-secondary" onclick="verMultinota(${row.id_seccion})">Ver</button>
                                <button class="btn btn-sm btn-primary" onclick="editarMultinota(${row.id_seccion})">Editar</button>
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
    </script>
@endsection