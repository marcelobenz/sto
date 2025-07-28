@extends("layouts.app")

@push("styles")
    <style>
        .table-container {
            margin-left: 20px;
        }
        .toggle-icon {
            cursor: pointer;
            margin-right: 5px;
        }
        .usuarios-list {
            display: none; /* Ocultamos la lista de usuarios por defecto */
        }
        .grupo-label {
            cursor: pointer;
            font-weight: bold;
        }
    </style>
@endpush

@section("content")
    <div class="container mt-5">
        <br />
        <br />
        <h1>Listado de Cuestionarios</h1>

        <div class="mb-3">
            <a
                href="{{ route("cuestionarios.create") }}"
                class="btn btn-success"
            >
                <i class="fas fa-plus"></i>
                Crear nuevo cuestionario
            </a>
        </div>

        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cuestionarios as $cuestionario)
                    <tr>
                        <td>{{ $cuestionario->id_cuestionario }}</td>
                        <td>{{ $cuestionario->titulo }}</td>
                        <td>{{ $cuestionario->descripcion }}</td>
                        <td>
                            {{ $cuestionario->flag_baja ? "Inactivo" : "Activo" }}
                        </td>
                        <td>
                            <a
                                href="{{ route("cuestionarios.edit", $cuestionario->id_cuestionario) }}"
                                class="btn btn-primary btn-sm"
                            >
                                Editar
                            </a>

                            @if ($cuestionario->flag_baja)
                                <form
                                    action="{{ route("cuestionarios.activar", $cuestionario->id_cuestionario) }}"
                                    method="POST"
                                    style="display: inline-block"
                                >
                                    @csrf
                                    @method("PUT")
                                    <button
                                        type="submit"
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('¿Estás seguro de querer activar este cuestionario?')"
                                    >
                                        Activar
                                    </button>
                                </form>
                            @else
                                <form
                                    action="{{ route("cuestionarios.desactivar", $cuestionario->id_cuestionario) }}"
                                    method="POST"
                                    style="display: inline-block"
                                >
                                    @csrf
                                    @method("PUT")
                                    <button
                                        type="submit"
                                        class="btn btn-warning btn-sm"
                                        onclick="return confirm('¿Estás seguro de querer desactivar este cuestionario?')"
                                    >
                                        Desactivar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
