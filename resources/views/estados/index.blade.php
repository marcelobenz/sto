@extends('navbar')

@section('heading')
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('contenidoPrincipal')
    <div class="container-fluid px-3">
        <h2 class="mt-3">Administración Workflow de Estados</h2>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Tipos de Trámite</h5>
            </div>
            <div class="card-body">
                <table id="tramitesTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Nombre del Trámite</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripting')
    <!-- jQuery y DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
   $(document).ready(function() {
    $('#tramitesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ route('estados.index') }}",
        "columns": [
            { "data": "categoria" },  
            { "data": "nombre_tipo_tramite" },
            {
                "data": "existe_configuracion",
                "render": function(data, type, row) {
                    if (data == 0) {
                        return `
                            <button class="btn btn-sm btn-primary fa fa-plus" 
                            onclick="window.location.href='/workflow/${row.id_tipo_tramite_multinota}'" 
                            title="Agregar"></button>
                        `;
                    } else {
                        return `
                            <button class="btn btn-sm btn-warning fa fa-edit" 
                            onclick="window.location.href='/workflow/edit/${row.id_tipo_tramite_multinota}'" 
                            title="Editar"></button>
                        `;
                    }
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
