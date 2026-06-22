<?php

namespace App\Services;

class GroqDocxRendererService
{
    public function analyzeAndPosition(string $docxPath): array
    {
        return ['images' => [], '__report' => ''];
    }

    public function getPositions(string $docxPath): array
    {
        return ['images' => [], '__report' => ''];
    }

    public function injectPositionedImages(string $html, string $docxPath, array $groqResult): array
    {
        return ['html' => $html, 'report' => ''];
    }
}
