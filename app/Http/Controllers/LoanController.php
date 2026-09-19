<?php

namespace App\Http\Controllers;

use App\Mail\LoanMail;
use App\Mail\LoanConfirmationMail;
use App\Mail\LoanDocumentsMail;
use App\Mail\LoanDocumentsConfirmationMail;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function simulate(Request $request)
    {
        $validated = $request->validate([
            'amount'        => 'required|numeric|min:1',
            'duration'      => 'required|integer|min:1',
            'interest_rate' => 'required|numeric|min:0',
        ]);

        $monthlyPayment = $this->loanService->calculateMonthlyPayment(
            (float) $validated['amount'],
            (int)   $validated['duration'],
            (float) $validated['interest_rate']
        );

        $amortizationSchedule = $this->loanService->generateAmortizationSchedule(
            (float) $validated['amount'],
            (int)   $validated['duration'],
            (float) $validated['interest_rate'],
            $monthlyPayment
        );

        return view('simulate', [
            'loan'                 => (object) $validated,
            'monthlyPayment'       => $monthlyPayment,
            'amortizationSchedule' => $amortizationSchedule,
        ]);
    }

    public function sendMail(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'phone'    => 'required|string|max:50',
            'amount'   => 'required|numeric|min:1000',
            'darly'    => 'required|numeric|min:1',
            'subject'  => 'required|string',
            'objet'    => 'nullable|string|max:2000',
            'currency' => 'nullable|string|in:' . implode(',', config('aurenza.currencies')),
        ], [
            'amount.min' => 'Le montant minimum pour une demande de prêt est de 1000.',
        ]);
        $data['currency'] = $data['currency'] ?? config('aurenza.default_currency');

        $locale = $request->input('locale', 'fr');
        if (!in_array($locale, ['fr', 'en', 'pl', 'es', 'ro', 'hr', 'pt'])) {
            $locale = 'fr';
        }
        App::setLocale($locale);

        $data['complete_url'] = url($locale . '/loan/complete')
            . '?name='  . urlencode($data['name'])
            . '&email=' . urlencode($data['email']);

        // Email 1 : dossier complet → contact@aurenzacapital.com
        Mail::to(config('mail.contact_address'))->send(new LoanMail($data, $locale));

        // Email 2 : confirmation → demandeur
        Mail::to($data['email'])->send(new LoanConfirmationMail($data, $locale));

        return back()->with('success', __('message.success_loan'));
    }

    /**
     * Taille totale de requête réellement acceptée par PHP, en kilo-octets.
     * On retient la plus contraignante entre post_max_size et upload_max_filesize.
     */
    private static function limitePhpKo(): int
    {
        $enOctets = static function (string $v): int {
            $v      = trim($v);
            $nombre = (int) $v;
            return match (strtolower(substr($v, -1))) {
                'g'     => $nombre * 1024 * 1024 * 1024,
                'm'     => $nombre * 1024 * 1024,
                'k'     => $nombre * 1024,
                default => $nombre,
            };
        };

        $post   = $enOctets((string) ini_get('post_max_size'));
        $upload = $enOctets((string) ini_get('upload_max_filesize'));

        $limites = array_filter([$post, $upload], fn ($o) => $o > 0);

        // 0 ou absent = illimité : on retombe sur la valeur métier de 10 Mo.
        return $limites ? (int) floor(min($limites) / 1024) : 10240;
    }

    public function showDocuments(Request $request)
    {
        // Nouveau jeton à chaque affichage du formulaire
        $token = Str::uuid()->toString();
        session(['doc_submission_token' => $token]);

        return view('loan-documents', [
            'prefillName'     => $request->query('name'),
            'prefillEmail'    => $request->query('email'),
            'submissionToken' => $token,
            // Affichée sous les champs, pour que la limite soit connue avant l'envoi.
            'maxFichierMo'    => round(min(5120, self::limitePhpKo() / 2) / 1024, 1),
        ]);
    }

    public function sendDocuments(Request $request)
    {
        $locale = $request->input('locale', 'fr');
        if (!in_array($locale, ['fr', 'en', 'pl', 'es', 'ro', 'hr', 'pt'], true)) {
            $locale = 'fr';
        }
        App::setLocale($locale);

        // ── Protection anti-doublon ──────────────────────────────────────────
        $submitted    = $request->input('submission_token', '');
        $sessionToken = session('doc_submission_token');

        if (!$submitted || !$sessionToken || !hash_equals($sessionToken, $submitted)) {
            return redirect()->route('loan.complete', ['locale' => $locale])
                ->with('docs_already_sent', true);
        }

        // ── Validation ───────────────────────────────────────────────────────
        // Elle passe AVANT la consommation du jeton : sinon un simple refus de
        // validation (fichier trop lourd, mauvais format) brûlait le jeton et la
        // correction suivante était rejetée comme un doublon.
        $needsVerso = in_array($request->input('doc_type'), ['id_card', 'license', 'residence'], true);

        // La limite par fichier ne peut pas dépasser ce que PHP accepte réellement :
        // au-delà, la requête est vidée avant d'arriver ici et l'utilisateur ne
        // comprend pas pourquoi son envoi échoue.
        $maxParFichier = (int) min(5120, floor(self::limitePhpKo() / ($needsVerso ? 2 : 1)) - 256);
        $maxParFichier = max($maxParFichier, 512);

        $fileRules = ['file', 'mimes:jpg,jpeg,png,pdf', 'max:' . $maxParFichier];

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email'],
            'address'        => ['required', 'string', 'max:1000'],
            'doc_type'       => ['required', 'string', 'in:id_card,passport,license,residence,other'],
            'id_photo_recto' => array_merge(['required'], $fileRules),
            'id_photo_verso' => array_merge($needsVerso ? ['required'] : ['nullable'], $fileRules),
        ]);

        // Le jeton n'est consommé qu'une fois le dossier valide, juste avant l'envoi.
        session()->forget('doc_submission_token');

        // ── Stockage temporaire des fichiers ─────────────────────────────────
        $tempFiles   = [];
        $attachments = [];

        $recto = $request->file('id_photo_recto');
        if ($recto instanceof \Illuminate\Http\UploadedFile) {
            $stored = $recto->store('temp-docs', 'local');
            if ($stored !== false) {
                $path          = storage_path('app/' . $stored);
                $attachments[] = ['path' => $path, 'name' => 'recto_' . $recto->getClientOriginalName(), 'mime' => $recto->getMimeType()];
                $tempFiles[]   = $path;
            }
        }

        $verso = $request->file('id_photo_verso');
        if ($verso instanceof \Illuminate\Http\UploadedFile) {
            $stored = $verso->store('temp-docs', 'local');
            if ($stored !== false) {
                $path          = storage_path('app/' . $stored);
                $attachments[] = ['path' => $path, 'name' => 'verso_' . $verso->getClientOriginalName(), 'mime' => $verso->getMimeType()];
                $tempFiles[]   = $path;
            }
        }

        // ── Envoi des emails ─────────────────────────────────────────────────
        try {
            Mail::to(config('mail.contact_address'))->send(new LoanDocumentsMail($data, $attachments, $locale));
            Mail::to($data['email'])->send(new LoanDocumentsConfirmationMail($data, $locale));
        } finally {
            foreach ($tempFiles as $p) {
                if (file_exists($p)) {
                    @unlink($p);
                }
            }
        }

        return redirect()->route('loan.complete', ['locale' => $locale])
            ->with('success', __('message.docs_success'));
    }
}
