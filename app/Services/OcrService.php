<?php

namespace App\Services;

class OcrService
{
    public function extractTextFromPdf(string $filePath): string
    {
        $pdf = storage_path('app/public/' . $filePath);
        $tempDir = storage_path('app/ocr/' . uniqid());
        mkdir($tempDir, 0777, true);

        // PDF → PNG
        shell_exec(
            "pdftoppm -png -r 300 " .
            escapeshellarg($pdf) . " " .
            escapeshellarg($tempDir . '/page')
        );

        $text = '';

        foreach (glob($tempDir . '/*.png') as $image) {
            $cmd = sprintf(
                'tesseract %s stdout -l uzb+rus+eng+uzb_cyrl --oem 1 --psm 6',
                escapeshellarg($image)
            );
            $text .= shell_exec($cmd) . "\n";
        }
\Log::info($text);
        // cleanup
//        array_map('unlink', glob("$tempDir/*"));
//        rmdir($tempDir)

        return trim($text);
    }}
