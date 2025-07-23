<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Ingreso Externo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-blue-100 h-screen flex items-center justify-center">
    <div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-sm text-center space-y-6">
        <h3 class="text-2xl font-bold text-blue-700">Ingreso Externo</h3>

        <form method="POST" action="{{ route('ingreso-externo.login') }}" class="space-y-5">
            @csrf

            <!-- CUIT Input -->
            <div class="relative">
                <i class="bi bi-person absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500 text-lg"></i>
                <input type="text" name="cuit" placeholder="CUIT" required
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <!-- Clave Input -->
            <div class="relative">
                <i class="bi bi-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-blue-500 text-lg"></i>
                <input type="password" name="clave" placeholder="Contraseña" required
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:outline-none" />
            </div>

            <!-- Opciones -->
            <div class="flex justify-between items-center text-sm text-gray-600">
                <label class="flex items-center gap-2">
                    <input type="checkbox" id="recordarme" class="accent-blue-500">
                    Recordarme
                </label>
                <a href="#" class="text-blue-500 hover:underline">¿Olvidaste tu contraseña?</a>
            </div>

            <!-- Botones -->
            <div class="space-y-2">
                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-full transition">
                    Iniciar Sesión
                </button>
                <a href="{{ route('ingreso-externo.registro') }}"
                    class="block w-full bg-gray-200 hover:bg-gray-300 text-blue-700 font-semibold py-2 rounded-full transition">
                    Registrar
                </a>
            </div>
        </form>
    </div>
</body>
</html>
