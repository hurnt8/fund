<?php

namespace App\Services;

use App\Models\ContractTemplate;
use App\Models\LoanRequest;

class ContractService
{
    // ── Labels traduits pour id_type ─────────────────────────────────────────
    private const ID_TYPE_LABELS = [
        'fr' => [
            'passeport'       => 'Passeport',
            'cni'             => "Carte nationale d'identité",
            'permis_conduire' => 'Permis de conduire',
            'titre_sejour'    => 'Titre de séjour',
            'autre'           => 'Autre document',
        ],
        'en' => [
            'passeport'       => 'Passport',
            'cni'             => 'National identity card',
            'permis_conduire' => "Driver's license",
            'titre_sejour'    => 'Residence permit',
            'autre'           => 'Other document',
        ],
        'pl' => [
            'passeport'       => 'Paszport',
            'cni'             => 'Dowód osobisty',
            'permis_conduire' => 'Prawo jazdy',
            'titre_sejour'    => 'Karta pobytu',
            'autre'           => 'Inny dokument',
        ],
        'es' => [
            'passeport'       => 'Pasaporte',
            'cni'             => 'Documento nacional de identidad',
            'permis_conduire' => 'Permiso de conducir',
            'titre_sejour'    => 'Permiso de residencia',
            'autre'           => 'Otro documento',
        ],
    ];

    // ── En-têtes pays ────────────────────────────────────────────────────────
    private array $countryHeaders = [
        'fr' => "RÉPUBLIQUE FRANÇAISE\nMinistère de la Justice\nTribunal de Première Instance",
        'pl' => "RÉPUBLIQUE DE POLOGNE\nMinistère de la Justice et de la Législation des Droits Humains\nTribunal de Première Instance de Pologne\nSecrétariat Général",
        'en' => "UNITED KINGDOM\nMinistry of Justice\nFirst Instance Tribunal",
        'es' => "REINO DE ESPAÑA\nMinisterio de Justicia\nTribunal de Primera Instancia",
    ];

    // ── Traductions des libellés du contrat ──────────────────────────────────
    private array $translations = [
        'fr' => [
            'title'          => 'CONTRAT DE PRÊT FINANCIER',
            'ref_label'      => 'Référence dossier',
            'archive_label'  => 'ARCHIVE N°',
            'between'        => 'ENTRE LES PARTIES',
            'lender_label'   => 'La société prêteuse',
            'lender_desc'    => 'représentée par son représentant légal dûment habilité, ci-après dénommée « la Société Prêteuse ».',
            'borrower_label' => 'L\'emprunteur',
            'borrower_desc'  => 'demeurant à {adresse_client}, {ne_e} le {date_naissance}, dont le numéro de {type_identite} est {numero_identite}, ci-après {denomme_e} « l\'Emprunteur ».',
            'agent_label'    => 'L\'agent de suivi du dossier',
            'agent_desc'     => 'agissant en qualité de responsable du suivi administratif et de la gestion du dossier de financement.',
            'finance_title'  => 'INFORMATIONS DU FINANCEMENT',
            'amount_label'   => 'Montant du crédit accordé',
            'duration_label' => 'Durée du financement',
            'months'         => 'mois',
            'monthly_label'  => 'Montant de remboursement mensuel',
            'rate_label'     => 'Taux d\'intérêt annuel',
            'fees_label'     => 'Frais administratifs et traitement du dossier',
            'bank_label'     => 'Coordonnées de règlement',
            'art1_title'     => 'ARTICLE 1 : OBJET DU CONTRAT',
            'art1_body'      => "Le présent contrat définit les conditions selon lesquelles la société CREDIXA INVESTI accorde un financement d'un montant total de {montant} {devise} à l'Emprunteur {nom_client}.\nCe financement est destiné à accompagner les besoins financiers et le développement des activités déclarées par l'Emprunteur.\nLes fonds seront mis à disposition après validation complète du dossier et acceptation des présentes conditions contractuelles.",
            'art2_title'     => 'ARTICLE 2 : CONDITIONS DE REMBOURSEMENT',
            'art2_body'      => "L'Emprunteur s'engage à rembourser le crédit selon l'échéancier suivant :\nDurée totale : {duree} mois ;\nPaiement mensuel : {mensualite} {devise} ;\nPaiement effectué à la fin de chaque mois jusqu'au remboursement intégral du crédit.\nLe remboursement comprend le capital, les intérêts applicables ainsi que les frais convenus.",
            'art3_title'     => 'ARTICLE 3 : INTÉRÊTS DU CRÉDIT',
            'art3_body'      => "Le financement accordé par CREDIXA INVESTI est soumis à un taux d'intérêt annuel de : {taux} %.\nLes intérêts sont calculés conformément aux conditions du présent contrat.",
            'art4_title'     => 'ARTICLE 4 : ENGAGEMENTS DE L\'EMPRUNTEUR',
            'art4_body'      => "L'Emprunteur reconnaît :\n- avoir reçu toutes les informations relatives au financement ;\n- accepter les conditions du présent contrat ;\n- s'engager à respecter les échéances prévues ;\n- rembourser la totalité des sommes dues.\nTout retard de paiement pourra entraîner l'application des mesures prévues par la réglementation applicable.",
            'art5_title'     => 'ARTICLE 5 : FRAIS ADMINISTRATIFS',
            'art5_body'      => "Les frais liés à l'étude, la préparation et la gestion administrative du dossier sont fixés à : {frais_admin} {devise}.\nCes frais font partie des conditions de traitement du financement.",
            'art6_title'     => 'ARTICLE 6 : COORDONNÉES DE RÈGLEMENT',
            'art6_body'      => "Les paiements devront être effectués sur le compte communiqué par la société prêteuse.\nTitulaire : CREDIXA INVESTI / Mandataire autorisé\nCompte bancaire : {compte_bancaire}",
            'art7_title'     => 'ARTICLE 7 : RÉSOLUTION DES LITIGES',
            'art7_body'      => "En cas de désaccord relatif à l'exécution ou à l'interprétation du présent contrat, les parties rechercheront en priorité une solution amiable.\nÀ défaut, le litige pourra être soumis aux autorités compétentes.",
            'art8_title'     => 'ARTICLE 8 : ACCEPTATION ET SIGNATURE',
            'art8_body'      => "Les parties déclarent avoir lu et accepté toutes les clauses du présent contrat.\nLe présent accord entre en vigueur après signature des parties.",
            'made_at'        => 'Fait le',
            'sig_lender'     => 'POUR CREDIXA INVESTI',
            'sig_agent'      => 'RESPONSABLE DU DOSSIER',
            'sig_borrower'   => 'L\'EMPRUNTEUR',
        ],
        'pl' => [
            'title'          => 'UMOWA POŻYCZKI FINANSOWEJ',
            'ref_label'      => 'Numer referencyjny',
            'archive_label'  => 'ARCHIWUM NR',
            'between'        => 'MIĘDZY STRONAMI',
            'lender_label'   => 'Firma pożyczkowa',
            'lender_desc'    => 'reprezentowana przez należycie umocowanego przedstawiciela prawnego, zwana dalej „Pożyczkodawcą".',
            'borrower_label' => 'Pożyczkobiorca',
            'borrower_desc'  => '{zamieszkal_a} pod adresem {adresse_client}, {ne_e} {date_naissance}, numer dokumentu {type_identite} : {numero_identite}, {denomme_e} dalej „Pożyczkobiorcą".',
            'agent_label'    => 'Opiekun sprawy',
            'agent_desc'     => 'działający jako odpowiedzialny za nadzór administracyjny i zarządzanie dokumentacją finansowania.',
            'finance_title'  => 'INFORMACJE O FINANSOWANIU',
            'amount_label'   => 'Przyznana kwota kredytu',
            'duration_label' => 'Okres finansowania',
            'months'         => 'miesięcy',
            'monthly_label'  => 'Miesięczna rata spłaty',
            'rate_label'     => 'Roczna stopa procentowa',
            'fees_label'     => 'Opłaty administracyjne i obsługa wniosku',
            'bank_label'     => 'Dane rozliczeniowe',
            'art1_title'     => 'ARTYKUŁ 1 : PRZEDMIOT UMOWY',
            'art1_body'      => "Niniejsza umowa określa warunki, na jakich firma CREDIXA INVESTI udziela finansowania w łącznej kwocie {montant} {devise} Pożyczkobiorcy {nom_client}.\nFinansowanie to przeznaczone jest na wsparcie potrzeb finansowych oraz rozwój działalności zadeklarowanej przez Pożyczkobiorcę.\nŚrodki zostaną udostępnione po pełnej weryfikacji dokumentacji i akceptacji niniejszych warunków umownych.",
            'art2_title'     => 'ARTYKUŁ 2 : WARUNKI SPŁATY',
            'art2_body'      => "Pożyczkobiorca zobowiązuje się do spłaty kredytu zgodnie z harmonogramem :\nŁączny okres : {duree} miesięcy ;\nMiesięczna płatność : {mensualite} {devise} ;\nPłatność dokonywana na koniec każdego miesiąca do całkowitej spłaty kredytu.",
            'art3_title'     => 'ARTYKUŁ 3 : ODSETKI KREDYTOWE',
            'art3_body'      => "Finansowanie udzielone przez CREDIXA INVESTI podlega rocznej stopie procentowej wynoszącej : {taux} %.",
            'art4_title'     => 'ARTYKUŁ 4 : ZOBOWIĄZANIA POŻYCZKOBIORCY',
            'art4_body'      => "Pożyczkobiorca potwierdza :\n- otrzymanie wszystkich informacji dotyczących finansowania ;\n- akceptację warunków niniejszej umowy ;\n- zobowiązanie do przestrzegania ustalonych terminów ;\n- spłatę wszystkich należnych kwot.",
            'art5_title'     => 'ARTYKUŁ 5 : OPŁATY ADMINISTRACYJNE',
            'art5_body'      => "Opłaty związane z analizą, przygotowaniem i administracyjną obsługą wniosku wynoszą : {frais_admin} {devise}.",
            'art6_title'     => 'ARTYKUŁ 6 : DANE ROZLICZENIOWE',
            'art6_body'      => "Płatności należy dokonywać na rachunek podany przez pożyczkodawcę.\nPosiadacz : CREDIXA INVESTI / Upoważniony pełnomocnik\nRachunek bankowy : {compte_bancaire}",
            'art7_title'     => 'ARTYKUŁ 7 : ROZSTRZYGANIE SPORÓW',
            'art7_body'      => "W przypadku jakiegokolwiek sporu dotyczącego wykonania lub interpretacji niniejszej umowy, strony będą szukać rozwiązania polubownego.\nW braku porozumienia spór może zostać przekazany właściwym organom.",
            'art8_title'     => 'ARTYKUŁ 8 : AKCEPTACJA I PODPIS',
            'art8_body'      => "Strony oświadczają, że zapoznały się i zaakceptowały wszystkie klauzule niniejszej umowy.",
            'made_at'        => 'Sporządzono dnia',
            'sig_lender'     => 'W IMIENIU CREDIXA INVESTI',
            'sig_agent'      => 'OPIEKUN SPRAWY',
            'sig_borrower'   => 'POŻYCZKOBIORCA',
        ],
        'en' => [
            'title'          => 'FINANCIAL LOAN AGREEMENT',
            'ref_label'      => 'File reference',
            'archive_label'  => 'ARCHIVE N°',
            'between'        => 'BETWEEN THE PARTIES',
            'lender_label'   => 'The lending company',
            'lender_desc'    => 'represented by its duly authorised legal representative, hereinafter referred to as "the Lender".',
            'borrower_label' => 'The borrower',
            'borrower_desc'  => 'residing at {adresse_client}, born on {date_naissance}, identity document {type_identite} N° {numero_identite}, hereinafter referred to as "the Borrower".',
            'agent_label'    => 'The case officer',
            'agent_desc'     => 'acting as responsible for administrative monitoring and management of the financing file.',
            'finance_title'  => 'FINANCING INFORMATION',
            'amount_label'   => 'Amount of credit granted',
            'duration_label' => 'Financing period',
            'months'         => 'months',
            'monthly_label'  => 'Monthly repayment amount',
            'rate_label'     => 'Annual interest rate',
            'fees_label'     => 'Administrative and processing fees',
            'bank_label'     => 'Payment details',
            'art1_title'     => 'ARTICLE 1: PURPOSE OF THE AGREEMENT',
            'art1_body'      => "This agreement defines the conditions under which CREDIXA INVESTI grants financing totalling {montant} {devise} to the Borrower {nom_client}.\nThe funds will be made available after full validation of the file and acceptance of these contractual conditions.",
            'art2_title'     => 'ARTICLE 2: REPAYMENT CONDITIONS',
            'art2_body'      => "The Borrower agrees to repay the loan according to the following schedule:\nTotal duration: {duree} months;\nMonthly payment: {mensualite} {devise};\nPayment made at the end of each month until full repayment.",
            'art3_title'     => 'ARTICLE 3: CREDIT INTEREST',
            'art3_body'      => "The financing granted by CREDIXA INVESTI is subject to an annual interest rate of: {taux} %.",
            'art4_title'     => 'ARTICLE 4: BORROWER\'S COMMITMENTS',
            'art4_body'      => "The Borrower acknowledges:\n- having received all information relating to the financing;\n- accepting the conditions of this agreement;\n- committing to respect the scheduled deadlines;\n- repaying all amounts due.",
            'art5_title'     => 'ARTICLE 5: ADMINISTRATIVE FEES',
            'art5_body'      => "The fees related to the study, preparation and administrative management of the file are set at: {frais_admin} {devise}.",
            'art6_title'     => 'ARTICLE 6: PAYMENT DETAILS',
            'art6_body'      => "Payments shall be made to the account communicated by the lending company.\nAccount holder: CREDIXA INVESTI / Authorised representative\nBank account: {compte_bancaire}",
            'art7_title'     => 'ARTICLE 7: DISPUTE RESOLUTION',
            'art7_body'      => "In the event of any dispute relating to the execution or interpretation of this agreement, the parties will seek an amicable solution first.\nFailing that, the dispute may be submitted to the competent authorities.",
            'art8_title'     => 'ARTICLE 8: ACCEPTANCE AND SIGNATURE',
            'art8_body'      => "The parties declare to have read and accepted all clauses of this agreement.",
            'made_at'        => 'Signed on',
            'sig_lender'     => 'FOR CREDIXA INVESTI',
            'sig_agent'      => 'CASE OFFICER',
            'sig_borrower'   => 'THE BORROWER',
        ],
        'es' => [
            'title'          => 'CONTRATO DE PRÉSTAMO FINANCIERO',
            'ref_label'      => 'Referencia del expediente',
            'archive_label'  => 'ARCHIVO N°',
            'between'        => 'ENTRE LAS PARTES',
            'lender_label'   => 'La sociedad prestamista',
            'lender_desc'    => 'representada por su representante legal debidamente habilitado, en adelante denominada "la Sociedad Prestamista".',
            'borrower_label' => 'El prestatario',
            'borrower_desc'  => 'con domicilio en {adresse_client}, {ne_e} el {date_naissance}, documento de identidad {type_identite} N° {numero_identite}, {denomme_e} "el Prestatario".',
            'agent_label'    => 'El agente de seguimiento',
            'agent_desc'     => 'actuando como responsable del seguimiento administrativo y la gestión del expediente de financiación.',
            'finance_title'  => 'INFORMACIÓN DEL FINANCIAMIENTO',
            'amount_label'   => 'Importe del crédito concedido',
            'duration_label' => 'Duración de la financiación',
            'months'         => 'meses',
            'monthly_label'  => 'Cuota mensual de reembolso',
            'rate_label'     => 'Tipo de interés anual',
            'fees_label'     => 'Gastos administrativos y tramitación',
            'bank_label'     => 'Datos de pago',
            'art1_title'     => 'ARTÍCULO 1: OBJETO DEL CONTRATO',
            'art1_body'      => "El presente contrato define las condiciones bajo las cuales CREDIXA INVESTI concede una financiación por un importe total de {montant} {devise} al Prestatario {nom_client}.\nLos fondos se pondrán a disposición tras la validación completa del expediente.",
            'art2_title'     => 'ARTÍCULO 2: CONDICIONES DE REEMBOLSO',
            'art2_body'      => "El Prestatario se compromete a reembolsar el crédito según el siguiente calendario:\nDuración total: {duree} meses;\nPago mensual: {mensualite} {devise};\nPago efectuado a finales de cada mes hasta el reembolso íntegro.",
            'art3_title'     => 'ARTÍCULO 3: INTERESES DEL CRÉDITO',
            'art3_body'      => "La financiación concedida por CREDIXA INVESTI está sujeta a un tipo de interés anual del: {taux} %.",
            'art4_title'     => 'ARTÍCULO 4: COMPROMISOS DEL PRESTATARIO',
            'art4_body'      => "El Prestatario reconoce:\n- haber recibido toda la información relativa a la financiación;\n- aceptar las condiciones del presente contrato;\n- comprometerse a respetar los plazos previstos;\n- reembolsar la totalidad de las sumas adeudadas.",
            'art5_title'     => 'ARTÍCULO 5: GASTOS ADMINISTRATIVOS',
            'art5_body'      => "Los gastos relacionados con el estudio, preparación y gestión administrativa del expediente se fijan en: {frais_admin} {devise}.",
            'art6_title'     => 'ARTÍCULO 6: DATOS DE PAGO',
            'art6_body'      => "Los pagos deberán efectuarse en la cuenta comunicada por la sociedad prestamista.\nTitular: CREDIXA INVESTI / Representante autorizado\nCuenta bancaria: {compte_bancaire}",
            'art7_title'     => 'ARTÍCULO 7: RESOLUCIÓN DE LITIGIOS',
            'art7_body'      => "En caso de desacuerdo relativo a la ejecución o interpretación del presente contrato, las partes buscarán prioritariamente una solución amistosa.",
            'art8_title'     => 'ARTÍCULO 8: ACEPTACIÓN Y FIRMA',
            'art8_body'      => "Las partes declaran haber leído y aceptado todas las cláusulas del presente contrato.",
            'made_at'        => 'Firmado el',
            'sig_lender'     => 'POR CREDIXA INVESTI',
            'sig_agent'      => 'RESPONSABLE DEL EXPEDIENTE',
            'sig_borrower'   => 'EL PRESTATARIO',
        ],
    ];

    // ── Variables dynamiques issues du dossier ───────────────────────────────
    public function getVariables(LoanRequest $loan): array
    {
        $client    = $loan->client;
        $admin     = $loan->admin;
        $locale    = $loan->contract_language ?? 'fr';
        $birthDate = $client?->birth_date
            ? $client->birth_date->format('d/m/Y')
            : '';

        $idType      = $client?->id_type ?? '';
        $idTypeLabel = self::ID_TYPE_LABELS[$locale][$idType]
                    ?? self::ID_TYPE_LABELS['fr'][$idType]
                    ?? $idType;

        // ── Accord de genre ──────────────────────────────────────────────────
        $gender = $client?->gender ?? 'N';
        $isFem  = $gender === 'F';

        $genderVars = [
            '{ne_e}'         => match($locale) {
                'fr'    => $isFem ? 'née'      : 'né',
                'pl'    => $isFem ? 'urodzona' : 'urodzony',
                'es'    => $isFem ? 'nacida'   : 'nacido',
                default => 'born',
            },
            '{denomme_e}'    => match($locale) {
                'fr'    => $isFem ? 'dénommée'   : 'dénommé',
                'pl'    => $isFem ? 'zwana'       : 'zwany',
                'es'    => $isFem ? 'denominada'  : 'denominado',
                default => 'referred to as',
            },
            '{zamieszkal_a}' => $isFem ? 'zamieszkała' : 'zamieszkały',
            '{e}'            => match($locale) {
                'fr'    => $isFem ? 'e' : '',
                'pl'    => $isFem ? 'a' : 'y',
                'es'    => $isFem ? 'a' : 'o',
                default => '',
            },
        ];

        // Balises personnalisées saisies dans la modale (ex: {ville}, {nom_projet})
        $extraVars = [];
        foreach ($loan->extra_fields ?? [] as $key => $value) {
            $extraVars['{' . $key . '}'] = (string) $value;
        }

        return array_merge([
            '{reference}'       => $loan->reference ?? '',
            '{archive}'         => $loan->archive_ref ?? '',
            '{nom_client}'      => $loan->name,
            '{adresse_client}'  => $loan->address ?? ($client?->address ?? ''),
            '{date_naissance}'  => $birthDate,
            '{numero_identite}' => $client?->id_number ?? '',
            '{type_identite}'   => $idTypeLabel,
            '{agent_suivi}'     => $loan->agent_suivi ?: ($admin?->name ?? 'CREDIXA INVESTI'),
            '{montant}'         => number_format((float)$loan->amount, 2, ',', ' '),
            '{devise}'          => $loan->currency ?? 'EUR',
            '{duree}'           => $loan->darly ?? '',
            '{mensualite}'      => number_format((float)$loan->monthly_payment, 2, ',', ' '),
            '{taux}'            => $loan->interest_rate ?? 5,
            '{frais_admin}'     => $loan->admin_fees
                                    ? number_format((float)$loan->admin_fees, 2, ',', ' ')
                                    : '—',
            '{compte_bancaire}' => $loan->bank_account ?? '—',
            '{date}'            => $loan->validated_at
                                    ? $loan->validated_at->format('d/m/Y')
                                    : now()->format('d/m/Y'),
            '{societe}'         => 'CREDIXA INVESTI',
        ], $genderVars, $extraVars);
    }

    // ── Génère le contrat en FR (aperçu admin) ───────────────────────────────
    public function generateFr(LoanRequest $loan): string
    {
        return $this->buildHtml($loan, 'fr');
    }

    // ── Traduit et génère dans la langue du client (envoi PDF) ───────────────
    public function generateForClient(LoanRequest $loan): string
    {
        $locale = $loan->contract_language ?? 'fr';
        if (!isset($this->translations[$locale])) {
            $locale = 'fr';
        }
        return $this->buildHtml($loan, $locale);
    }

    private function buildHtml(LoanRequest $loan, string $locale): string
    {
        $t      = $this->translations[$locale];
        $vars   = $this->getVariables($loan);
        $header = $this->countryHeaders[$locale] ?? $this->countryHeaders['fr'];

        // Si le dossier a un modèle personnalisé de type HTML → l'utiliser
        $template = $loan->contractTemplate;
        if ($template && $template->template_type === 'html' && $template->content) {
            return $this->renderCustomTemplate($template, $t, $vars, $header);
        }

        // Fallback : vue Blade structurée
        return view('contracts.template', [
            't'       => $t,
            'vars'    => $vars,
            'header'  => $header,
            'loan'    => $loan,
            'tpl'     => $template,
        ])->render();
    }

    /**
     * Rend un template HTML personnalisé (stocké en BDD) avec toutes les balises substituées.
     * Injecte automatiquement le filigrane, les logos et les signatures du template.
     */
    private function renderCustomTemplate(ContractTemplate $template, array $t, array $vars, string $header): string
    {
        $content = $template->content;

        // Aplatir les clés de traduction en balises {clé}
        $translationVars = [];
        foreach ($t as $key => $value) {
            $translationVars['{' . $key . '}'] = $value;
        }
        $translationVars['{header}'] = $header;

        // Ordre : d'abord les traductions, ensuite les données (pour permettre {borrower_desc} contenant {nom_client})
        $all = array_merge($translationVars, $vars);

        // Double passe pour résoudre les balises imbriquées
        $rendered = str_replace(array_keys($all), array_values($all), $content);
        $rendered = str_replace(array_keys($all), array_values($all), $rendered);

        // ── Filigrane ───────────────────────────────────────────────────────
        $watermarkHtml = '';
        $watermarkCss  = '';
        if ($template->watermark_path) {
            $wmUrl = asset('storage/' . $template->watermark_path);
            $watermarkCss = '.crx-wm{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:0;background:url("' . $wmUrl . '") center/40% no-repeat;opacity:.08;}';
            $watermarkHtml = '<div class="crx-wm"></div>';
        }

        // ── Logos haut de page ───────────────────────────────────────────────
        $logosHtml = '';
        if ($template->logo_left_path || $template->logo_right_path) {
            $left  = $template->logo_left_path
                ? '<img src="' . asset('storage/' . $template->logo_left_path) . '" style="max-height:55px;max-width:150px;object-fit:contain;">'
                : '<span></span>';
            $right = $template->logo_right_path
                ? '<img src="' . asset('storage/' . $template->logo_right_path) . '" style="max-height:55px;max-width:150px;object-fit:contain;">'
                : '<span></span>';
            $logosHtml = '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #e0e0e0;">'
                . $left . $right
                . '</div>';
        }

        // ── Cachet + Signatures ──────────────────────────────────────────────
        $sigsHtml = '';
        if ($template->signature_admin_path || $template->signature_agent_path || $template->stamp_path) {
            $sigsHtml = '<div style="margin-top:24px;display:flex;justify-content:space-around;align-items:flex-end;gap:20px;">';
            foreach ([
                $template->signature_admin_path => 'max-height:55px;max-width:140px;object-fit:contain;',
                $template->stamp_path           => 'max-height:65px;max-width:140px;object-fit:contain;',
                $template->signature_agent_path => 'max-height:55px;max-width:140px;object-fit:contain;',
            ] as $path => $style) {
                if ($path) {
                    $sigsHtml .= '<div style="text-align:center;"><img src="' . asset('storage/' . $path) . '" style="' . $style . '"></div>';
                }
            }
            $sigsHtml .= '</div>';
        }

        return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><style>'
            . $this->contractCss()
            . $watermarkCss
            . '</style></head><body>'
            . $watermarkHtml
            . '<div class="page">'
            . $logosHtml
            . $rendered
            . $sigsHtml
            . '</div></body></html>';
    }

    // ── CSS partagé avec la vue Blade ────────────────────────────────────────
    public function contractCss(): string
    {
        return '
body{font-family:\'DejaVu Sans\',sans-serif;font-size:11px;color:#1a1a2e;line-height:1.7;margin:0;padding:0}
.page{padding:30px 40px}
.header{text-align:center;border-bottom:3px solid #0B1A2E;padding-bottom:14px;margin-bottom:20px}
.header-country{font-size:9px;color:#555;white-space:pre-line;margin-bottom:8px}
.header-title{font-size:18px;font-weight:bold;color:#0B1A2E;letter-spacing:1px;margin-bottom:6px}
.header-ref{font-size:9px;color:#444}
.section-title{font-size:10px;font-weight:bold;color:#C8A951;text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid #e0c97a;margin:18px 0 8px;padding-bottom:3px}
.parties-block{margin-bottom:10px}
.party-label{font-weight:bold;color:#0B1A2E}
.finance-table{width:100%;border-collapse:collapse;margin:10px 0 16px}
.finance-table td{padding:6px 10px;border:1px solid #dde;font-size:10.5px}
.finance-table td:first-child{background:#f5f7fb;font-weight:bold;width:55%;color:#0B1A2E}
.finance-table td:last-child{font-weight:bold;color:#C8A951;font-size:12px}
.article{margin-bottom:14px}
.article-title{font-weight:bold;color:#0B1A2E;font-size:10.5px;margin-bottom:5px}
.article-body{white-space:pre-line;font-size:10px;color:#333;line-height:1.75}
.signature-block{margin-top:30px;border-top:2px solid #0B1A2E;padding-top:14px}
.sig-date{margin-bottom:20px;font-size:10px}
.sig-row{display:table;width:100%}
.sig-cell{display:table-cell;width:33%;text-align:center;padding:10px 5px}
.sig-label{font-weight:bold;font-size:9px;text-transform:uppercase;color:#0B1A2E;border-top:1px solid #ccc;padding-top:5px;margin-top:40px}
.company-name{font-size:13px;font-weight:bold;color:#0B1A2E;letter-spacing:2px}
';
    }

    /**
     * Retourne les balises de traduction {clé} → valeur pour une locale donnée.
     * Utilisé pour l'aperçu multi-langue.
     */
    public function getTranslationVars(string $locale): array
    {
        $t      = $this->translations[$locale] ?? $this->translations['fr'];
        $header = $this->countryHeaders[$locale] ?? $this->countryHeaders['fr'];

        $vars = ['{header}' => $header];
        foreach ($t as $key => $value) {
            $vars['{' . $key . '}'] = $value;
        }
        return $vars;
    }

    public function substituteVars(string $content, array $vars): string
    {
        return str_replace(array_keys($vars), array_values($vars), $content);
    }
}
