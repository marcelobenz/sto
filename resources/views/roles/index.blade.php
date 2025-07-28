@extends("layouts.app")

@section("content")
    <div class="container-fluid px-3">
        <h1 class="mb-4">Lista de Roles</h1>

        <div class="row mb-3">
            <div class="col-md-12">
                <a href="{{ route("roles.create") }}" class="btn btn-primary">
                    Crear Rol
                </a>
            </div>
        </div>

        <table
            id="rolesTable"
            class="table table-bordered table-striped table-hover"
        >
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
                            <a
                                href="{{ route("roles.edit", $rol->id_rol) }}"
                                class="btn btn-sm btn-primary"
                            >
                                <i class="fa fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push("scripts")
    <script>
        $(document).ready(function () {
            $('#rolesTable').DataTable({
                language: {
                    paginate: {
                        previous: '<',
                        next: '>',
                    },
                },
            });
        });
    </script>
@endpush
