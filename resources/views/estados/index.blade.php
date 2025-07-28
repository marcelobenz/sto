@extends("layouts.app")

@section("content")
    <div class="container-fluid px-3">
        <h2 class="mt-3">Administración Workflow de Estados</h2>

        <div class="card mt-3">
            <div class="card-header">
                <h5>Tipos de Trámite</h5>
            </div>
            <div class="card-body">
                <table
                    id="tramitesTable"
                    class="table table-bordered table-striped"
                >
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

@push("scripts")
    <script>
        $(document).ready(function () {
            $('#tramitesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("estados.index") }}',
                columns: [
                    { data: 'categoria' },
                    { data: 'nombre_tipo_tramite' },
                    {
                        data: 'existe_configuracion',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            let botones = '';
                            if (data == 0) {
                                botones += `
                    <button class="btn btn-sm btn-primary fa fa-plus"
                    onclick="window.location.href='/workflow/${row.id_tipo_tramite_multinota}'"
                    title="Agregar"></button>
                `;
                            } else {
                                botones += `
                    <button class="btn btn-sm btn-warning fa fa-edit"
                    onclick="window.location.href='/workflow/editar/${row.id_tipo_tramite_multinota}'"
                    title="Editar"></button>
                `;
                            }
                            if (row.existe_borrador == 1) {
                                botones += `
                 <button class="btn btn-sm btn-info fa fa-file-alt"
                    onclick="window.location.href='/workflow/borrador/${row.id_tipo_tramite_multinota}'"
                    title="Borrador"></button>
                `;
                            }
                            return botones;
                        },
                    },
                ],
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
