<?php

namespace App\Services;

use App\Models\LoanRequest;

/**
 * Résout les variables {balise} → valeur pour un dossier de prêt.
 * Le DOCX est rédigé directement dans la langue du client — pas de traduction automatique.
 * Seules les données du dossier sont injectées.
 */
class ContractVariableResolver
{
    public function __construct(
        private ContractService $contractService,
    ) {}

    /**
     * Retourne le tableau complet [{balise} => valeur_XML_escaped]
     * à partir d'un LoanRequest.
     */
    public function resolve(LoanRequest $loan, string $locale = 'fr'): array
    {
        return $this->escapeXml($this->buildRawVars($loan, $locale));
    }

    /**
     * Retourne les variables avec des données de démonstration (aperçu admin).
     */
    public function resolveSample(string $locale = 'fr'): array
    {
        return $this->escapeXml($this->sampleVars($locale));
    }

    /**
     * Vérifie quelles variables du DOCX ne sont pas couvertes par resolve().
     *
     * @param  string[] $detectedVars  ex: ['reference','nom_client','devise',...]
     */
    public function getMissing(array $detectedVars, LoanRequest $loan, string $locale = 'fr'): array
    {
        $resolved = $this->resolve($loan, $locale);
        $keys     = array_map(fn($k) => trim($k, '{}'), array_keys($resolved));

        return array_values(array_filter(
            $detectedVars,
            fn($v) => !in_array($v, $keys, true)
        ));
    }

    // ── Internals ────────────────────────────────────────────────────────────────

    private function buildRawVars(LoanRequest $loan, string $locale): array
    {
        // Ensure relations are loaded
        if (!$loan->relationLoaded('client')) {
            $loan->load('client');
        }
        if (!$loan->relationLoaded('admin')) {
            $loan->load('admin');
        }

        $client = $loan->client;
        $gender = $this->resolveGender($loan, $locale);

        $vars = [
            '{reference}'       => $loan->reference ?? '',
            '{archive}'         => $loan->archive_ref ?? '',
            '{nom_client}'      => $client?->name ?? $loan->name ?? '',
            '{adresse_client}'  => $client?->address ?? $loan->address ?? '',
            '{date_naissance}'  => $client?->birth_date?->format('d/m/Y') ?? '',
            '{type_identite}'   => $this->contractService->translateIdType(
                                    $client?->id_type ?? '', $locale),
            '{numero_identite}' => $client?->id_number ?? $client?->npi ?? $loan->npi ?? '',
            '{agent_suivi}'     => $loan->agent_suivi ?? $loan->admin?->name ?? '',
            '{directeur}'       => $loan->directeur ?? '',
            '{montant}'         => number_format((float)$loan->amount, 2, ',', ' '),
            '{devise}'          => $loan->currency ?? 'EUR',
            '{duree}'           => (string) ($loan->darly ?? ''),
            '{mensualite}'      => number_format((float)$loan->monthly_payment, 2, ',', ' '),
            '{taux}'            => number_format((float)$loan->interest_rate, 2, ',', '.'),
            '{frais_admin}'     => number_format((float)$loan->admin_fees, 2, ',', ' '),
            '{compte_bancaire}' => $loan->bank_account ?? '',
            '{date}'            => $loan->validated_at?->format('d/m/Y') ?? now()->format('d/m/Y'),
            '{societe}'         => config('app.company_name', 'CREDIXA INVESTI'),
        ];

        // Variables de genre
        $vars = array_merge($vars, $gender);

        // Champs personnalisés saisis à la création du dossier
        foreach ((array) ($loan->extra_fields ?? []) as $key => $value) {
            $vars['{' . $key . '}'] = (string) $value;
        }

        return $vars;
    }

    private function resolveGender(LoanRequest $loan, string $locale): array
    {
        $gender = $loan->client?->gender ?? 'male';
        $female = $gender === 'female';

        return match ($locale) {
            'pl' => [
                '{ne_e}'         => $female ? 'urodzona' : 'urodzony',
                '{nee}'          => $female ? 'urodzona' : 'urodzony',
                '{denomme_e}'    => $female ? 'zwana' : 'zwany',
                '{zamieszkal_a}' => $female ? 'zamieszkała' : 'zamieszkały',
                '{e}'            => $female ? 'a' : 'y',
            ],
            'es' => [
                '{ne_e}'         => $female ? 'nacida' : 'nacido',
                '{nee}'          => $female ? 'nacida' : 'nacido',
                '{denomme_e}'    => $female ? 'denominada' : 'denominado',
                '{zamieszkal_a}' => '',
                '{e}'            => $female ? 'a' : 'o',
            ],
            'en' => [
                '{ne_e}'         => 'born',
                '{nee}'          => 'born',
                '{denomme_e}'    => 'referred to as',
                '{zamieszkal_a}' => 'residing at',
                '{e}'            => '',
            ],
            default => [
                '{ne_e}'         => $female ? 'née' : 'né',
                '{nee}'          => $female ? 'née' : 'né',
                '{denomme_e}'    => $female ? 'dénommée' : 'dénommé',
                '{zamieszkal_a}' => '',
                '{e}'            => $female ? 'e' : '',
            ],
        };
    }

    private function sampleVars(string $locale): array
    {
        $gender = match ($locale) {
            'pl' => ['{ne_e}' => 'urodzony', '{nee}' => 'urodzony', '{denomme_e}' => 'zwany', '{zamieszkal_a}' => 'zamieszkały', '{e}' => 'y'],
            'es' => ['{ne_e}' => 'nacido',   '{nee}' => 'nacido',   '{denomme_e}' => 'denominado', '{zamieszkal_a}' => '', '{e}' => 'o'],
            'en' => ['{ne_e}' => 'born',     '{nee}' => 'born',     '{denomme_e}' => 'referred to as', '{zamieszkal_a}' => 'residing at', '{e}' => ''],
            default => ['{ne_e}' => 'né',    '{nee}' => 'né',       '{denomme_e}' => 'dénommé', '{zamieszkal_a}' => '', '{e}' => ''],
        };

        return array_merge([
            '{reference}'       => 'CR-2026-0001',
            '{archive}'         => 'ARC-2026-0001',
            '{nom_client}'      => 'Jean DUPONT',
            '{adresse_client}'  => '12 rue de la Paix, 75001 Paris',
            '{date_naissance}'  => '15/06/1985',
            '{type_identite}'   => 'Passeport',
            '{numero_identite}' => 'AB123456',
            '{agent_suivi}'     => 'Marie MARTIN',
            '{directeur}'       => 'Pierre DUPONT',
            '{montant}'         => '15 000,00',
            '{devise}'          => 'EUR',
            '{duree}'           => '36',
            '{mensualite}'      => '450,00',
            '{taux}'            => '5,50',
            '{frais_admin}'     => '250,00',
            '{compte_bancaire}' => 'FR76 1234 5678 9012 3456 7890 123',
            '{date}'            => now()->format('d/m/Y'),
            '{societe}'         => 'CREDIXA INVESTI',
        ], $gender);
    }

    private function escapeXml(array $vars): array
    {
        return array_map(
            fn($v) => htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8'),
            $vars
        );
    }
}
