@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <br />
                <div class="card">
                    <div class="card-header">Registrar Nueva Licencia</div>

                    <div class="card-body">
                        <!-- Formulario para registrar una nueva licencia -->
                        <form
                            method="POST"
                            action="{{ route("licencias.store", $usuario->id_usuario_interno) }}"
                        >
                            @csrf

                            <div class="form-group">
                                <label for="legajo">Legajo</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="legajo"
                                    name="legajo"
                                    value="{{ $usuario->legajo }}"
                                    disabled
                                />
                            </div>

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="nombre"
                                    name="nombre"
                                    value="{{ $usuario->nombre }} {{ $usuario->apellido }}"
                                    disabled
                                />
                            </div>

                            <div class="form-group">
                                <label for="fecha_inicio">Fecha Desde</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="fecha_inicio"
                                    name="fecha_inicio"
                                    required
                                />
                            </div>

                            <div class="form-group">
                                <label for="fecha_fin">Fecha Hasta</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    id="fecha_fin"
                                    name="fecha_fin"
                                    required
                                />
                            </div>

                            <div class="form-group">
                                <label for="motivo">Motivo</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="motivo"
                                    name="motivo"
                                    required
                                />
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Guardar Licencia
                            </button>
                            <a
                                href="{{ route("usuarios.index") }}"
                                class="btn btn-secondary"
                            >
                                Volver
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla con historial de licencias -->
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Historial de Licencias</div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Motivo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historialLicencias as $licencia)
                                    <tr>
                                        <td>{{ $licencia->motivo }}</td>
                                        <td>{{ $licencia->fecha_inicio }}</td>
                                        <td>{{ $licencia->fecha_fin }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
