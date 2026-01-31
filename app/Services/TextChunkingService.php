<?php

namespace App\Services;

class TextChunkingService
{
    public function chunk(string $text, int $chunkSize = 800, int $overlap = 100): array
    {
        $text = preg_replace('/\s+/', ' ', trim($text));

        $words = explode(' ', $text);
        $chunks = [];

        $start = 0;
        $count = count($words);

        while ($start < $count) {
            $end = min($start + $chunkSize, $count);
            $chunkWords = array_slice($words, $start, $end - $start);

            $chunks[] = implode(' ', $chunkWords);

            $start += ($chunkSize - $overlap);
        }

        return $chunks;
    }
}
