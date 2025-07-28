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
            display: none;
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
        <h1>Buscar Contribuyente</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @endif

        <form action="{{ route("contribuyente.buscar") }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="cuit" class="form-label">CUIT</label>
                <input
                    type="text"
                    class="form-control"
                    id="cuit"
                    name="cuit"
                    placeholder="Ingrese el CUIT"
                    value="{{ old("cuit") }}"
                    required
                />
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        @isset($contribuyente)
            <div class="mt-5">
                <h3>Datos del Contribuyente</h3>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>CUIT:</strong>
                        {{ $contribuyente->cuit }}
                    </li>
                    <li class="list-group-item">
                        <strong>Nombre:</strong>
                        {{ $contribuyente->nombre }}
                    </li>
                    <li class="list-group-item">
                        <strong>Apellido:</strong>
                        {{ $contribuyente->apellido }}
                    </li>

                    <li class="list-group-item">
                        <form
                            action="{{ route("contribuyente.actualizarCorreo", ["id" => $contribuyente->id_contribuyente_multinota]) }}"
                            method="POST"
                            class="form-inline d-flex align-items-center"
                        >
                            @csrf
                            @method("PUT")
                            <strong>Correo:</strong>
                            <input
                                type="email"
                                class="form-control ml-3"
                                name="correo"
                                value="{{ $contribuyente->correo }}"
                                required
                            />
                            <button type="submit" class="btn btn-success ml-2">
                                Guardar
                            </button>
                        </form>
                    </li>

                    <li class="list-group-item">
                        <strong>Teléfono:</strong>
                        {{ $contribuyente->telefono1 }}
                    </li>
                </ul>

                <form
                    action="{{ route("contribuyente.restablecerClave", ["id" => $contribuyente->id_contribuyente_multinota]) }}"
                    method="POST"
                >
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        Restablecer Contraseña
                    </button>
                </form>
            </div>
        @endisset
    </div>
@endsection
