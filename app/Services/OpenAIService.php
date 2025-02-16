<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService {
    public function consultarTramite($pregunta) {
        $apiKey = env('OPENAI_API_KEY');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'Eres un asistente que responde sobre estados de trámites.'],
                ['role' => 'user', 'content' => $pregunta]
            ],
            'temperature' => 0.5
        ]);

        return $response->json()['choices'][0]['message']['content'] ?? 'No pude obtener la información.';
    }
}
