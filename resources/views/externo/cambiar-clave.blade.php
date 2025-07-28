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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <br />
                <br />
                <br />
                <div class="card">
                    <div class="card-header">Cambiar clave</div>
                    <div class="card-body">
                        <form
                            action="{{ route("cambiar-clave.submit") }}"
                            method="POST"
                        >
                            @csrf
                            <div class="form-group">
                                <label for="current_password">
                                    Contraseña Actual
                                </label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="current_password"
                                    name="current_password"
                                    required
                                />
                            </div>
                            <div class="form-group">
                                <label for="new_password">
                                    Nueva Contraseña
                                </label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="new_password"
                                    name="new_password"
                                    required
                                />
                            </div>
                            <div class="form-group">
                                <label for="new_password_confirmation">
                                    Confirmar Nueva Contraseña
                                </label>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="new_password_confirmation"
                                    name="new_password_confirmation"
                                    required
                                />
                            </div>
                            <button type="submit" class="btn btn-primary">
                                Cambiar Contraseña
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
