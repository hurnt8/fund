@component('mail::message')

{{--
  Ce template utilise un système de {BALISES} résolues automatiquement
  par UserInvitationMail selon la LANGUE (locale) et le GENRE (gender) du destinataire.

  BALISES DISPONIBLES DANS LE TEMPLATE :
  ──────────────────────────────────────────────────────────────────────────
  {CHER_E}            → Cher / Chère / Dear / Estimado / Estimada / Szanowny …
  {SALUTATION}        → Monsieur / Madame / Mr. / Ms. / Sr. / Sra. / Pan / Pani
  {PRENOM}            → Prénom du destinataire
  {NOM_COMPLET}       → Nom complet du destinataire
  {EMAIL}             → Adresse email du destinataire
  {LIEN_ACTIVATION}   → URL du lien d'activation (remplacé automatiquement)
  {NOM_ENTREPRISE}    → Credixa Invest
  {FORMULE_POLITESSE} → Cordialement / Best regards / Atentamente / Z poważaniem
  {EQUIPE}            → L'équipe Credixa Invest / The Credixa Invest Team …
  {NOTICE_PERSONNEL}  → Mention sur la confidentialité du lien (traduit)
  {NOTICE_IGNORE}     → Mention d'ignorance si pas à l'origine (traduit, genré)
  ──────────────────────────────────────────────────────────────────────────
--}}

# {{ $greeting }}

{{ $resolved['{INTRO_CORPS}'] }}

{{ $resolved['{CORPS_ACTION}'] }}

@component('mail::button', ['url' => $activationUrl, 'color' => 'primary'])
{{ $btnLabel }}
@endcomponent

---

> {{ $resolved['{NOTICE_PERSONNEL}'] }}
>
> {{ $resolved['{NOTICE_IGNORE}'] }}

{{ $resolved['{FORMULE_POLITESSE}'] }},

**{{ $resolved['{EQUIPE}'] }}**

@endcomponent
