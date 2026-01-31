<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    protected string $baseUrl = 'http://localhost:11434';

    public function embed(string $text): array
    {
        $res = Http::post($this->baseUrl.'/api/embeddings', [
            'model' => 'nomic-embed-text',
            'prompt' => $text,
        ]);

        return $res->json('embedding');
    }
}
