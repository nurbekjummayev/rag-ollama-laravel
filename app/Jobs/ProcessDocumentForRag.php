<?php

namespace App\Jobs;

use App\Models\Document;
use App\Services\DocumentParserService;
use App\Services\EmbeddingService;
use App\Services\TextChunkingService;
use App\Services\VectorStoreService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessDocumentForRag implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Document $document) {}

    public function handle(
        DocumentParserService $parser,
        TextChunkingService $chunker,
        EmbeddingService $embedder,
        VectorStoreService $vectorStore
    ) {
        Log::info('RAG job started', [
            'document_id' => $this->document->id,
        ]);

        $vectorStore->createCollectionIfNotExists();

        $text = $parser->extractText($this->document->file_path);
        Log::info('Text extracted', ['length' => strlen($text)]);

        $chunks = $chunker->chunk($text);
        Log::info('Chunks created', ['count' => count($chunks)]);

        foreach ($chunks as $i => $chunk) {
            $vector = $embedder->embed($chunk);
            $pointId = (string) Str::uuid();

            Log::info('Embedding created', ['index' => $i]);

            $vectorStore->upsert(
                id: $pointId,
                vector: $vector,
                payload: [
                    'document_id' => $this->document->id,
                    'chunk_index' => $i,
                    'text' => $chunk,
                ]
            );
        }

        Log::info('RAG job finished');
    }
}
