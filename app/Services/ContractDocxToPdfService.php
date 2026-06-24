<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

/**
 * Convertit un fichier DOCX (déjà généré avec variables remplacées) en PDF.
 * Utilise PhpWord + DomPDF, tous deux déjà installés via Composer.
 */
class ContractDocxToPdfService
{
    /**
     * Convertit le DOCX en PDF et retourne le chemin absolu du fichier PDF temporaire.
     */
    public function convert(string $docxAbsPath): string
    {
        Settings::setPdfRendererPath(base_path('vendor/dompdf/dompdf'));
        Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);

        $phpWord = IOFactory::load($docxAbsPath);

        $pdfPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR
            . 'contract_' . uniqid('', true) . '.pdf';

        $writer = IOFactory::createWriter($phpWord, 'PDF');
        $writer->save($pdfPath);

        return $pdfPath;
    }
}
