<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LoanRequestApprovedMail;
use App\Mail\LoanValidatedMail;
use App\Mail\SignedContractAcknowledgementMail;
use App\Mail\UserInvitationMail;
use App\Models\ClientNotification;
use App\Models\ContractTemplate;
use App\Models\LoanHistory;
use App\Models\LoanRequest;
use App\Models\User;
use App\Services\ContractService;
use App\Services\DocumentArchive;
use App\Services\LoanPdfService;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoanRequestController extends Controller
{
    public function __construct(
        private LoanService      $loanService,
        private ContractService  $contractService,
        private LoanPdfService   $pdfService,
    ) {}

    public function index(Request $request)
    {
        $admin = Auth::user();
        $query = LoanRequest::with(['client', 'admin'])
            ->where('admin_id', $admin->id);

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

        $stats = [
            'total'    => LoanRequest::where('admin_id', $admin->id)->count(),
            'draft'    => LoanRequest::where('admin_id', $admin->id)->where('status', 'draft')->count(),
            'pending'  => LoanRequest::where('admin_id', $admin->id)->where('status', 'pending')->count(),
            'validated'=> LoanRequest::where('admin_id', $admin->id)->where('status', 'validated')->count(),
            'finalized'=> LoanRequest::where('admin_id', $admin->id)->where('status', 'finalized')->count(),
        ];

        return view('admin.loans.index', compact('loans', 'stats'));
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
            'client_locale'     => 'nullable|in:fr,en,pl,es',
            'client_currency'   => 'nullable|string|max:10',
            // Prêt
            'amount'            => 'required|numeric|min:100',
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
        ], [
            'client_email.unique' => 'Cet email est déjà utilisé dans le système. Passez en mode "Client existant" pour sélectionner ce client.',
            'client_name.required_if'  => 'Le nom du client est obligatoire pour un nouveau client.',
            'client_email.required_if' => 'L\'email du client est obligatoire pour un nouveau client.',
            'client_id.required_if'    => 'Veuillez sélectionner un client existant.',
            'amount.required'   => 'Le montant est obligatoire.',
            'darly.required'    => 'La durée est obligatoire.',
        ]);

        $admin = Auth::user();

        // Créer ou récupérer le client
        $sendActivationEmail = false;
        $activationUrl       = null;

        if ($data['client_mode'] === 'new') {
            $token  = Str::random(64);
            $client = User::create([
                'name'             => $data['client_name'],
                'email'            => $data['client_email'],
                'password'         => Hash::make(Str::random(32)),
                'type'             => 'client',
                'created_by'       => $admin->id,
                'invitation_token' => $token,
                'phone'            => $data['client_phone'] ?? null,
                'address'          => $data['client_address'] ?? null,
                'birth_date'       => $data['client_birth_date'] ?? null,
                'id_type'          => $data['client_id_type'] ?? null,
                'id_number'        => $data['client_id_number'] ?? null,
                'locale'           => $data['client_locale'] ?? 'fr',
                'currency'         => $data['client_currency'] ?? $data['currency'],
            ]);
            $client->assignRole('client');
            $sendActivationEmail = true;
            $activationUrl = route('invitation.activate', ['token' => $token]);
        } else {
            $client = User::findOrFail($data['client_id']);
            // Un admin régulier ne peut sélectionner que ses propres clients
            if (!$admin->hasRole('super-admin')) {
                abort_unless($client->created_by === $admin->id, 403, 'Client non autorisé.');
            }
        }

        // Calculs financiers
        $calc = $this->loanService->calculateAll(
            (float) $data['amount'],
            5.00,
            (int) $data['darly']
        );

        // Créer la demande
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

        // Générer le contrat (aperçu FR stocké)
        $loan->contract_content = $this->contractService->generateFr($loan);
        $loan->save();

        // Envoyer l'email d'activation si nouveau client
        if ($sendActivationEmail && $activationUrl) {
            try {
                Mail::to($client->email)->send(new UserInvitationMail($client, $activationUrl));
            } catch (\Throwable $e) {
                Log::error('UserInvitationMail failed for ' . $client->email . ': ' . $e->getMessage());
            }
        }

        $this->logHistory($loan, 'created', null, ['status' => $loan->status]);

        return redirect()->route('admin.loans.show', $loan)
                         ->with('success', 'Dossier N°' . $loan->reference . ' créé (statut : Brouillon). '
                             . ($sendActivationEmail ? 'Un email d\'activation a été envoyé à ' . $client->email . '.' : ''));
    }

    public function show(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        $loan->load(['client', 'admin', 'history.admin', 'contractTemplate']);
        $generatedDocs = $loan->generatedDocuments()->with('generatedBy')->get();
        return view('admin.loans.show', compact('loan', 'generatedDocs'));
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
            'amount'               => 'required|numeric|min:100',
            'darly'                => 'required|integer|min:1|max:360',
            'objet'                => 'nullable|string|max:255',
            'subject'              => 'nullable|string|max:2000',
            'start_date'           => 'nullable|date',
            'currency'             => 'required|string|max:10',
            'admin_fees'           => 'nullable|numeric|min:0',
            'bank_account'         => 'nullable|string|max:255',
            'agent_suivi'          => 'nullable|string|max:255',
            'directeur'            => 'nullable|string|max:255',
            'special_conditions'   => 'nullable|string',
            'contract_template_id' => 'nullable|exists:contract_templates,id',
            'contract_language'    => 'nullable|in:fr,en,pl,es',
            'extra_fields'         => 'nullable|array',
            'extra_fields.*'       => 'nullable|string|max:500',
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

        // Régénérer l'aperçu dans la langue choisie
        $loan->refresh();
        $loan->contract_content = $this->contractService->generateForClient($loan);
        $loan->save();

        $this->logHistory($loan, 'updated', $old, $loan->only(['amount','darly','status','contract_template_id']));

        return redirect()->route('admin.loans.show', $loan)
                         ->with('success', 'Demande mise à jour.');
    }

    public function contract(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        $templates   = $this->templatesForAdmin(Auth::user());
        $previewHtml = $loan->contract_content
            ?? $this->contractService->generateFr($loan);
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

        $old = ['status' => $loan->status];

        $loan->update([
            'status'       => LoanRequest::STATUS_VALIDATED,
            'validated_at' => now(),
        ]);

        // Générer PDFs traduits dans la langue du client
        $locale          = $loan->contract_language ?? 'fr';
        $contractPdf     = $this->pdfService->generate($loan, $locale);
        $amortizationPdf = $this->pdfService->generateAmortizationPdf($loan, $locale);

        // Envoyer email au client avec contrat + tableau d'amortissement
        try {
            Mail::to($loan->email)->send(
                new LoanValidatedMail($loan, $contractPdf, $locale, $amortizationPdf)
            );
        } catch (\Throwable $e) {
            Log::error('LoanValidatedMail failed for ' . $loan->reference . ': ' . $e->getMessage());
        }

        // Mettre à jour statut → contract_sent
        $loan->update([
            'status'  => LoanRequest::STATUS_CONTRACT_SENT,
            'sent_at' => now(),
        ]);

        $this->logHistory($loan, 'validated_and_sent', $old, ['status' => $loan->status]);

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

        // Supprimer PDFs temporaires
        @unlink($contractPdf);
        @unlink($amortizationPdf);

        return redirect()->route('admin.loans.show', $loan)
                         ->with('success', 'Contrat validé et envoyé au client (avec tableau d\'amortissement).');
    }

    public function markSigned(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);

        $old = ['status' => $loan->status];
        $loan->update([
            'status'              => LoanRequest::STATUS_CONTRACT_SIGNED,
            'signed_received_at'  => now(),
        ]);

        // Email accusé réception au client
        $locale = $loan->contract_language ?? 'fr';
        Mail::to($loan->email)->send(new SignedContractAcknowledgementMail($loan, $locale));

        $this->logHistory($loan, 'signed_received', $old, ['status' => $loan->status]);

        return back()->with('success', 'Contrat signé marqué comme reçu. Email envoyé au client.');
    }

    public function updateStatus(Request $request, LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', LoanRequest::STATUSES),
        ]);

        $old = ['status' => $loan->status];
        $loan->update(['status' => $data['status']]);

        // Passage en "validé" → email de confirmation + notif in-app
        if ($data['status'] === LoanRequest::STATUS_VALIDATED && $old['status'] !== LoanRequest::STATUS_VALIDATED) {
            $locale = $loan->contract_language ?? 'fr';
            try {
                Mail::to($loan->email)->send(new LoanRequestApprovedMail($loan, $locale));
            } catch (\Throwable $e) {
                Log::error('LoanRequestApprovedMail failed for ' . $loan->reference . ': ' . $e->getMessage());
            }
            if ($loan->client_id) {
                ClientNotification::notifyUser(
                    $loan->client,
                    'loan_update',
                    'app.notif_loan_validated',
                    'app.notif_loan_validated_body',
                    ['reference' => $loan->reference],
                    ['loan_id' => $loan->id, 'reference' => $loan->reference, 'url' => '/app/loans']
                );
            }
        }

        // Créditer le solde du client lors du passage en "finalisé"
        if ($data['status'] === LoanRequest::STATUS_FINALIZED && $old['status'] !== LoanRequest::STATUS_FINALIZED) {
            $loan->client?->increment('balance', (float) $loan->amount);

            if ($loan->client_id) {
                $cur = $loan->currency ?? config('credixa.default_currency');
                ClientNotification::notifyUser(
                    $loan->client,
                    'credit',
                    'app.notif_loan_funded',
                    'app.notif_loan_funded_body',
                    ['amount' => number_format((float) $loan->amount, 2, ',', ' '), 'currency' => $cur],
                    ['loan_id' => $loan->id, 'amount' => $loan->amount, 'currency' => $cur]
                );
            }
        }

        $this->logHistory($loan, 'status_changed', $old, ['status' => $data['status']]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function destroy(LoanRequest $loan)
    {
        $this->authorizeAccess($loan);
        abort_unless($loan->status === LoanRequest::STATUS_DRAFT, 403, 'Seuls les brouillons peuvent être supprimés.');
        $loan->delete();
        return redirect()->route('admin.loans.index')->with('success', 'Demande supprimée.');
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
}
