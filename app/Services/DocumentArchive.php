<?php

namespace App\Services;

use App\Models\ContractTemplate;
use App\Models\DocumentAuditLog;
use App\Models\GeneratedDocument;
use App\Models\LoanRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Orchestre la génération DOCX, l'archivage en base et l'audit trail.
 */
class DocumentArchive
{
    public function __construct(
        private ContractDocxRenderer     $renderer,
        private ContractVariableResolver $resolver,
        private AuditLogger              $audit,
    ) {}

    /**
     * Génère le DOCX, le stocke dans generated_documents et logue l'action.
     * Toujours crée un nouveau fichier (chaque appel = nouveau document archivé).
     */
    public function generate(LoanRequest $loan, ContractTemplate $template, string $locale = 'fr'): GeneratedDocument
    {
        if (!$template->hasDocxTemplate()) {
            throw new \RuntimeException(
                "Le modèle \"{$template->name}\" n'a pas de template DOCX uploadé."
            );
        }

        $vars    = $this->resolver->resolve($loan, $locale);
        $absPath = $this->renderer->generate($loan, $template, $locale);
        $relPath = 'generated-contracts/' . basename($absPath);
        $checksum = hash_file('sha256', $absPath);

        $doc = GeneratedDocument::create([
            'loan_request_id'      => $loan->id,
            'contract_template_id' => $template->id,
            'generated_by'         => Auth::id(),
            'template_version'     => $template->docx_version,
            'locale'               => $locale,
            'docx_path'            => $relPath,
            'checksum'             => $checksum,
            'vars_snapshot'        => $vars,
        ]);

        $this->audit->log(
            DocumentAuditLog::ACTION_DOCUMENT_GENERATED,
            $loan,
            $template,
            $template->docx_version,
            ['generated_document_id' => $doc->id, 'locale' => $locale],
        );

        return $doc;
    }

    /**
     * Retourne le chemin absolu d'un DOCX généré.
     * Réutilise le fichier existant si la version du template n'a pas changé ;
     * sinon régénère.
     */
    public function getOrGenerate(LoanRequest $loan, ContractTemplate $template, string $locale = 'fr'): string
    {
        $currentVars = $this->resolver->resolve($loan, $locale);

        $existing = GeneratedDocument::where('loan_request_id', $loan->id)
            ->where('contract_template_id', $template->id)
            ->where('template_version', $template->docx_version)
            ->where('locale', $locale)
            ->latest()
            ->first();

        if ($existing) {
            $absPath = storage_path('app/' . $existing->docx_path);
            // Réutilise le cache uniquement si le fichier existe ET que les données du dossier n'ont pas changé
            $snapshot = (array) ($existing->vars_snapshot ?? []);
            ksort($snapshot);
            $current  = $currentVars;
            ksort($current);
            if (file_exists($absPath) && $snapshot === $current) {
                $this->audit->log(
                    DocumentAuditLog::ACTION_DOCUMENT_DOWNLOADED,
                    $loan,
                    $template,
                    $template->docx_version,
                    ['generated_document_id' => $existing->id],
                );
                return $absPath;
            }
        }

        $doc = $this->generate($loan, $template, $locale);
        return storage_path('app/' . $doc->docx_path);
    }
}
