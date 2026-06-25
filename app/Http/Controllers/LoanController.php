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
            'amount'   => 'required|numeric|min:1',
            'darly'    => 'required|numeric|min:1',
            'subject'  => 'required|string',
            'objet'    => 'nullable|string|max:2000',
            'currency' => 'nullable|string|in:' . implode(',', config('credixa.currencies')),
        ]);
        $data['currency'] = $data['currency'] ?? config('credixa.default_currency');

        $locale = $request->input('locale', 'fr');
        if (!in_array($locale, ['fr', 'en', 'pl', 'es'])) {
            $locale = 'fr';
        }
        App::setLocale($locale);

        $data['complete_url'] = url($locale . '/loan/complete')
            . '?name='  . urlencode($data['name'])
            . '&email=' . urlencode($data['email']);

        // Email 1 : dossier complet → contact@credixa.eu
        Mail::to('contact@credixa.eu')->send(new LoanMail($data, $locale));

        // Email 2 : confirmation → demandeur
        Mail::to($data['email'])->send(new LoanConfirmationMail($data, $locale));

        return back()->with('success', __('message.success_loan'));
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
        ]);
    }

    public function sendDocuments(Request $request)
    {
        $locale = $request->input('locale', 'fr');
        if (!in_array($locale, ['fr', 'en', 'pl', 'es'], true)) {
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

        // Consommer le jeton AVANT tout envoi
        session()->forget('doc_submission_token');

        // ── Validation ───────────────────────────────────────────────────────
        $needsVerso = in_array($request->input('doc_type'), ['id_card', 'license', 'residence'], true);
        $fileRules  = ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email'],
            'address'        => ['required', 'string', 'max:1000'],
            'doc_type'       => ['required', 'string', 'in:id_card,passport,license,residence,other'],
            'id_photo_recto' => array_merge(['required'], $fileRules),
            'id_photo_verso' => array_merge($needsVerso ? ['required'] : ['nullable'], $fileRules),
        ]);

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
            Mail::to('contact@credixa.eu')->send(new LoanDocumentsMail($data, $attachments, $locale));
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
