<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;

class DocumentParserService
{
    public function extractText(string $filePath): string
    {
        $fullPath = storage_path('app/public/'.$filePath);

        if (! file_exists($fullPath)) {
            throw new \Exception('File not found');
        }

        return match (pathinfo($fullPath, PATHINFO_EXTENSION)) {
            'pdf' => $this->fromPdf($fullPath),
            'docx' => $this->fromDocx($fullPath),
            default => throw new \Exception('Unsupported file type'),
        };
    }

    protected function fromPdf(string $path): string
    {
        $output = shell_exec('pdftotext '.escapeshellarg($path).' -');

        return trim($output ?? '');
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
