<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LoanValidatedMail;
use App\Mail\SignedContractAcknowledgementMail;
use App\Models\ClientNotification;
use App\Models\ContractTemplate;
use App\Models\LoanHistory;
use App\Models\LoanRequest;
use App\Models\User;
use App\Services\ContractHtmlService;
use App\Services\DocumentArchive;
use App\Services\LoanPdfService;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoanRequestController extends Controller
{
    public function __construct(
        private LoanService     $loanService,
        private LoanPdfService  $pdfService,
    ) {}

    public function index(Request $request)
    {
        $admin        = Auth::user();
        $isSuperAdmin = $admin->hasRole('super-admin');

        // Super-admin voit tous les dossiers; admin régulier voit les siens
        $query = LoanRequest::with(['client', 'admin']);
        if (! $isSuperAdmin) {
            $query->where('admin_id', $admin->id);
        }

        // Filtre par admin (super-admin uniquement)
        if ($isSuperAdmin && $request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('reference', 'like', "%$search%");
            });
        }

        $loans = $query->latest()->paginate(15)->appends($request->query());

        $base = $isSuperAdmin ? LoanRequest::query() : LoanRequest::where('admin_id', $admin->id);
        $stats = [
            'total'           => (clone $base)->count(),
            'draft'           => (clone $base)->where('status', 'draft')->count(),
            'pending'         => (clone $base)->where('status', 'pending')->count(),
            'validated'       => (clone $base)->where('status', 'validated')->count(),
            'contract_sent'   => (clone $base)->where('status', 'contract_sent')->count(),
            'contract_signed' => (clone $base)->where('status', 'contract_signed')->count(),
            'finalized'       => (clone $base)->where('status', 'finalized')->count(),
            'rejected'        => (clone $base)->where('status', 'rejected')->count(),
        ];

        $admins = $isSuperAdmin
            ? User::where('type', 'staff')
                  ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super-admin']))
                  ->orderBy('name')->get()
            : collect();

        return view('admin.loans.index', compact('loans', 'stats', 'isSuperAdmin', 'admins'));
    }

    public function create()
    {
        $admin     = Auth::user();
        $myClients = $this->clientsForAdmin($admin);
        $templates  = $this->templatesForAdmin($admin);
        $currencies = config('credixa.currencies');

        return view('admin.loans.create', compact('myClients', 'templates', 'currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Client
            'client_mode'       => 'required|in:existing,new',
            'client_id'         => 'required_if:client_mode,existing|nullable|exists:users,id',
            'client_name'       => 'required_if:client_mode,new|nullable|string|max:255',
            'client_email'      => 'required_if:client_mode,new|nullable|email|unique:users,email',
            'client_phone'      => 'nullable|string|max:50',
            'client_address'    => 'nullable|string|max:500',
            'client_birth_date' => 'nullable|date',
            'client_id_type'    => 'nullable|string|max:30',
            'client_id_number'  => 'nullable|string|max:60',
            'client_locale'     => 'nullable|in:fr,en,pl,es,pt',
            'client_currency'   => 'nullable|string|max:10',
            // Prêt
            'amount'            => 'required|numeric|min:1000',
            'darly'             => 'required|integer|min:1|max:360',
            'objet'             => 'nullable|string|max:255',
            'subject'           => 'nullable|string|max:2000',
            'start_date'        => 'nullable|date',
            'currency'          => 'required|string|max:10',
            'admin_fees'        => 'nullable|numeric|min:0',
            'bank_account'      => 'nullable|string|max:255',
            'agent_suivi'       => 'nullable|string|max:255',
            'directeur'         => 'nullable|string|max:255',
            'special_conditions'=> 'nullable|string',
            'contract_template_id' => 'nullable|exists:contract_templates,id',
            // Balises personnalisées du template (modale)
            'extra_fields'      => 'nullable|array',
            'extra_fields.*'    => 'nullable|string|max:500',
            // Fichiers joints au dossier
            'files'             => 'nullable|array|max:10',
            'files.*'           => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:20480',
        ], [
            'client_email.unique' => 'Cet email est déjà utilisé dans le système. Passez en mode "Client existant" pour sélectionner ce client.',
            'client_name.required_if'  => 'Le nom du client est obligatoire pour un nouveau client.',
            'client_email.required_if' => 'L\'email du client est obligatoire pour un nouveau client.',
            'client_id.required_if'    => 'Veuillez sélectionner un client existant.',
            'amount.required'   => 'Le montant est obligatoire.',
            'amount.min'        => 'Le montant minimum pour une demande de prêt est de 1000.',
            'darly.required'    => 'La durée est obligatoire.',
        ]);

        $admin = Auth::user();

        // Vérifier l'autorisation client existant avant la transaction
        if ($data['client_mode'] === 'existing') {
            $client = User::findOrFail($data['client_id']);
            if (!$admin->hasRole('super-admin')) {
                abort_unless($client->created_by === $admin->id, 403, 'Client non autorisé.');
            }
        }

        $calc = $this->loanService->calculateAll(
            (float) $data['amount'],
            5.00,
            (int) $data['darly']
        );

        $loan = DB::transaction(
            function () use ($data, $admin, $calc) {
                if ($data['client_mode'] === 'new') {
                    $client = User::create([
                        'name'             => $data['client_name'],
                        'email'            => $data['client_email'],
                        'password'         => Hash::make(Str::random(32)),
                        'type'             => 'client',
                        'created_by'       => $admin->id,
                        'invitation_token' => Str::random(64),
                        'phone'            => $data['client_phone'] ?? null,
                        'address'          => $data['client_address'] ?? null,
                        'birth_date'       => $data['client_birth_date'] ?? null,
                        'id_type'          => $data['client_id_type'] ?? null,
                        'id_number'        => $data['client_id_number'] ?? null,
                        'locale'           => $data['client_locale'] ?? 'fr',
                        'currency'         => $data['client_currency'] ?? $data['currency'],
                    ]);
                    $client->assignRole('client');
                } else {
                    $client = User::findOrFail($data['client_id']);
                }

                $locale = $client->locale ?? 'fr';

                $loan = LoanRequest::create([
                    'reference'            => LoanRequest::generateReference(),
                    'archive_ref'          => 'CR-ARCH-' . strtoupper(Str::random(8)),
                    'admin_id'             => $admin->id,
                    'client_id'            => $client->id,
                    'contract_template_id' => $data['contract_template_id'] ?? null,
                    'name'                 => $client->name,
                    'email'                => $client->email,
                    'phone'                => $client->phone ?? $data['client_phone'] ?? null,
                    'address'              => $client->address ?? $data['client_address'] ?? null,
                    'amount'               => $data['amount'],
                    'interest_rate'        => 5.00,
                    'currency'             => $client->currency ?? $data['currency'],
                    'start_date'           => $data['start_date'] ?? now()->toDateString(),
                    'darly'                => $data['darly'],
                    'objet'                => $data['objet'] ?? null,
                    'subject'              => $data['subject'] ?? null,
                    'special_conditions'   => $data['special_conditions'] ?? null,
                    'admin_fees'           => $data['admin_fees'] ?? null,
                    'bank_account'         => $data['bank_account'] ?? null,
                    'agent_suivi'          => $data['agent_suivi'] ?? null,
                    'directeur'            => $data['directeur'] ?? null,
                    'monthly_payment'      => $calc['monthly_payment'],
                    'total_cost'           => $calc['total_cost'],
                    'total_with_interest'  => $calc['total_with_interest'],
                    'amortization_schedule'=> $calc['amortization_schedule'],
                    'contract_language'    => $locale,
                    'status'               => LoanRequest::STATUS_DRAFT,
                    'extra_fields'         => !empty($data['extra_fields']) ? $data['extra_fields'] : null,
                ]);

                $this->logHistory($loan, 'created', null, ['status' => $loan->status]);

                return $loan;
            }
        );

        return redirect()->route('admin.loans.show', $loan)
                         ->with('success', 'Dossier N°' . $loan->reference . ' créé (statut : Brouillon).');
    }

    public function show(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        $loan->load(['client', 'admin', 'history.admin', 'contractTemplate']);
        $generatedDocs = $loan->generatedDocuments()->with('generatedBy')->get();
        $isSuperAdmin  = Auth::user()->hasRole('super-admin');
        $admins        = $isSuperAdmin
            ? \App\Models\User::where('type', 'staff')
                ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'super-admin']))
                ->orderBy('name')->get()
            : collect();
        return view('admin.loans.show', compact('loan', 'generatedDocs', 'isSuperAdmin', 'admins'));
    }

    public function edit(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        abort_unless($loan->isEditable(), 403, 'Cette demande ne peut plus être modifiée.');

        $admin     = Auth::user();
        $myClients = User::where('type', 'client')
                         ->whereHas('clientLoans', fn($q) => $q->where('admin_id', $admin->id))
                         ->orderBy('name')->get();
        $currencies = config('credixa.currencies');
        $templates  = $this->templatesForAdmin($admin);

        return view('admin.loans.edit', compact('loan', 'myClients', 'currencies', 'templates'));
    }

    public function update(Request $request, LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        abort_unless($loan->isEditable(), 403);

        $data = $request->validate([
            'amount'               => 'required|numeric|min:1000',
            'darly'                => 'required|integer|min:1|max:360',
            'objet'                => 'nullable|string|max:255',
            'subject'              => 'nullable|string|max:2000',
            'start_date'           => 'nullable|date',
            'currency'             => 'required|string|max:10',
            'admin_fees'            => 'nullable|numeric|min:0',
            'frais_assurance'       => 'nullable|numeric|min:0',
            'date_fin_assurance'    => 'nullable|date',
            'bank_account'          => 'nullable|string|max:255',
            'agent_suivi'           => 'nullable|string|max:255',
            'directeur'             => 'nullable|string|max:255',
            'special_conditions'    => 'nullable|string',
            'contract_template_id'  => 'nullable|exists:contract_templates,id',
            'insurance_template_id' => 'nullable|exists:contract_templates,id',
            'contract_language'     => 'nullable|in:fr,en,pl,es,pt',
            'extra_fields'         => 'nullable|array',
            'extra_fields.*'       => 'nullable|string|max:500',
        ], [
            'amount.min' => 'Le montant minimum pour une demande de prêt est de 1000.',
        ]);

        $old = $loan->only(['amount','darly','status','contract_template_id']);

        $calc = $this->loanService->calculateAll(
            (float) $data['amount'], 5.00, (int) $data['darly']
        );

        $loan->update(array_merge($data, [
            'interest_rate'        => 5.00,
            'monthly_payment'      => $calc['monthly_payment'],
            'total_cost'           => $calc['total_cost'],
            'total_with_interest'  => $calc['total_with_interest'],
            'amortization_schedule'=> $calc['amortization_schedule'],
            'contract_language'    => $data['contract_language'] ?? $loan->contract_language ?? 'fr',
            'extra_fields'         => !empty($data['extra_fields']) ? $data['extra_fields'] : null,
        ]));

        $this->logHistory($loan, 'updated', $old, $loan->only(['amount','darly','status','contract_template_id']));

        return redirect()->route('admin.loans.show', $loan)
                         ->with('success', 'Demande mise à jour.');
    }

    public function contractViewer(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        if (!$loan->contract_pdf_path) {
            return back()->with('error', 'Aucun PDF de contrat disponible pour ce dossier.');
        }
        return view('admin.loans.contract-viewer', compact('loan'));
    }

    public function insuranceViewer(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        if (!$loan->insurance_pdf_path) {
            return back()->with('error', 'Aucune attestation d\'assurance disponible pour ce dossier.');
        }
        return view('admin.loans.insurance-viewer', compact('loan'));
    }

    public function contract(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        $templates   = $this->templatesForAdmin(Auth::user());
        $previewHtml = $loan->contract_content ?? '';
        return view('admin.loans.contract', compact('loan', 'templates', 'previewHtml'));
    }

    public function previewPdf(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->contract_pdf_path) {
            return back()->with('error', 'Aucun PDF uploadé pour ce dossier.');
        }

        $absPath = storage_path('app/private/' . $loan->contract_pdf_path);
        if (!file_exists($absPath)) {
            return back()->with('error', 'Fichier PDF introuvable sur le serveur.');
        }

        return response()->file($absPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Contrat_' . $loan->reference . '.pdf"',
        ]);
    }

    public function uploadContractPdf(Request $request, LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        $request->validate([
            'contract_pdf' => 'required|file|mimes:pdf|max:20480',
        ], [
            'contract_pdf.required' => 'Veuillez sélectionner un fichier PDF.',
            'contract_pdf.mimes'    => 'Seuls les fichiers PDF sont acceptés.',
            'contract_pdf.max'      => 'Le fichier PDF ne doit pas dépasser 20 Mo.',
        ]);

        // Supprimer l'ancien PDF s'il existe
        if ($loan->contract_pdf_path) {
            $old = storage_path('app/private/' . $loan->contract_pdf_path);
            if (file_exists($old)) {
                @unlink($old);
            }
        }

        $file    = $request->file('contract_pdf');
        $relPath = 'contract-pdfs/' . $loan->id . '/' . $loan->reference . '.pdf';
        $absDir  = storage_path('app/private/contract-pdfs/' . $loan->id);

        if (!is_dir($absDir)) {
            mkdir($absDir, 0755, true);
        }

        $file->move($absDir, $loan->reference . '.pdf');

        $loan->update(['contract_pdf_path' => $relPath]);

        $this->logHistory($loan, 'pdf_uploaded', [], ['pdf' => $relPath]);

        return back()->with('success', 'PDF du contrat uploadé avec succès. Il sera joint à l\'email envoyé au client.');
    }

    public function previewInsurancePdf(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->insurance_pdf_path) {
            return back()->with('error', 'Aucune attestation d\'assurance uploadée pour ce dossier.');
        }

        $absPath = storage_path('app/private/' . $loan->insurance_pdf_path);
        if (!file_exists($absPath)) {
            return back()->with('error', 'Fichier d\'attestation introuvable sur le serveur.');
        }

        return response()->file($absPath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Assurance_' . $loan->reference . '.pdf"',
        ]);
    }

    public function uploadInsurancePdf(Request $request, LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        $request->validate([
            'insurance_pdf' => 'required|file|mimes:pdf|max:20480',
        ], [
            'insurance_pdf.required' => 'Veuillez sélectionner un fichier PDF.',
            'insurance_pdf.mimes'    => 'Seuls les fichiers PDF sont acceptés.',
            'insurance_pdf.max'      => 'Le fichier PDF ne doit pas dépasser 20 Mo.',
        ]);

        if ($loan->insurance_pdf_path) {
            $old = storage_path('app/private/' . $loan->insurance_pdf_path);
            if (file_exists($old)) {
                @unlink($old);
            }
        }

        $file    = $request->file('insurance_pdf');
        $relPath = 'insurance-pdfs/' . $loan->id . '/' . $loan->reference . '_assurance.pdf';
        $absDir  = storage_path('app/private/insurance-pdfs/' . $loan->id);

        if (!is_dir($absDir)) {
            mkdir($absDir, 0755, true);
        }

        $file->move($absDir, $loan->reference . '_assurance.pdf');

        $loan->update(['insurance_pdf_path' => $relPath]);

        $this->logHistory($loan, 'insurance_pdf_uploaded', [], ['pdf' => $relPath]);

        return back()->with('success', 'Attestation d\'assurance uploadée avec succès.');
    }

    public function sendInsuranceMail(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->insurance_pdf_path) {
            return back()->with('error', 'Générez d\'abord l\'attestation d\'assurance.');
        }

        $absPath = storage_path('app/private/' . $loan->insurance_pdf_path);
        if (!file_exists($absPath)) {
            return back()->with('error', 'Fichier d\'attestation introuvable sur le serveur.');
        }

        $locale = $loan->contract_language ?? 'fr';

        Mail::to($loan->email)->send(new \App\Mail\InsuranceAttestationMail($loan, $absPath, $locale));

        $this->logHistory($loan, 'insurance_sent', [], ['email' => $loan->email]);

        return back()->with('success', 'Attestation d\'assurance envoyée à ' . $loan->email . '.');
    }

    public function selectInsuranceTemplate(Request $request, LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        $request->validate(['insurance_template_id' => 'nullable|exists:contract_templates,id']);
        $loan->update(['insurance_template_id' => $request->insurance_template_id ?: null]);
        return back()->with('success', 'Modèle d\'assurance sélectionné.');
    }

    public function generateInsurancePdf(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        // Si le template a un DOCX, orienter vers le téléchargement DOCX
        if ($loan->insuranceTemplate?->hasDocxTemplate()) {
            return back()->with('error', 'Ce modèle utilise un fichier DOCX. Cliquez sur "Télécharger DOCX" pour l\'obtenir, convertissez-le en PDF, puis uploadez-le.');
        }

        $vars                = app(\App\Services\ContractService::class)->getVariables($loan);
        $vars['{directeur}'] = $loan->directeur ?: 'CREDIXA INVESTI';

        // Si le template a du contenu HTML personnalisé, l'utiliser
        $template = $loan->insuranceTemplate;
        if ($template && $template->content) {
            $html = app(ContractHtmlService::class)->buildContractHtml($loan, $template, $loan->contract_language ?? 'fr');
        } else {
            $html = view('contracts.assurance-emprunteur', ['vars' => $vars])->render();
            $html = str_replace(array_keys($vars), array_values($vars), $html);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'isPhpEnabled'         => false,
                'defaultFont'          => 'DejaVu Sans',
                'dpi'                  => 150,
            ]);

        $relDir  = 'insurance-pdfs/' . $loan->id;
        $relPath = $relDir . '/' . $loan->reference . '_assurance.pdf';
        $absDir  = storage_path('app/private/' . $relDir);

        if (!is_dir($absDir)) {
            mkdir($absDir, 0755, true);
        }

        file_put_contents(storage_path('app/private/' . $relPath), $pdf->output());

        $loan->update(['insurance_pdf_path' => $relPath]);
        $this->logHistory($loan, 'insurance_pdf_generated', [], ['pdf' => $relPath]);

        return back()->with('success', 'Attestation d\'assurance générée avec succès.');
    }

    public function downloadInsuranceDocx(LoanRequest $loan, DocumentArchive $archive)
    {
        $this->authorizeAccess($loan);

        $template = $loan->insuranceTemplate;

        if (!$template || !$template->hasDocxTemplate()) {
            return back()->with('error', 'Aucun template DOCX assurance disponible. Sélectionnez un modèle avec DOCX dans la fiche du dossier.');
        }

        $locale = $loan->contract_language ?? 'fr';

        try {
            $path = $archive->getOrGenerate($loan, $template, $locale);
        } catch (\Throwable $e) {
            return back()->with('error', 'Génération DOCX assurance impossible : ' . $e->getMessage());
        }

        return response()->download($path, 'Assurance_' . $loan->reference . '.docx');
    }

    public function downloadDocx(LoanRequest $loan, DocumentArchive $archive)
    {
        $this->authorizeAccess($loan);

        $template = $loan->contractTemplate
            ?? ContractTemplate::where('is_default', true)->first();

        if (!$template || !$template->hasDocxTemplate()) {
            return back()->with('error', 'Aucun template DOCX disponible pour ce dossier. Uploadez un template DOCX depuis la gestion des modèles.');
        }

        $locale = $loan->contract_language ?? 'fr';

        try {
            $path = $archive->getOrGenerate($loan, $template, $locale);
        } catch (\Throwable $e) {
            return back()->with('error', 'Génération DOCX impossible : ' . $e->getMessage());
        }

        return response()->download($path, 'Contrat_' . $loan->reference . '.docx');
    }

    public function updateContract(Request $request, LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        $data = $request->validate(['contract_content' => 'required|string']);

        $old = ['contract_content' => substr($loan->contract_content ?? '', 0, 100)];
        $loan->update(['contract_content' => $data['contract_content']]);
        $this->logHistory($loan, 'contract_edited', $old, ['contract_content' => substr($data['contract_content'], 0, 100)]);

        return back()->with('success', 'Contrat enregistré.');
    }

    public function validateLoan(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        abort_unless($loan->canBeValidated(), 403, 'Cette demande ne peut pas être validée.');

        // ── Bloquer si le contrat PDF n'est pas encore uploadé ────────────
        $contractPdfAbs = $loan->contract_pdf_path
            ? storage_path('app/private/' . $loan->contract_pdf_path)
            : null;

        if (!$contractPdfAbs || !file_exists($contractPdfAbs)) {
            return back()->with(
                'error',
                'Impossible de valider : aucun contrat PDF n\'a été uploadé pour ce dossier. '
                . 'Uploadez le contrat signé (section "PDF du contrat") avant de valider.'
            );
        }

        $old = ['status' => $loan->status];

        // ── Verrou + bascule atomique du statut ───────────────────────────
        // Empêche un double traitement (double-clic, requêtes concurrentes) :
        // un seul thread peut faire passer le dossier en "validated".
        // Les autres tombent sur canBeValidated() === false et s'arrêtent net,
        // avant tout envoi d'email.
        $claimed = DB::transaction(function () use ($loan) {
            $fresh = LoanRequest::lockForUpdate()->find($loan->id);
            if (!$fresh->canBeValidated()) {
                return false;
            }
            $fresh->update(['status' => LoanRequest::STATUS_VALIDATED, 'validated_at' => now()]);
            return true;
        });

        if (!$claimed) {
            return redirect()->route('admin.loans.show', $loan)
                ->with('error', 'Ce dossier a déjà été validé (ou est en cours de validation).');
        }

        $loan->refresh();

        $locale    = $loan->contract_language ?? 'fr';
        $recipient = $this->recipientEmail($loan);

        // ── Générer le tableau d'amortissement en PDF ─────────────────────
        set_time_limit(180);
        $amortPdfPath = null;
        try {
            $amortPdfPath = $this->pdfService->generateAmortizationPdf($loan, $locale);
        } catch (\Throwable $e) {
            Log::warning('Amortization PDF generation failed for ' . $loan->reference . ': ' . $e->getMessage());
        }

        // ── Envoyer l'email dans la langue du client ──────────────────────
        try {
            Mail::to($recipient)->send(
                new LoanValidatedMail($loan, $contractPdfAbs, $locale, $amortPdfPath ?? '')
            );
        } catch (\Throwable $e) {
            Log::error('LoanValidatedMail failed for ' . $loan->reference . ': ' . $e->getMessage());
        } finally {
            if ($amortPdfPath && file_exists($amortPdfPath)) {
                @unlink($amortPdfPath);
            }
        }

        // ── Passer au statut contract_sent ────────────────────────────────
        $loan->update([
            'status'  => LoanRequest::STATUS_CONTRACT_SENT,
            'sent_at' => now(),
        ]);

        $this->logHistory($loan, 'validated_and_sent', $old, ['status' => $loan->status, 'locale' => $locale, 'recipient' => $recipient]);

        // Notification in-app au client
        if ($loan->client_id) {
            ClientNotification::notifyUser(
                $loan->client,
                'loan_update',
                'app.notif_loan_contract',
                'app.notif_loan_contract_body',
                ['reference' => $loan->reference],
                ['loan_id' => $loan->id, 'reference' => $loan->reference]
            );
        }

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', 'Contrat validé — email envoyé à ' . $recipient
                . ' en ' . strtoupper($locale)
                . ($amortPdfPath ? ' avec tableau d\'amortissement.' : ' (tableau d\'amortissement non généré).')
            );
    }

    public function resendContractEmail(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        if (!$loan->contract_pdf_path) {
            return back()->with('error', 'Aucun PDF uploadé pour ce dossier. Uploadez le PDF avant de renvoyer l\'email.');
        }

        $pdfAbs = storage_path('app/private/' . $loan->contract_pdf_path);

        if (!file_exists($pdfAbs)) {
            return back()->with('error', 'Fichier PDF introuvable sur le serveur.');
        }

        $locale    = $loan->contract_language ?? 'fr';
        $recipient = $this->recipientEmail($loan);

        try {
            Mail::to($recipient)->send(new LoanValidatedMail($loan, $pdfAbs, $locale));
        } catch (\Throwable $e) {
            Log::error('resendContractEmail failed for ' . $loan->reference . ': ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }

        $this->logHistory($loan, 'contract_edited', [], ['action' => 'email_with_pdf_resent', 'recipient' => $recipient]);

        return back()->with('success', 'Email avec le PDF du contrat renvoyé à ' . $recipient . '.');
    }

    public function markSigned(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        $old = ['status' => $loan->status];

        // ── Verrou + bascule atomique du statut ───────────────────────────
        // Empêche l'envoi en double de l'email d'accusé de réception en cas
        // de double-clic ou de requêtes concurrentes sur ce bouton.
        $claimed = DB::transaction(function () use ($loan) {
            $fresh = LoanRequest::lockForUpdate()->find($loan->id);
            if ($fresh->status !== LoanRequest::STATUS_CONTRACT_SENT) {
                return false;
            }
            $fresh->update([
                'status'             => LoanRequest::STATUS_CONTRACT_SIGNED,
                'signed_received_at' => now(),
            ]);
            return true;
        });

        if (!$claimed) {
            return back()->with('error', 'Ce contrat a déjà été marqué comme signé.');
        }

        $loan->refresh();

        $locale    = $loan->contract_language ?? 'fr';
        $recipient = $this->recipientEmail($loan);
        $mailSent  = true;
        try {
            Mail::to($recipient)->send(new SignedContractAcknowledgementMail($loan, $locale));
        } catch (\Throwable $e) {
            Log::error('SignedContractAcknowledgementMail failed for ' . $loan->reference . ': ' . $e->getMessage());
            $mailSent = false;
        }

        if ($loan->client_id) {
            ClientNotification::notifyUser(
                $loan->client,
                'loan_update',
                'app.notif_loan_signed',
                'app.notif_loan_signed_body',
                ['reference' => $loan->reference],
                ['loan_id' => $loan->id, 'reference' => $loan->reference]
            );
        }

        $this->logHistory($loan, 'signed_received', $old, ['status' => $loan->status]);

        $msg = 'Contrat signé marqué comme reçu.';
        $msg .= $mailSent ? ' Email envoyé au client.' : ' (Email non envoyé — vérifiez la configuration mail.)';

        return back()->with('success', $msg);
    }

    public function updateStatus(Request $request, LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', LoanRequest::STATUSES),
        ]);

        $old = ['status' => $loan->status];

        // ── Passage en "validé" : flux complet (contrat + tableau + email) ─
        if ($data['status'] === LoanRequest::STATUS_VALIDATED && $old['status'] !== LoanRequest::STATUS_VALIDATED) {

            $contractPdfAbs = $loan->contract_pdf_path
                ? storage_path('app/private/' . $loan->contract_pdf_path)
                : null;

            if (!$contractPdfAbs || !file_exists($contractPdfAbs)) {
                return back()->with(
                    'error',
                    'Impossible de valider : aucun contrat PDF n\'a été uploadé pour ce dossier. '
                    . 'Uploadez le contrat signé (section "PDF du contrat") avant de valider.'
                );
            }

            $locale    = $loan->contract_language ?? 'fr';
            $recipient = $this->recipientEmail($loan);

            set_time_limit(180);
            $amortPdfPath = null;
            try {
                $amortPdfPath = $this->pdfService->generateAmortizationPdf($loan, $locale);
            } catch (\Throwable $e) {
                Log::warning('Amortization PDF generation failed for ' . $loan->reference . ': ' . $e->getMessage());
            }

            try {
                Mail::to($recipient)->send(
                    new LoanValidatedMail($loan, $contractPdfAbs, $locale, $amortPdfPath ?? '')
                );
            } catch (\Throwable $e) {
                Log::error('LoanValidatedMail failed for ' . $loan->reference . ': ' . $e->getMessage());
            } finally {
                if ($amortPdfPath && file_exists($amortPdfPath)) {
                    @unlink($amortPdfPath);
                }
            }

            $loan->update([
                'status'       => LoanRequest::STATUS_CONTRACT_SENT,
                'validated_at' => now(),
                'sent_at'      => now(),
            ]);

            if ($loan->client_id) {
                ClientNotification::notifyUser(
                    $loan->client,
                    'loan_update',
                    'app.notif_loan_contract',
                    'app.notif_loan_contract_body',
                    ['reference' => $loan->reference],
                    ['loan_id' => $loan->id, 'reference' => $loan->reference]
                );
            }

            $this->logHistory($loan, 'validated_and_sent', $old, ['status' => $loan->status, 'locale' => $locale, 'recipient' => $recipient]);

            return redirect()->route('admin.loans.show', $loan)
                ->with('success', 'Contrat validé — email envoyé à ' . $recipient
                    . ' en ' . strtoupper($locale)
                    . ($amortPdfPath ? ' avec tableau d\'amortissement.' : '.'));
        }

        // ── Autres changements de statut ──────────────────────────────────
        $isFinalization = $data['status'] === LoanRequest::STATUS_FINALIZED
            && $old['status'] !== LoanRequest::STATUS_FINALIZED;

        DB::transaction(function () use ($loan, $data, $old, $isFinalization) {
            // Verrou pessimiste pour éviter double-crédit en cas de double-clic
            $fresh = LoanRequest::lockForUpdate()->find($loan->id);
            if ($isFinalization && $fresh->status === LoanRequest::STATUS_FINALIZED) {
                return; // déjà finalisé, on ignore
            }

            $fresh->update(['status' => $data['status']]);

            if ($isFinalization && $fresh->client_id) {
                $fresh->client?->increment('balance', (float) $fresh->amount);

                $cur = $fresh->currency ?? config('credixa.default_currency');
                ClientNotification::notifyUser(
                    $fresh->client,
                    'credit',
                    'app.notif_loan_funded',
                    'app.notif_loan_funded_body',
                    ['amount' => number_format((float) $fresh->amount, 2, ',', ' '), 'currency' => $cur],
                    ['loan_id' => $fresh->id, 'amount' => $fresh->amount, 'currency' => $cur]
                );
            }

            $this->logHistory($fresh, 'status_changed', $old, ['status' => $data['status']]);
        });

        $loan->refresh();

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        abort_unless($loan->status === LoanRequest::STATUS_DRAFT, 403, 'Seuls les brouillons peuvent être supprimés.');
        $loan->delete();
        return redirect()->route('admin.loans.index')->with('success', 'Demande supprimée.');
    }

    public function assignAdmin(Request $request, LoanRequest $loan)
    {
        abort_unless(Auth::user()->hasRole('super-admin'), 403);

        $data = $request->validate([
            'admin_id' => 'required|exists:users,id',
        ]);

        $admin = \App\Models\User::findOrFail($data['admin_id']);
        abort_unless(
            $admin->hasRole('admin') || $admin->hasRole('super-admin'),
            422,
            'L\'utilisateur sélectionné n\'est pas un administrateur.'
        );

        $old = ['admin_id' => $loan->admin_id, 'admin_name' => $loan->admin?->name];
        $loan->update(['admin_id' => $admin->id]);
        $this->logHistory($loan, 'admin_assigned', $old, ['admin_id' => $admin->id, 'admin_name' => $admin->name]);

        return back()->with('success', "Dossier réaffecté à {$admin->name}.");
    }

    private function clientsForAdmin(\App\Models\User $admin): \Illuminate\Support\Collection
    {
        if ($admin->hasRole('super-admin')) {
            return User::where('type', 'client')->orderBy('name')->get();
        }
        return User::where('type', 'client')
                   ->where('created_by', $admin->id)
                   ->orderBy('name')->get();
    }

    private function templatesForAdmin(\App\Models\User $admin): \Illuminate\Support\Collection
    {
        if ($admin->hasRole('super-admin')) {
            return ContractTemplate::orderBy('name')->get();
        }
        $assigned = $admin->assignedTemplates()->orderBy('name')->get();
        if ($assigned->isEmpty()) {
            return ContractTemplate::where('is_default', true)->get();
        }
        return $assigned;
    }

    private function authorizeAccess(LoanRequest $loan): void
    {
        $user = Auth::user();
        if ($user->hasRole('super-admin')) return;
        abort_unless($loan->admin_id === $user->id, 403, 'Accès non autorisé.');
    }

    private function logHistory(LoanRequest $loan, string $action, ?array $old, ?array $new): void
    {
        LoanHistory::create([
            'loan_request_id' => $loan->id,
            'admin_id'        => Auth::id(),
            'action'          => $action,
            'old_value'       => $old,
            'new_value'       => $new,
        ]);
    }

    /**
     * Retourne l'email actuel du client lié au dossier.
     * Priorité : email du compte User (toujours à jour) → email copié sur le dossier.
     */
    private function recipientEmail(LoanRequest $loan): string
    {
        return $loan->client?->email ?? $loan->email;
    }
}
