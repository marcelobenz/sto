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
    <h1>Bienvenido a la Bandeja de Usuario Externo</h1>

    <p>Este es un ejemplo de página después del inicio de sesión exitoso.</p>
@endsection
