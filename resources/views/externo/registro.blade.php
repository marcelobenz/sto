<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD1LSzqe5ZoyFn4Uk3JX1CTR2aeXGMVRtg&libraries=places"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-8 space-y-10">
        <h2 class="text-2xl font-bold text-center text-gray-800">Crear Cuenta</h2>

        {{-- Datos Personales --}}
        <div class="space-y-4 border border-gray-200 rounded-xl p-6 shadow-sm">
            <h3 class="text-xl font-semibold text-gray-700">Datos Personales</h3>

            @if ($errors->any())
                <div class="mb-2">
                    <ul class="list-disc text-red-600 pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('registro-externo.registrar') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="nombre" placeholder="Nombre" class="input" value="{{ old('nombre') }}">
                    <input type="text" name="apellido" placeholder="Apellido" class="input" value="{{ old('apellido') }}">
                </div>

                <input type="text" name="cuit" placeholder="CUIL / CUIT" class="input" value="{{ old('cuil_cuit') }}">
                <input type="email" name="correo" placeholder="Correo electrónico" class="input" value="{{ old('email') }}">

                <div class="grid grid-cols-2 gap-4">
                  <input type="text" name="telefono_1" placeholder="Teléfono 1" class="input" value="{{ old('telefono_1') }}">
                  <input type="text" name="telefono_2" placeholder="Teléfono 2 (opcional)" class="input" value="{{ old('telefono_2') }}">
                 </div>

                <div class="grid grid-cols-2 gap-4">
                    <input type="password" name="clave" placeholder="Contraseña" class="input">
                    <input type="password" name="clave_confirmation" placeholder="Repetir contraseña" class="input">
                </div>

                {{-- Dirección --}}
                <div class="mt-8 border border-gray-200 rounded-xl p-6 shadow-sm space-y-4">
                    <h3 class="text-xl font-semibold text-gray-700">Dirección</h3>

                    <div class="flex gap-2">
                       <input type="text" id="autocomplete" placeholder="Buscar dirección..." class="input flex-1" oninput="fetchPredicciones()" autocomplete="off" />
                        <div id="predictions" class="border rounded bg-white max-h-40 overflow-auto"></div>
                        <button type="button" onclick="buscarDireccion()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 rounded-lg text-sm">
                            Buscar
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="calle" id="calle" placeholder="Calle" class="input">
                        <input type="text" name="numero" id="numero" placeholder="Número" class="input">
                        <input type="text" name="ciudad" id="ciudad" placeholder="Ciudad" class="input">
                        <input type="text" name="provincia" id="provincia" placeholder="Provincia" class="input">
                        <input type="text" name="codigo_postal" id="codigo_postal" placeholder="Código Postal" class="input">
                    </div>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-xl font-semibold transition">
                    Registrarse
                </button>
            </form>
        </div>
    </div>

    <style>
        .input {
            @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500;
        }
    </style>

   <script>
  const API_KEY = '';

  async function fetchPredicciones() {
    const input = document.getElementById('autocomplete').value;
    const predictionsContainer = document.getElementById('predictions');

    if (input.length < 3) {
      predictionsContainer.innerHTML = '';
      return;
    }

    const url = ``;

    try {
      const response = await fetch(url);
      const data = await response.json();

      if (data.predictions) {
        predictionsContainer.innerHTML = data.predictions.map(pred => `
          <div class="p-2 cursor-pointer hover:bg-blue-100" onclick="selectPrediction('${pred.place_id}', '${pred.description}')">
            ${pred.description}
          </div>
        `).join('');
      } else {
        predictionsContainer.innerHTML = '<div class="p-2 text-gray-500">No hay resultados</div>';
      }
    } catch (error) {
      console.error('Error al obtener predicciones:', error);
      predictionsContainer.innerHTML = '<div class="p-2 text-red-500">Error al buscar</div>';
    }
  }

async function selectPrediction(placeId, description) {
  document.getElementById('autocomplete').value = description;
  document.getElementById('predictions').innerHTML = '';

  const url = ``;

  try {
    const response = await fetch(url);
    const data = await response.json();

    if (data.status === 'OK' && data.result && data.result.address_components) {
      let calle = '', numero = '', ciudad = '', provincia = '', cp = '';

      data.result.address_components.forEach(comp => {
        const types = comp.types;
        if (types.includes("route")) calle = comp.long_name;
        if (types.includes("street_number")) numero = comp.long_name;
        if (types.includes("locality")) ciudad = comp.long_name;
        if (types.includes("administrative_area_level_1")) provincia = comp.long_name;
        if (types.includes("postal_code")) cp = comp.long_name;
      });

      document.getElementById("calle").value = calle;
      document.getElementById("numero").value = numero;
      document.getElementById("ciudad").value = ciudad;
      document.getElementById("provincia").value = provincia;
      document.getElementById("codigo_postal").value = cp;
    } else {
      alert("No se pudieron obtener detalles de la dirección");
    }
  } catch (error) {
    console.error('Error al obtener detalles:', error);
    alert("Error al obtener detalles de la dirección");
  }
}

</script>
</body>
</html>
