<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OllamaChatService
{
    protected string $baseUrl = 'http://localhost:11434';

    public function chat(string $prompt): string
    {
        $response = Http::timeout(120)->post(
            $this->baseUrl.'/api/chat',
            [
                'model' => 'llama3:8b',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'stream' => false,
            ]
        );

        if ($response->failed()) {
            throw new \Exception('Ollama API error');
        }

        return $response->json('message.content');
    }
}
