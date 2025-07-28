@extends("layouts.app")

@push("styles")
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: 500;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
@endpush

@section("content")
    <div class="container my-5">
        <div class="form-container">
            <h1 class="text-center mb-4">Editar Configuración SMTP</h1>

            <form action="{{ route("configuracion.update") }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="host" class="form-label">SMTP Host:</label>
                    <input
                        type="text"
                        id="host"
                        name="host"
                        class="form-control"
                        value="{{ $configuracion_mail->host ?? "" }}"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="puerto" class="form-label">SMTP Puerto:</label>
                    <input
                        type="number"
                        id="smtp_port"
                        name="puerto"
                        class="form-control"
                        value="{{ $configuracion_mail->puerto ?? "" }}"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="usuario" class="form-label">
                        SMTP Usuario:
                    </label>
                    <input
                        type="text"
                        id="usuario"
                        name="usuario"
                        class="form-control"
                        value="{{ $configuracion_mail->usuario ?? "" }}"
                        required
                    />
                </div>
                <div class="mb-3">
                    <label for="clave" class="form-label">
                        SMTP Contraseña:
                    </label>
                    <input
                        type="password"
                        id="clave"
                        name="clave"
                        class="form-control"
                        value="{{ $configuracion_mail->clave ?? "" }}"
                        required
                    />
                </div>
                <button type="submit" class="btn btn-primary">
                    Guardar Cambios
                </button>
            </form>
        </div>
    </div>
@endsection
