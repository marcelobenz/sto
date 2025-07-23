@extends('navbarExterno')

@section('heading')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('contenidoPrincipal')
<div class="max-w-xl mx-auto px-4 py-8">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-800 text-white text-lg font-semibold px-6 py-4">
            Cambiar clave
        </div>

        <div class="p-6">
            <form action="{{ route('cambiar-clave.submit') }}" method="POST" class="space-y-6">
                @csrf

                @if($errors->any())
                    <div class="bg-red-100 text-red-700 border border-red-400 rounded px-4 py-3">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="current_password" class="block font-medium text-sm text-gray-700">Contraseña Actual</label>
                    <input type="password" id="current_password" name="current_password" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="new_password" class="block font-medium text-sm text-gray-700">Nueva Contraseña</label>
                    <input type="password" id="new_password" name="new_password" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="new_password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar Nueva Contraseña</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                           class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition">
                        Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
