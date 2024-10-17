@extends('navbar')

@section('heading')
<link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    .table-container { margin-left: 20px; }
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
@endsection

@section('contenidoPrincipal')
<div class="container mt-5">
    <h1>Buscar Contribuyente</h1>

    
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

   
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    
    <form action="{{ route('contribuyente.buscar') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="cuit" class="form-label">CUIT</label>
            <input type="text" class="form-control" id="cuit" name="cuit" placeholder="Ingrese el CUIT" value="{{ old('cuit') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

  
    @isset($contribuyente)
        <div class="mt-5">
            <h3>Datos del Contribuyente</h3>
            <ul class="list-group">
                <li class="list-group-item"><strong>CUIT:</strong> {{ $contribuyente->cuit }}</li>
                <li class="list-group-item"><strong>Nombre:</strong> {{ $contribuyente->nombre }}</li>
                <li class="list-group-item"><strong>Apellido:</strong> {{ $contribuyente->apellido }}</li>
                <li class="list-group-item"><strong>Correo:</strong> {{ $contribuyente->correo }}</li>
                <li class="list-group-item"><strong>Teléfono:</strong> {{ $contribuyente->telefono1 }}</li>
            </ul>
        </div>
    @endisset
</div>
@endsection
