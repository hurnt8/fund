<?php

namespace Database\Seeders;

use App\Models\ContractTemplate;
use Illuminate\Database\Seeder;

class ContractTemplateSeeder extends Seeder
{
    public function run(): void
    {
        ContractTemplate::updateOrCreate(
            ['name' => 'Contrat Standard AURENZA CAPITAL INVESTI'],
            [
                'is_default'    => true,
                'template_type' => 'html',
                'locale'        => null,
                'created_by'    => null,
                'content'       => $this->defaultContent(),
            ]
        );
    }

    /**
     * Contenu HTML du contrat standard — fidèle au document DOCX fourni.
     *
     * Balises disponibles :
     *   Données dossier  : {reference}, {archive}, {nom_client}, {adresse_client},
     *                      {date_naissance}, {numero_identite}, {type_identite},
     *                      {agent_suivi}, {montant}, {devise}, {duree}, {mensualite},
     *                      {taux}, {frais_admin}, {compte_bancaire}, {date}, {societe}
     *
     *   Traductions auto : {title}, {header}, {between}, {ref_label}, {archive_label},
     *                      {lender_label}, {lender_desc}, {borrower_label}, {borrower_desc},
     *                      {agent_label}, {agent_desc}, {finance_title}, {amount_label},
     *                      {duration_label}, {months}, {monthly_label}, {rate_label},
     *                      {fees_label}, {bank_label},
     *                      {art1_title}, {art1_body} … {art8_title}, {art8_body},
     *                      {made_at}, {sig_lender}, {sig_agent}, {sig_borrower}
     *
     * Le ContractService résout TOUTES ces balises avant de rendre le PDF.
     */
    private function defaultContent(): string
    {
        return <<<'HTML'
<div class="header">
  <div class="header-country">{header}</div>
  <div class="header-title">{title}</div>
  <div class="header-ref">
    {ref_label} : {reference}&nbsp;&nbsp;|&nbsp;&nbsp;{archive_label} : {archive}
  </div>
</div>

<div class="section-title">{between}</div>

<div class="parties-block">
  <span class="party-label">{lender_label}</span>
  <span class="company-name"> {societe}</span>,
  {lender_desc}
</div>

<div class="parties-block">
  <span class="party-label">{borrower_label}</span>,
  <strong>{nom_client}</strong>,
  {borrower_desc}
</div>

<div class="parties-block">
  <span class="party-label">{agent_label}</span>,
  <strong>{agent_suivi}</strong>,
  {agent_desc}
</div>

<div class="section-title">{finance_title}</div>
<table class="finance-table">
  <tr><td>{amount_label}</td><td>{montant} {devise}</td></tr>
  <tr><td>{duration_label}</td><td>{duree} {months}</td></tr>
  <tr><td>{monthly_label}</td><td>{mensualite} {devise}</td></tr>
  <tr><td>{rate_label}</td><td>{taux} %</td></tr>
  <tr><td>{fees_label}</td><td>{frais_admin} {devise}</td></tr>
</table>

<div class="article">
  <div class="article-title">{art1_title}</div>
  <div class="article-body">{art1_body}</div>
</div>

<div class="article">
  <div class="article-title">{art2_title}</div>
  <div class="article-body">{art2_body}</div>
</div>

<div class="article">
  <div class="article-title">{art3_title}</div>
  <div class="article-body">{art3_body}</div>
</div>

<div class="article">
  <div class="article-title">{art4_title}</div>
  <div class="article-body">{art4_body}</div>
</div>

<div class="article">
  <div class="article-title">{art5_title}</div>
  <div class="article-body">{art5_body}</div>
</div>

<div class="article">
  <div class="article-title">{art6_title}</div>
  <div class="article-body">{art6_body}</div>
</div>

<div class="article">
  <div class="article-title">{art7_title}</div>
  <div class="article-body">{art7_body}</div>
</div>

<div class="article">
  <div class="article-title">{art8_title}</div>
  <div class="article-body">{art8_body}</div>
</div>

<div class="signature-block">
  <div class="sig-date">{made_at} : {date}</div>
  <div class="sig-row">
    <div class="sig-cell"><div class="sig-label">{sig_lender}</div></div>
    <div class="sig-cell"><div class="sig-label">{sig_agent}</div></div>
    <div class="sig-cell"><div class="sig-label">{sig_borrower}</div></div>
  </div>
</div>
HTML;
    }
}
