<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;

class DocumentParserService
{
    public function extractText(string $filePath): string
    {
//        $text = $this->fromPdf($filePath);
//
//        // Agar text juda kam bo‘lsa → scan deb hisoblaymiz
//        if (strlen($text) < 100) {
            $text = app(OcrService::class)
                ->extractTextFromPdf($filePath);
//        }

        return $text;
    }

    protected function fromPdf(string $filePath): string
    {
        $path = storage_path('app/public/' . $filePath);
        return shell_exec(
            'pdftotext ' . escapeshellarg($path) . ' -'
        ) ?? '';
    }

    protected function fromDocx(string $path): string
    {
        $phpWord = IOFactory::load($path);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText().' ';
                }
            }
        }

        return trim($text);
    }
}
