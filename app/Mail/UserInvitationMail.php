<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    // ── Sujets de l'email selon [locale][genre] ──────────────────────────────
    private const SUBJECTS = [
        'fr' => ['M' => 'Activation de votre compte — Credixa Invest',
                 'F' => 'Activation de votre compte — Credixa Invest',
                 'N' => 'Activez votre compte — Credixa Invest'],
        'en' => ['M' => 'Activate your account — Credixa Invest',
                 'F' => 'Activate your account — Credixa Invest',
                 'N' => 'Activate your account — Credixa Invest'],
        'es' => ['M' => 'Activación de su cuenta — Credixa Invest',
                 'F' => 'Activación de su cuenta — Credixa Invest',
                 'N' => 'Active su cuenta — Credixa Invest'],
        'pl' => ['M' => 'Aktywacja Twojego konta — Credixa Invest',
                 'F' => 'Aktywacja Twojego konta — Credixa Invest',
                 'N' => 'Aktywuj swoje konto — Credixa Invest'],
    ];

    // ── Labels du bouton selon la locale ────────────────────────────────────
    private const BTN_LABELS = [
        'fr' => 'Activer mon compte',
        'en' => 'Activate my account',
        'es' => 'Activar mi cuenta',
        'pl' => 'Aktywuj moje konto',
    ];

    // ── Corps principal selon la locale ─────────────────────────────────────
    private const BODY = [
        'fr' => [
            'intro'  => 'Un conseiller **{NOM_ENTREPRISE}** vient de créer votre espace client personnel.',
            'action' => 'Pour accéder à votre espace et suivre vos dossiers de financement, **activez votre compte** en cliquant sur le bouton ci-dessous.',
        ],
        'en' => [
            'intro'  => 'A **{NOM_ENTREPRISE}** advisor has just created your personal client space.',
            'action' => 'To access your space and track your financing files, **activate your account** by clicking the button below.',
        ],
        'es' => [
            'intro'  => 'Un asesor de **{NOM_ENTREPRISE}** acaba de crear su espacio de cliente personal.',
            'action' => 'Para acceder a su espacio y hacer seguimiento de sus expedientes, **active su cuenta** haciendo clic en el botón a continuación.',
        ],
        'pl' => [
            'intro'  => 'Doradca **{NOM_ENTREPRISE}** właśnie utworzył Twój osobisty obszar klienta.',
            'action' => 'Aby uzyskać dostęp do swojego obszaru i śledzić swoje wnioski, **aktywuj konto** klikając poniższy przycisk.',
        ],
    ];

    // ── Valeurs des balises selon [locale][genre] ────────────────────────────
    private const TAGS = [
        '{CHER_E}' => [
            'fr' => ['M' => 'Cher',       'F' => 'Chère',     'N' => 'Bonjour'],
            'en' => ['M' => 'Dear',       'F' => 'Dear',      'N' => 'Hello'],
            'es' => ['M' => 'Estimado',   'F' => 'Estimada',  'N' => 'Hola'],
            'pl' => ['M' => 'Szanowny',   'F' => 'Szanowna',  'N' => 'Witaj'],
        ],
        '{SALUTATION}' => [
            'fr' => ['M' => 'Monsieur',   'F' => 'Madame',    'N' => ''],
            'en' => ['M' => 'Mr.',        'F' => 'Ms.',       'N' => ''],
            'es' => ['M' => 'Sr.',        'F' => 'Sra.',      'N' => ''],
            'pl' => ['M' => 'Panie',      'F' => 'Pani',      'N' => ''],
        ],
        '{FORMULE_POLITESSE}' => [
            'fr' => ['M' => 'Cordialement',    'F' => 'Cordialement',    'N' => 'Cordialement'],
            'en' => ['M' => 'Best regards',    'F' => 'Best regards',    'N' => 'Kind regards'],
            'es' => ['M' => 'Atentamente',     'F' => 'Atentamente',     'N' => 'Saludos'],
            'pl' => ['M' => 'Z poważaniem',    'F' => 'Z poważaniem',    'N' => 'Z pozdrowieniami'],
        ],
        '{EQUIPE}' => [
            'fr' => ['M' => "L'équipe Credixa Invest",    'F' => "L'équipe Credixa Invest",    'N' => "L'équipe Credixa Invest"],
            'en' => ['M' => 'The Credixa Invest Team',    'F' => 'The Credixa Invest Team',    'N' => 'The Credixa Invest Team'],
            'es' => ['M' => 'El equipo de Credixa Invest','F' => 'El equipo de Credixa Invest','N' => 'El equipo de Credixa Invest'],
            'pl' => ['M' => 'Zespół Credixa Invest',      'F' => 'Zespół Credixa Invest',      'N' => 'Zespół Credixa Invest'],
        ],
        '{NOTICE_PERSONNEL}' => [
            'fr' => ['M' => "Ce lien d'activation est **personnel et unique**. Il expire dès que vous avez défini votre mot de passe.",
                     'F' => "Ce lien d'activation est **personnel et unique**. Il expire dès que vous avez défini votre mot de passe.",
                     'N' => "Ce lien d'activation est **personnel et unique**. Il expire dès que vous avez défini votre mot de passe."],
            'en' => ['M' => 'This activation link is **personal and unique**. It expires as soon as you have set your password.',
                     'F' => 'This activation link is **personal and unique**. It expires as soon as you have set your password.',
                     'N' => 'This activation link is **personal and unique**. It expires as soon as you have set your password.'],
            'es' => ['M' => 'Este enlace de activación es **personal y único**. Expira en cuanto haya establecido su contraseña.',
                     'F' => 'Este enlace de activación es **personal y único**. Expira en cuanto haya establecido su contraseña.',
                     'N' => 'Este enlace de activación es **personal y único**. Expira en cuanto haya establecido su contraseña.'],
            'pl' => ['M' => 'Ten link aktywacyjny jest **osobisty i unikalny**. Wygasa natychmiast po ustawieniu hasła.',
                     'F' => 'Ten link aktywacyjny jest **osobisty i unikalny**. Wygasa natychmiast po ustawieniu hasła.',
                     'N' => 'Ten link aktywacyjny jest **osobisty i unikalny**. Wygasa natychmiast po ustawieniu hasła.'],
        ],
        '{NOTICE_IGNORE}' => [
            'fr' => ['M' => "Si vous n'êtes pas à l'origine de cette création de compte, vous pouvez ignorer cet email.",
                     'F' => "Si vous n'êtes pas à l'origine de cette création de compte, vous pouvez ignorer cet email.",
                     'N' => "Si vous n'êtes pas à l'origine de cette création de compte, vous pouvez ignorer cet email."],
            'en' => ['M' => "If you did not request this account creation, you can ignore this email.",
                     'F' => "If you did not request this account creation, you can ignore this email.",
                     'N' => "If you did not request this account creation, you can ignore this email."],
            'es' => ['M' => "Si usted no solicitó la creación de esta cuenta, puede ignorar este email.",
                     'F' => "Si usted no solicitó la creación de esta cuenta, puede ignorar este email.",
                     'N' => "Si usted no solicitó la creación de esta cuenta, puede ignorar este email."],
            'pl' => ['M' => "Jeśli nie prosiłeś o utworzenie tego konta, możesz zignorować ten email.",
                     'F' => "Jeśli nie prosiłaś o utworzenie tego konta, możesz zignorować ten email.",
                     'N' => "Jeśli nie prosiłeś/aś o utworzenie tego konta, możesz zignorować ten email."],
        ],
    ];

    public function __construct(
        public User   $user,
        public string $activationUrl,
    ) {}

    public function envelope(): Envelope
    {
        $locale  = $this->user->locale  ?? 'fr';
        $gender  = $this->user->gender  ?? 'N';
        $subject = self::SUBJECTS[$locale][$gender]
                ?? self::SUBJECTS['fr']['N'];

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $locale    = $this->user->locale ?? 'fr';
        $gender    = $this->user->gender ?? 'N';
        $firstName = explode(' ', $this->user->name)[0];

        $resolved = [];
        foreach (self::TAGS as $balise => $locales) {
            $resolved[$balise] = $locales[$locale][$gender]
                              ?? $locales['fr']['N'];
        }

        $resolved['{PRENOM}']          = $firstName;
        $resolved['{NOM_COMPLET}']     = $this->user->name;
        $resolved['{EMAIL}']           = $this->user->email;
        $resolved['{LIEN_ACTIVATION}'] = $this->activationUrl;
        $resolved['{NOM_ENTREPRISE}']  = 'Credixa Invest';

        $body = self::BODY[$locale] ?? self::BODY['fr'];
        $resolved['{INTRO_CORPS}']  = $this->applyReplacements($body['intro'],  $resolved);
        $resolved['{CORPS_ACTION}'] = $this->applyReplacements($body['action'], $resolved);

        $greeting = trim($resolved['{CHER_E}'] . ' ' . $resolved['{SALUTATION}'] . ' ' . $this->user->name);

        return new Content(
            view: 'emails.user-invitation',
            with: [
                'user'          => $this->user,
                'activationUrl' => $this->activationUrl,
                'btnLabel'      => self::BTN_LABELS[$locale] ?? self::BTN_LABELS['fr'],
                'greeting'      => $greeting,
                'resolved'      => $resolved,
                'locale'        => $locale,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function applyReplacements(string $text, array $resolved): string
    {
        return str_replace(array_keys($resolved), array_values($resolved), $text);
    }
}
