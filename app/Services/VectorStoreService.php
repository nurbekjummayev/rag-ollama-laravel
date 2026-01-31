<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VectorStoreService
{
    protected string $baseUrl = 'http://localhost:6333';
    protected string $collection = 'documents';

    public function createCollectionIfNotExists(int $vectorSize = 768): void
    {
        Http::put("{$this->baseUrl}/collections/{$this->collection}", [
            'vectors' => [
                'size' => $vectorSize,
                'distance' => 'Cosine',
            ],
        ]);
    }

    public function upsert(string $id, array $vector, array $payload): void
    {
        $response = Http::put(
            "{$this->baseUrl}/collections/{$this->collection}/points?wait=true",
            [
                'points' => [
                    [
                        'id' => (string) $id,
                        'vector' => $vector,
                        'payload' => $payload,
                    ],
                ],
            ]
        );

        if ($response->failed()) {
            \Log::error('Qdrant upsert failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    public function search(array $vector, int $limit = 5): array
    {
        $res = Http::post("{$this->baseUrl}/collections/{$this->collection}/points/search", [
            'vector' => $vector,
            'limit' => $limit,
            'with_payload' => true,
        ]);

        return $res->json('result') ?? [];
    }
}
