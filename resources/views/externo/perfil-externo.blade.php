@extends('navbarExterno')

@section('heading')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('contenidoPrincipal')
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-800 text-white text-lg font-semibold px-6 py-4">
            Modificar Perfil
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-700 border border-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-700 border border-red-400">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('perfil-externo.actualizarPerfil') }}" method="POST" class="space-y-6">
                @csrf

                <input type="hidden" name="id_contribuyente_multinota" value="{{ $usuario->id_contribuyente_multinota }}">

                <div>
                    <label for="cuit" class="block font-medium text-gray-700">CUIT</label>
                    <input
                        type="text"
                        id="cuit"
                        name="cuit"
                        value="{{ old('cuit', $usuario->cuit ?? '') }}"
                        required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="00-00000000-0"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre" class="block font-medium text-gray-700">Nombre</label>
                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            value="{{ old('nombre', $usuario->nombre ?? '') }}"
                            required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>

                    <div>
                        <label for="apellido" class="block font-medium text-gray-700">Apellido</label>
                        <input
                            type="text"
                            id="apellido"
                            name="apellido"
                            value="{{ old('apellido', $usuario->apellido ?? '') }}"
                            required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                </div>

                <div>
                    <label for="correo" class="block font-medium text-gray-700">Correo Electrónico</label>
                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        value="{{ old('correo', $usuario->correo ?? '') }}"
                        required
                        class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="correo@ejemplo.com"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="telefono1" class="block font-medium text-gray-700">Teléfono 1</label>
                        <input
                            type="tel"
                            id="telefono1"
                            name="telefono1"
                            value="{{ old('telefono1', $usuario->telefono1 ?? '') }}"
                            required
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="+54 9 11 1234 5678"
                        />
                    </div>

                    <div>
                        <label for="telefono2" class="block font-medium text-gray-700">Teléfono 2</label>
                        <input
                            type="tel"
                            id="telefono2"
                            name="telefono2"
                            value="{{ old('telefono2', $usuario->telefono2 ?? '') }}"
                            class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="+54 9 11 8765 4321"
                        />
                    </div>
                </div>

                <div>
                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
