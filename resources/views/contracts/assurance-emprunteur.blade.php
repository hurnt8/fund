<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
@page {
    margin: 18mm 22mm 20mm 22mm;
}
body {
    font-family: "DejaVu Serif", "Times New Roman", Times, Georgia, serif;
    font-size: 10.5pt;
    color: #000;
    line-height: 1.70;
    margin: 0;
    padding: 0;
}
.page { padding: 0; }

/* ── Filigrane ── */
.crx-wm {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    pointer-events: none; z-index: -1; opacity: 0.08;
    background-size: 50%; background-position: center; background-repeat: no-repeat;
}
.crx-wm-text {
    position: fixed; top: 42%; left: 0; width: 100%; text-align: center;
    font-size: 55pt; font-weight: bold; color: rgba(0,0,0,0.04);
    pointer-events: none; z-index: -1;
    font-family: "DejaVu Serif", serif; letter-spacing: 8px;
}

/* ── En-tête ── */
.header-wrap {
    border-bottom: 2pt solid #000;
    margin-bottom: 16pt;
    padding-bottom: 10pt;
}
.header-logo-row {
    display: table; width: 100%; margin-bottom: 6pt;
}
.header-logo-left {
    display: table-cell; width: 20%; vertical-align: middle; text-align: left;
}
.header-logo-center {
    display: table-cell; width: 60%; vertical-align: middle; text-align: center;
}
.header-logo-right {
    display: table-cell; width: 20%; vertical-align: middle; text-align: right;
}
.doc-type {
    font-size: 7.5pt; letter-spacing: 2px; color: #555; text-transform: uppercase;
    text-align: center; margin-bottom: 3pt;
}
.doc-title {
    font-size: 15pt; font-weight: bold; text-transform: uppercase;
    text-align: center; letter-spacing: 1.5px; line-height: 1.3;
}
.doc-subtitle {
    font-size: 9pt; text-align: center; color: #333; margin-top: 3pt;
}
.header-ref-block {
    text-align: center; font-size: 9pt; margin-top: 8pt; line-height: 1.8;
}
.ref-box {
    display: inline-block;
    border: 1pt solid #888;
    padding: 2pt 12pt;
    font-size: 8.5pt;
    margin: 0 4pt;
}
.ref-box strong { font-size: 10pt; }

/* ── Titres de section ── */
.sec-title {
    font-size: 9pt; font-weight: bold; text-transform: uppercase;
    letter-spacing: 1.2px; color: #fff;
    background: #222;
    padding: 3pt 8pt;
    margin: 14pt 0 7pt;
}

/* ── Tableaux d'identité / financement ── */
.info-table {
    width: 100%; border-collapse: collapse;
    font-size: 10pt; margin-bottom: 10pt;
}
.info-table td {
    padding: 3.5pt 7pt;
    border: 0.5pt solid #bbb;
    vertical-align: top;
}
.info-table td.lbl {
    background: #f2f2f2; font-weight: bold; width: 42%; font-size: 9.5pt;
}
.info-table td.val {
    font-weight: bold; color: #000;
}

/* ── Tableau des garanties ── */
.guar-table {
    width: 100%; border-collapse: collapse;
    font-size: 9.5pt; margin-bottom: 10pt;
}
.guar-table th {
    background: #444; color: #fff; font-weight: bold;
    padding: 4pt 7pt; text-align: center; font-size: 8.5pt;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.guar-table td {
    padding: 3.5pt 7pt; border: 0.5pt solid #bbb; vertical-align: middle;
}
.guar-table td.gname { font-weight: bold; }
.guar-table td.gcov  { text-align: center; }
.guar-table td.gcheck { text-align: center; font-size: 12pt; }
.guar-table tr:nth-child(even) td { background: #fafafa; }

/* ── Articles CG ── */
.article { margin-bottom: 11pt; }
.article-title {
    font-weight: bold; font-size: 10pt;
    text-decoration: underline; margin-bottom: 4pt;
    text-transform: uppercase;
}
.article-body {
    font-size: 9.5pt; color: #111; line-height: 1.72; white-space: pre-line;
}
.sub-art { font-weight: bold; margin-top: 4pt; margin-bottom: 2pt; font-size: 9.5pt; }

/* ── Encadré important ── */
.notice-box {
    border: 1pt solid #888; background: #f8f8f8;
    padding: 6pt 10pt; margin: 10pt 0;
    font-size: 9pt; line-height: 1.65;
}
.notice-box strong { font-size: 9.5pt; }

/* ── Bloc signature ── */
.sig-block {
    margin-top: 22pt; border-top: 1.5pt solid #000; padding-top: 10pt;
}
.sig-date { font-size: 10pt; margin-bottom: 16pt; }
.sig-row { display: table; width: 100%; }
.sig-cell {
    display: table-cell; width: 33.33%;
    text-align: center; padding: 0 5pt; vertical-align: bottom;
}
.sig-col-header {
    font-weight: bold; font-size: 8pt; text-transform: uppercase;
    letter-spacing: 0.5px; margin-bottom: 5pt; color: #000;
}
.sig-img-wrap { min-height: 48px; display: block; }
.sig-line {
    border-top: 0.5pt solid #555;
    margin-top: 6pt; padding-top: 4pt;
    font-size: 8.5pt; font-style: italic;
}

/* ── Pied de page ── */
.footer-note {
    font-size: 7.5pt; color: #555; border-top: 0.5pt solid #ccc;
    margin-top: 14pt; padding-top: 5pt; line-height: 1.55;
}
</style>
</head>
<body>

{{-- ── Filigrane ── --}}
@isset($images)
  @if(!empty($images['watermark_path']))
    <div class="crx-wm" style="background-image:url('{{ $images['watermark_path'] }}')"></div>
  @else
    <div class="crx-wm-text">CONFIDENTIEL</div>
  @endif
@else
  <div class="crx-wm-text">CONFIDENTIEL</div>
@endisset

<div class="page">

{{-- ═══════════════════════════════════════════
     EN-TÊTE
     ═══════════════════════════════════════════ --}}
<div class="header-wrap">

  <div class="header-logo-row">
    <div class="header-logo-left">
      @isset($images)
        @if(!empty($images['logo_left_path']))
          <img src="{{ $images['logo_left_path'] }}"
               style="max-height:58px;max-width:130px;object-fit:contain;">
        @endif
      @endisset
    </div>

    <div class="header-logo-center">
      <div class="doc-type">Document contractuel — Produit A340G</div>
      <div class="doc-title">Attestation d'Assurance Emprunteur</div>
      <div class="doc-subtitle">Conditions Générales réf. CG-A340G — Garanties Décès / PTIA / ITT / IPT</div>
    </div>

    <div class="header-logo-right">
      @isset($images)
        @if(!empty($images['logo_right_path']))
          <img src="{{ $images['logo_right_path'] }}"
               style="max-height:58px;max-width:130px;object-fit:contain;">
        @endif
      @endisset
    </div>
  </div>

  <div class="header-ref-block">
    <span class="ref-box">N° Dossier&nbsp;: <strong>{{ $vars['{reference}'] ?? '{reference}' }}</strong></span>
    <span class="ref-box">Date d'effet&nbsp;: <strong>{{ $vars['{date}'] ?? '{date}' }}</strong></span>
    <span class="ref-box">Réf. archivage&nbsp;: <strong>{{ $vars['{archive}'] ?? '{archive}' }}</strong></span>
  </div>

</div>


{{-- ═══════════════════════════════════════════
     SECTION 1 — IDENTITÉ DE L'ASSURÉ
     ═══════════════════════════════════════════ --}}
<div class="sec-title">1. Identité de l'assuré</div>

<table class="info-table">
  <tr>
    <td class="lbl">Nom et prénom</td>
    <td class="val">{{ $vars['{nom_client}'] ?? '{nom_client}' }}</td>
  </tr>
  <tr>
    <td class="lbl">Adresse postale</td>
    <td class="val">{{ $vars['{adresse_client}'] ?? '{adresse_client}' }}</td>
  </tr>
  <tr>
    <td class="lbl">Date de naissance</td>
    <td class="val">{{ $vars['{date_naissance}'] ?? '{date_naissance}' }}</td>
  </tr>
  <tr>
    <td class="lbl">Pièce d'identité</td>
    <td class="val">{{ $vars['{type_identite}'] ?? '{type_identite}' }} &nbsp;N°&nbsp; {{ $vars['{numero_identite}'] ?? '{numero_identite}' }}</td>
  </tr>
  <tr>
    <td class="lbl">Établissement prêteur</td>
    <td class="val">{{ $vars['{societe}'] ?? '{societe}' }}</td>
  </tr>
</table>


{{-- ═══════════════════════════════════════════
     SECTION 2 — CARACTÉRISTIQUES DU PRÊT GARANTI
     ═══════════════════════════════════════════ --}}
<div class="sec-title">2. Caractéristiques du prêt garanti</div>

<table class="info-table">
  <tr>
    <td class="lbl">Capital emprunté</td>
    <td class="val">{{ $vars['{montant}'] ?? '{montant}' }} {{ $vars['{devise}'] ?? '{devise}' }}</td>
  </tr>
  <tr>
    <td class="lbl">Durée du prêt</td>
    <td class="val">{{ $vars['{duree}'] ?? '{duree}' }} mois</td>
  </tr>
  <tr>
    <td class="lbl">Taux nominal annuel</td>
    <td class="val">{{ $vars['{taux}'] ?? '{taux}' }} %</td>
  </tr>
  <tr>
    <td class="lbl">Mensualité de remboursement</td>
    <td class="val">{{ $vars['{mensualite}'] ?? '{mensualite}' }} {{ $vars['{devise}'] ?? '{devise}' }}</td>
  </tr>
  <tr>
    <td class="lbl">Capital initial assuré (100 %)</td>
    <td class="val">{{ $vars['{montant}'] ?? '{montant}' }} {{ $vars['{devise}'] ?? '{devise}' }}</td>
  </tr>
  <tr>
    <td class="lbl">Date d'effet de l'assurance</td>
    <td class="val">{{ $vars['{date}'] ?? '{date}' }}</td>
  </tr>
  <tr>
    <td class="lbl">Durée de l'assurance</td>
    <td class="val">{{ $vars['{duree}'] ?? '{duree}' }} mois — même durée que le prêt</td>
  </tr>
  <tr>
    <td class="lbl">Frais de dossier</td>
    <td class="val">{{ $vars['{frais_admin}'] ?? '{frais_admin}' }} {{ $vars['{devise}'] ?? '{devise}' }}</td>
  </tr>
  @if(($vars['{frais_assurance}'] ?? '—') !== '—')
  <tr>
    <td class="lbl">Frais d'assurance emprunteur</td>
    <td class="val">{{ $vars['{frais_assurance}'] }} {{ $vars['{devise}'] ?? '{devise}' }}</td>
  </tr>
  @endif
  @if(($vars['{date_fin_assurance}'] ?? '—') !== '—')
  <tr>
    <td class="lbl">Date de fin d'assurance</td>
    <td class="val">{{ $vars['{date_fin_assurance}'] }}</td>
  </tr>
  @endif
  @if(!empty($vars['{compte_bancaire}']) && ($vars['{compte_bancaire}'] ?? '') !== '')
  <tr>
    <td class="lbl">Compte bancaire de prélèvement</td>
    <td class="val">{{ $vars['{compte_bancaire}'] }}</td>
  </tr>
  @endif
</table>


{{-- ═══════════════════════════════════════════
     SECTION 3 — GARANTIES SOUSCRITES
     ═══════════════════════════════════════════ --}}
<div class="sec-title">3. Garanties souscrites</div>

<table class="guar-table">
  <thead>
    <tr>
      <th style="width:52%;text-align:left;">Garantie</th>
      <th style="width:24%;">Taux de couverture</th>
      <th style="width:24%;">Souscrite</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="gname">Décès (DC)</td>
      <td class="gcov">100 % du capital restant dû</td>
      <td class="gcheck">&#10003;</td>
    </tr>
    <tr>
      <td class="gname">Perte Totale et Irréversible d'Autonomie (PTIA)</td>
      <td class="gcov">100 % du capital restant dû</td>
      <td class="gcheck">&#10003;</td>
    </tr>
    <tr>
      <td class="gname">Incapacité Temporaire Totale de travail (ITT)</td>
      <td class="gcov">Mensualité — 90 jours de franchise</td>
      <td class="gcheck">&#10003;</td>
    </tr>
    <tr>
      <td class="gname">Invalidité Permanente Totale (IPT ≥ 66 %)</td>
      <td class="gcov">100 % du capital restant dû</td>
      <td class="gcheck">&#10003;</td>
    </tr>
    <tr>
      <td class="gname">Invalidité Permanente Partielle (IPP ≥ 33 %)</td>
      <td class="gcov">Prorata taux d'invalidité</td>
      <td class="gcheck">&#10003;</td>
    </tr>
    <tr>
      <td class="gname">Perte d'emploi (PE) — sous conditions</td>
      <td class="gcov">Mensualité — 180 jours de franchise</td>
      <td class="gcheck" style="color:#888;">—</td>
    </tr>
  </tbody>
</table>

<div class="notice-box">
  <strong>Cotisation d'assurance :</strong> La cotisation est incluse dans la mensualité de
  <strong>{{ $vars['{mensualite}'] ?? '{mensualite}' }} {{ $vars['{devise}'] ?? '{devise}' }}</strong>.
  Le taux d'assurance applicable est conforme aux conditions générales CG-A340G en vigueur.
  Une fiche standardisée d'information (FSI) a été remise à l'assuré préalablement à la signature.
</div>


{{-- ═══════════════════════════════════════════
     SECTION 4 — CONDITIONS GÉNÉRALES (EXTRAITS)
     ═══════════════════════════════════════════ --}}
<div class="sec-title">4. Extraits des conditions générales — CG-A340G</div>

<div class="article">
  <div class="article-title">Article 1 — Objet de l'assurance</div>
  <div class="article-body">La présente assurance a pour objet de garantir le remboursement des échéances du prêt contracté par l'Assuré auprès de l'Établissement Prêteur, en cas de réalisation d'un sinistre couvert, pendant la durée du prêt.

L'assureur s'engage à prendre en charge les mensualités ou le capital restant dû, selon la garantie mise en jeu, dans les conditions et limites définies aux présentes Conditions Générales réf. CG-A340G.</div>
</div>

<div class="article">
  <div class="article-title">Article 2 — Garanties</div>
  <div class="article-body"><span class="sub-art">2.1 Garantie Décès (DC)</span>
En cas de décès de l'Assuré, quelle qu'en soit la cause, l'Assureur verse à l'Établissement Prêteur le capital restant dû à la date du décès, dans la limite du capital assuré.

<span class="sub-art">2.2 Garantie PTIA</span>
En cas de Perte Totale et Irréversible d'Autonomie reconnue médicalement, l'Assureur solde le capital restant dû, dans les mêmes conditions que la garantie DC.

<span class="sub-art">2.3 Garantie ITT</span>
En cas d'Incapacité Temporaire Totale de travail dûment constatée, après une franchise de 90 jours, l'Assureur prend en charge les mensualités du prêt pour la durée de l'incapacité, dans la limite de la durée du prêt.

<span class="sub-art">2.4 Garantie IPT / IPP</span>
En cas d'Invalidité Permanente constatée après consolidation, l'Assureur intervient selon le taux d'invalidité reconnu, à partir d'un taux minimal de 33 % (IPP) ou 66 % (IPT).</div>
</div>

<div class="article">
  <div class="article-title">Article 3 — Exclusions générales</div>
  <div class="article-body">Sont notamment exclus de toute garantie :
— Les sinistres résultant d'une guerre civile ou étrangère, d'émeutes ou d'actes de terrorisme ;
— Les sinistres consécutifs à l'usage de drogues ou stupéfiants non prescrits médicalement ;
— Les maladies ou affections préexistantes non déclarées dans le questionnaire médical ;
— Les sinistres résultant d'une tentative de suicide ou d'automutilation volontaire ;
— Les blessures ou maladies survenant lors d'activités sportives à risque non déclarées ;
— Les sinistres dont la cause est directement liée à une état d'ébriété avéré de l'Assuré.</div>
</div>

<div class="article">
  <div class="article-title">Article 4 — Déclaration de sinistre</div>
  <div class="article-body">Toute déclaration de sinistre doit être adressée par écrit à l'Assureur dans un délai de <strong>30 jours calendaires</strong> suivant la survenance du sinistre (ou dès que l'Assuré en a connaissance pour les garanties invalidité/incapacité).

Le dossier de déclaration doit comprendre : acte de décès ou certificat médical circonstancié, pièce d'identité, tableau d'amortissement du prêt, et tout justificatif demandé par l'Assureur.

L'adresse de déclaration est celle de l'Établissement Prêteur mentionné à la Section 1 du présent document.</div>
</div>

<div class="article">
  <div class="article-title">Article 5 — Obligations de l'assuré</div>
  <div class="article-body">L'Assuré s'engage à :
— Répondre sincèrement à toutes les questions posées dans le questionnaire médical préalable à la souscription ;
— Déclarer tout changement d'état de santé susceptible d'affecter le risque assuré ;
— Payer régulièrement les primes d'assurance aux échéances convenues ;
— Informer l'Assureur de tout sinistre dans les délais impartis ;
— Fournir tous les justificatifs nécessaires à l'instruction du dossier sinistre.</div>
</div>

<div class="article">
  <div class="article-title">Article 6 — Durée et résiliation</div>
  <div class="article-body">La présente assurance prend effet à la date mentionnée en Section 2 et expire à la date du remboursement intégral du prêt garanti, sans pouvoir dépasser <strong>{{ $vars['{duree}'] ?? '{duree}' }} mois</strong>.

En cas de remboursement anticipé total du prêt, l'assurance prend fin de plein droit à la date d'extinction du prêt. Une résiliation peut intervenir, dans les conditions prévues au Code des assurances, notamment en application de la loi Lemoine permettant la substitution d'assurance à tout moment.</div>
</div>

<div class="article">
  <div class="article-title">Article 7 — Loi applicable et juridiction</div>
  <div class="article-body">Le présent contrat d'assurance est soumis au droit français. Tout litige relatif à son interprétation ou à son exécution relève de la compétence des tribunaux français. L'Assuré dispose d'un droit d'accès et de rectification aux données personnelles le concernant conformément à la réglementation en vigueur (RGPD).</div>
</div>


{{-- ═══════════════════════════════════════════
     SECTION 5 — DÉCLARATION DE L'ASSURÉ
     ═══════════════════════════════════════════ --}}
<div class="sec-title">5. Déclaration et consentement de l'assuré</div>

<div class="notice-box">
Je soussigné(e), <strong>{{ $vars['{nom_client}'] ?? '{nom_client}' }}</strong>,
{ne_e} le <strong>{{ $vars['{date_naissance}'] ?? '{date_naissance}' }}</strong>,
demeurant au <strong>{{ $vars['{adresse_client}'] ?? '{adresse_client}' }}</strong>,
déclare avoir pris connaissance des Conditions Générales d'Assurance référence <strong>CG-A340G</strong>
remises préalablement à la signature du présent document, en avoir compris les termes et conditions,
notamment les exclusions et les modalités de mise en jeu des garanties.

Je confirme l'exactitude des informations déclarées et reconnais avoir répondu sincèrement et complètement
à l'ensemble des questions relatives à mon état de santé. Je consens expressément au traitement
de mes données personnelles et de santé dans le cadre strict de la gestion du présent contrat d'assurance.

<strong>Référence dossier prêt :</strong> {{ $vars['{reference}'] ?? '{reference}' }}
<strong>Établissement prêteur :</strong> {{ $vars['{societe}'] ?? '{societe}' }}
<strong>Agent suivi :</strong> {{ $vars['{agent_suivi}'] ?? '{agent_suivi}' }}
</div>


{{-- ═══════════════════════════════════════════
     SIGNATURES
     ═══════════════════════════════════════════ --}}
<div class="sig-block">

  <div class="sig-date">
    Fait à ________________________, le&nbsp;&nbsp;<strong>{{ $vars['{date}'] ?? '{date}' }}</strong>
  </div>

  <div class="sig-row">

    {{-- Établissement prêteur --}}
    <div class="sig-cell">
      <div class="sig-col-header">L'Établissement Prêteur<br>{{ $vars['{societe}'] ?? '{societe}' }}</div>
      <div class="sig-img-wrap">
        @isset($images)
          @if(!empty($images['signature_admin_path']))
            <img src="{{ $images['signature_admin_path'] }}"
                 style="max-height:48px;max-width:110px;object-fit:contain;display:block;margin:0 auto">
          @endif
        @endisset
      </div>
      <div class="sig-line">{{ $vars['{directeur}'] ?? '{directeur}' }}</div>
    </div>

    {{-- Agent suivi (cachet) --}}
    <div class="sig-cell">
      <div class="sig-col-header">Le Responsable Dossier</div>
      <div class="sig-img-wrap">
        @isset($images)
          @if(!empty($images['stamp_path']))
            <img src="{{ $images['stamp_path'] }}"
                 style="max-height:56px;max-width:120px;object-fit:contain;display:block;margin:0 auto">
          @endif
        @endisset
      </div>
      <div class="sig-line">{{ $vars['{agent_suivi}'] ?? '{agent_suivi}' }}</div>
    </div>

    {{-- Assuré --}}
    <div class="sig-cell">
      <div class="sig-col-header">L'Assuré(e)<br>Lu et approuvé</div>
      <div class="sig-img-wrap">
        @isset($images)
          @if(!empty($images['signature_agent_path']))
            <img src="{{ $images['signature_agent_path'] }}"
                 style="max-height:48px;max-width:110px;object-fit:contain;display:block;margin:0 auto">
          @endif
        @endisset
      </div>
      <div class="sig-line">{{ $vars['{nom_client}'] ?? '{nom_client}' }}</div>
    </div>

  </div>
</div>

{{-- ── Pied de page légal ── --}}
<div class="footer-note">
  <strong>Document confidentiel — Attestation d'Assurance Emprunteur — Produit CG-A340G</strong><br>
  Ce document constitue l'attestation personnalisée d'assurance emprunteur délivrée à
  {{ $vars['{nom_client}'] ?? '{nom_client}' }} (dossier {{ $vars['{reference}'] ?? '{reference}' }})
  par {{ $vars['{societe}'] ?? '{societe}' }}.
  Les Conditions Générales complètes de référence CG-A340G sont disponibles sur demande auprès
  de l'établissement prêteur. Ce document est établi en vertu des dispositions du Code des assurances
  et de la réglementation relative à l'assurance emprunteur (loi Lagarde, loi Hamon, loi Lemoine).
  Données traitées conformément au RGPD — DPO : {{ $vars['{societe}'] ?? '{societe}' }}.
</div>

</div>
</body>
</html>
