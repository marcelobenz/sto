@extends('navbar')

@section('heading')

    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <h1>Lista de Roles</h1>
@endsection

@section('contenidoPrincipal')
    <div class="container-fluid px-3">
        <h1 class="mb-4">Lista de Roles</h1>
        
        <div class="row mb-3">
            <div class="col-md-12">
                <a href="{{ route('roles.create') }}" class="btn btn-primary">Crear Rol</a>
            </div>
        </div>

        <table id="rolesTable" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $rol)
                    <tr>
                        <td>{{ $rol->id_rol }}</td>
                        <td>{{ $rol->nombre }}</td>
                        <td>
                            <a href="{{ route('roles.edit', $rol->id_rol) }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-edit"></i> 
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripting')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rolesTable').DataTable({
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
