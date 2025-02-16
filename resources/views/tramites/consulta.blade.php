@extends('navbar')

@section('contenidoPrincipal')
<div class="container">
    <h2 class="mb-4">Consulta de Trámite</h2>

    <div class="card">
        <div class="card-body">
            <form id="consulta-form">
                <div class="mb-3">
                    <label for="pregunta" class="form-label">Escribe o di tu pregunta:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="pregunta" name="pregunta" placeholder="Ejemplo: ¿En qué estado está mi trámite 12345?" required>
                        <button type="button" id="voice-btn" class="btn btn-secondary">
                            🎤
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Consultar</button>
            </form>

            <div class="mt-4">
                <h5>Respuesta:</h5>
                <div id="respuesta" class="alert alert-info" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('consulta-form').addEventListener('submit', function(event) {
    event.preventDefault();
    enviarConsulta(); // 🔹 Se usa la misma función
});

// 🎙️ **Reconocimiento de voz con auto-envío**
const voiceButton = document.getElementById("voice-btn");
const preguntaInput = document.getElementById("pregunta");
const respuestaDiv = document.getElementById("respuesta");

if ('webkitSpeechRecognition' in window) {
    const recognition = new webkitSpeechRecognition();
    recognition.lang = "es-ES"; 
    recognition.continuous = false; 
    recognition.interimResults = false; 

    voiceButton.addEventListener("click", function () {
        recognition.start();
        preguntaInput.placeholder = "Escuchando...";
    });

    recognition.onresult = function (event) {
        const transcript = event.results[0][0].transcript;
        preguntaInput.value = transcript;
        preguntaInput.placeholder = "Consulta lista, enviando...";
        enviarConsulta(); // 🔹 Auto-envía la consulta al hablar
    };

    recognition.onerror = function (event) {
        console.error("Error en reconocimiento de voz:", event);
        preguntaInput.placeholder = "Error en reconocimiento de voz";
    };
} else {
    voiceButton.disabled = true;
    voiceButton.title = "Tu navegador no soporta reconocimiento de voz";
}

// 🔹 **FUNCIÓN REUTILIZABLE PARA ENVIAR CONSULTAS**
function enviarConsulta() {
    let pregunta = preguntaInput.value;

    fetch('/consultar-tramite', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ pregunta: pregunta })
    })
    .then(response => response.json())
    .then(data => {
        if (data.tipo === 'redirect') {
            // 🔄 **Redirigir al listado con el filtro aplicado**
            window.location.href = data.url;
        } else {
            // 📢 **Mostrar la respuesta en el chat**
            respuestaDiv.innerHTML = data.respuesta;
            respuestaDiv.style.display = 'block';

            // 🔊 **Leer la respuesta en voz alta**
            hablarRespuesta(data.respuesta);
        }
    })
    .catch(error => {
        respuestaDiv.innerHTML = "Error al consultar el trámite.";
        respuestaDiv.style.display = 'block';
    });
}


// 🔊 **FUNCIÓN PARA HABLAR LA RESPUESTA**
function hablarRespuesta(texto) {
    if ('speechSynthesis' in window) {
        let utterance = new SpeechSynthesisUtterance(texto);
        utterance.lang = 'es-ES'; // 🟢 Español
        speechSynthesis.speak(utterance);
    } else {
        console.warn("Tu navegador no soporta síntesis de voz.");
    }
}
</script>
@endsection
