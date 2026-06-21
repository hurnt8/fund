<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractTemplate;
use App\Models\User;
use App\Services\ContractDocxService;
use App\Services\ContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractTemplateController extends Controller
{
    public function __construct(
        private ContractDocxService $docxService,
        private ContractService     $contractService,
    ) {}

    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('super-admin')) {
            $templates = ContractTemplate::with('creator', 'assignedAdmins')->latest()->get();
        } else {
            $templates = $user->assignedTemplates()->with('creator')->latest()->get();
        }
        return view('admin.contract-templates.index', compact('templates'));
    }

    public function create()
    {
        $variables = $this->variablesList();
        return view('admin.contract-templates.create', compact('variables'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'content'       => 'nullable|string',
            'is_default'    => 'boolean',
            'locale'        => 'nullable|in:fr,en,pl,es',
            'template_type' => 'nullable|in:html,docx',
            'docx_file'     => 'nullable|file|max:10240',
        ]);

        if (!empty($data['is_default'])) {
            ContractTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        $templateType = 'html';
        $docxPath     = null;
        $detectedTags = null;

        if ($request->hasFile('docx_file')) {
            $file = $request->file('docx_file');
            if (strtolower($file->getClientOriginalExtension()) !== 'docx') {
                return back()->withInput()->withErrors(['docx_file' => 'Le fichier doit avoir l\'extension .docx']);
            }
            $docxPath     = $file->store('templates/docx', 'local');
            $detectedTags = $this->docxService->extractTags(Storage::path($docxPath));
            $templateType = 'docx';
        }

        $template = ContractTemplate::create([
            'name'          => $data['name'],
            'content'       => $data['content'] ?? '',
            'is_default'    => $data['is_default'] ?? false,
            'template_type' => $templateType,
            'locale'        => $data['locale'] ?? null,
            'docx_path'     => $docxPath,
            'detected_tags' => $detectedTags,
            'created_by'    => Auth::id(),
        ]);

        $this->handleImageUploads($request, $template);

        return redirect()->route('admin.contract-templates.index')
                         ->with('success', 'Modèle créé.');
    }

    public function edit(ContractTemplate $contractTemplate)
    {
        $variables = $this->variablesList();
        $admins    = Auth::user()->hasRole('super-admin')
            ? User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->orderBy('name')->get()
            : collect();
        $assignedIds = $contractTemplate->assignedAdmins()->pluck('users.id')->toArray();

        return view('admin.contract-templates.edit', [
            'template'    => $contractTemplate,
            'variables'   => $variables,
            'admins'      => $admins,
            'assignedIds' => $assignedIds,
        ]);
    }

    public function update(Request $request, ContractTemplate $contractTemplate)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'content'       => 'nullable|string',
            'is_default'    => 'boolean',
            'locale'        => 'nullable|in:fr,en,pl,es',
            'template_type' => 'nullable|in:html,docx',
            'docx_file'     => 'nullable|file|max:10240',
        ]);

        if (!empty($data['is_default'])) {
            ContractTemplate::where('id', '!=', $contractTemplate->id)
                             ->update(['is_default' => false]);
        }

        $updates = [
            'name'       => $data['name'],
            'content'    => $data['content'] ?? $contractTemplate->content,
            'is_default' => $data['is_default'] ?? false,
            'locale'     => $data['locale'] ?? $contractTemplate->locale,
        ];

        if ($request->hasFile('docx_file')) {
            $file = $request->file('docx_file');
            if (strtolower($file->getClientOriginalExtension()) !== 'docx') {
                return back()->withInput()->withErrors(['docx_file' => 'Le fichier doit avoir l\'extension .docx']);
            }
            if ($contractTemplate->docx_path) {
                Storage::delete($contractTemplate->docx_path);
            }
            $updates['docx_path']     = $file->store('templates/docx', 'local');
            $updates['detected_tags'] = $this->docxService->extractTags(Storage::path($updates['docx_path']));
            $updates['template_type'] = 'docx';
        }

        $contractTemplate->update($updates);
        $this->handleImageUploads($request, $contractTemplate);

        // Synchroniser les admins assignés (super-admin uniquement)
        if (Auth::user()->hasRole('super-admin')) {
            $adminIds = array_filter(array_map('intval', (array) $request->input('assigned_admins', [])));
            $contractTemplate->assignedAdmins()->sync($adminIds);
        }

        return redirect()->route('admin.contract-templates.index')
                         ->with('success', 'Modèle mis à jour.');
    }

    public function saveContent(Request $request, ContractTemplate $contractTemplate)
    {
        $request->validate(['content' => 'required|string']);
        $contractTemplate->update(['content' => $request->content]);
        return response()->json(['ok' => true]);
    }

    private function handleImageUploads(Request $request, ContractTemplate $template): void
    {
        $imageFields = [
            'watermark'       => 'watermark_path',
            'logo_left'       => 'logo_left_path',
            'logo_right'      => 'logo_right_path',
            'stamp'           => 'stamp_path',
            'signature_admin' => 'signature_admin_path',
            'signature_agent' => 'signature_agent_path',
        ];

        foreach ($imageFields as $input => $column) {
            if ($request->hasFile($input)) {
                $old = $template->$column;
                $template->$column = $request->file($input)->store(
                    'contract-images', 'public'
                );
                if ($old) Storage::disk('public')->delete($old);
            } elseif ($request->boolean('remove_' . $input) && $template->$column) {
                Storage::disk('public')->delete($template->$column);
                $template->$column = null;
            }
        }

        $template->save();
    }

    public function destroy(ContractTemplate $contractTemplate)
    {
        abort_if($contractTemplate->is_default, 403, 'Impossible de supprimer le modèle par défaut.');
        $contractTemplate->delete();
        return back()->with('success', 'Modèle supprimé.');
    }

    public function preview(ContractTemplate $contractTemplate, Request $request)
    {
        $locale = in_array($request->get('locale'), ['fr','en','pl','es'])
                ? $request->get('locale') : 'fr';
        $isFem  = $request->get('gender') === 'F';

        [$allVars] = $this->buildAllVars($locale, $isFem);

        $baseUrl     = route('admin.contract-templates.preview', $contractTemplate);
        $editUrl     = route('admin.contract-templates.edit', $contractTemplate);
        $genderParam = $isFem ? 'F' : 'M';

        // ── Barre commune ─────────────────────────────────────────────────────
        $langTabs = '';
        foreach (['fr' => 'FR', 'en' => 'EN', 'pl' => 'PL', 'es' => 'ES'] as $lc => $label) {
            $active    = $lc === $locale ? ' pv-lang-active' : '';
            $href      = $baseUrl . '?locale=' . $lc . '&gender=' . $genderParam;
            $langTabs .= '<a href="' . $href . '" class="pv-lang' . $active . '" data-lc="' . $lc . '">' . $label . '</a>';
        }

        $mUrl = $baseUrl . '?locale=' . $locale . '&gender=M';
        $fUrl = $baseUrl . '?locale=' . $locale . '&gender=F';
        $genderBtns = '<a href="' . $mUrl . '" class="pv-gen' . (!$isFem ? ' pv-gen-active' : '') . '" data-g="M" title="Masculin">♂</a>'
                    . '<a href="' . $fUrl . '" class="pv-gen' . ($isFem  ? ' pv-gen-active' : '') . '" data-g="F" title="Féminin">♀</a>';

        $typeBadge = $contractTemplate->template_type === 'docx'
            ? '<span class="pv-type">DOCX</span>'
            : '<span class="pv-type pv-type-html">HTML</span>';

        $editIcon = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';

        $barHtml = '<div id="pv-bar">'
            . '<span class="pv-badge">Aperçu</span>' . $typeBadge
            . '<span class="pv-name">' . e($contractTemplate->name) . '</span>'
            . '<span class="pv-div"></span>'
            . '<div class="pv-langs">' . $langTabs . '</div>'
            . '<div class="pv-gens">' . $genderBtns . '</div>'
            . '<span class="pv-spacer"></span>'
            . '<a href="' . $editUrl . '" class="pv-act pv-act-gold">' . $editIcon . ' Modifier</a>'
            . '<button onclick="window.close()" class="pv-act pv-act-close">✕</button>'
            . '</div>';

        // ── Images (HTML templates) ───────────────────────────────────────────
        $imgs = [];
        foreach (['watermark_path','logo_left_path','logo_right_path','stamp_path','signature_admin_path','signature_agent_path'] as $f) {
            $imgs[$f] = $contractTemplate->$f ? asset('storage/' . $contractTemplate->$f) : null;
        }

        $isDocx = $contractTemplate->template_type === 'docx' && $contractTemplate->docx_path;

        if ($isDocx) {
            // Balises détectées avec info connue/inconnue
            $allVarNames     = array_map(fn($d) => $d, $this->variablesList()); // key => desc
            $detectedBalises = [];
            foreach (($contractTemplate->detected_tags ?? []) as $tag) {
                $detectedBalises[$tag] = $allVarNames[$tag] ?? null;
            }

            $downloadUrl  = route('admin.contract-templates.download-docx', $contractTemplate);
            $docxFrameUrl = route('admin.contract-templates.docx-frame', $contractTemplate)
                          . '?locale=' . $locale . '&gender=' . $genderParam;

            return view('admin.contract-templates.preview', [
                'template'         => $contractTemplate,
                'isDocx'           => true,
                'locale'           => $locale,
                'isFem'            => $isFem,
                'barHtml'          => $barHtml,
                'docxFrameUrl'     => $docxFrameUrl,
                'docxFrameBase'    => route('admin.contract-templates.docx-frame', $contractTemplate),
                'detectedBalises'  => $detectedBalises,
                'downloadUrl'      => $downloadUrl,
            ]);
        }

        // ── HTML template ─────────────────────────────────────────────────────
        // Convertir les {balises} en chips éditables
        $editableContent = preg_replace(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            '<span class="balise-chip" contenteditable="false">{$1}</span>',
            $contractTemplate->content
        );

        return view('admin.contract-templates.preview', [
            'template'         => $contractTemplate,
            'isDocx'           => false,
            'locale'           => $locale,
            'isFem'            => $isFem,
            'barHtml'          => $barHtml,
            'editableContent'  => $editableContent,
            'rawContent'       => $contractTemplate->content,
            'sampleVars'       => $allVars,
            'categorizedVars'  => $this->categorizedVariables(),
            'imgs'             => $imgs,
            'saveUrl'          => route('admin.contract-templates.save-content', $contractTemplate),
        ]);
    }

    public function downloadDocx(ContractTemplate $contractTemplate, Request $request)
    {
        if (!$contractTemplate->docx_path) {
            abort(404);
        }

        $templatePath = Storage::path($contractTemplate->docx_path);

        // Récupère le HTML sauvegardé (contient le watermark CSS + images absolues)
        $savedHtml = (!empty($contractTemplate->content)
                      && stripos($contractTemplate->content, '<html') !== false)
                   ? $contractTemplate->content
                   : null;

        try {
            // Crée un DOCX avec {balises} préservées + modifications (watermark, images) intégrées
            $outDocx  = $this->docxService->createDownloadDocx($templatePath, $savedHtml);
            $filename = Str::slug($contractTemplate->name) . '.docx';

            return response()->download($outDocx, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('downloadDocx failed: ' . $e->getMessage());
            abort(500, 'Erreur lors de la génération du DOCX.');
        }
    }

    public function docxFrame(ContractTemplate $contractTemplate, Request $request)
    {
        if ($contractTemplate->template_type !== 'docx' || !$contractTemplate->docx_path) {
            abort(404);
        }

        $locale = in_array($request->get('locale'), ['fr','en','pl','es'])
                ? $request->get('locale') : 'fr';
        $isFem  = $request->get('gender') === 'F';

        [$allVars] = $this->buildAllVars($locale, $isFem);

        // User has saved an edited HTML version → re-inject DOCX images then parse
        if (!empty($contractTemplate->content) && stripos($contractTemplate->content, '<html') !== false) {
            $savedHtml = $contractTemplate->content;
            $docxPath  = Storage::path($contractTemplate->docx_path);
            $savedHtml = $this->docxService->reInjectImages($savedHtml, $docxPath);
            $data              = $this->docxService->parseSavedHtmlToPages($savedHtml, $locale, $docxPath);
            $data['hasEdit']   = true;
            return response()->json($data);
        }

        $docxPath = Storage::path($contractTemplate->docx_path);
        $data     = $this->docxService->renderHtmlScoped($docxPath, $allVars, $locale);
        $data['hasEdit'] = false;

        return response()->json($data);
    }

    public function resetDocxEdit(ContractTemplate $contractTemplate)
    {
        abort_if($contractTemplate->template_type !== 'docx', 400, 'Not a DOCX template');
        $contractTemplate->update(['content' => '']);
        return response()->json(['ok' => true]);
    }

    // ── Helpers privés ─────────────────────────────────────────────────────────

    private function buildAllVars(string $locale, bool $isFem): array
    {
        $genderVars = [
            '{ne_e}'         => match($locale) {
                'fr'    => $isFem ? 'née'        : 'né',
                'pl'    => $isFem ? 'urodzona'   : 'urodzony',
                'es'    => $isFem ? 'nacida'     : 'nacido',
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

        $translationVars = $this->contractService->getTranslationVars($locale);

        $dataVars = [
            '{reference}'       => 'CR-2026-0001',
            '{archive}'         => 'CR-ARCH-PREVIEW',
            '{nom_client}'      => 'Jean Dupont',
            '{adresse_client}'  => '12 Rue de la Paix, 75001 Paris, France',
            '{date_naissance}'  => '15/03/1985',
            '{type_identite}'   => match($locale) { 'pl' => 'Paszport', 'en' => 'Passport', 'es' => 'Pasaporte', default => 'Passeport' },
            '{numero_identite}' => 'AB123456789',
            '{agent_suivi}'     => 'Marie Martin',
            '{montant}'         => '10 000,00',
            '{devise}'          => match($locale) { 'pl' => 'PLN', default => 'EUR' },
            '{duree}'           => '12',
            '{mensualite}'      => '856,07',
            '{taux}'            => '5',
            '{frais_admin}'     => '150,00',
            '{compte_bancaire}' => 'DE28 2007 0024 0567 3876 05 — BIC : DEUTDEDB',
            '{date}'            => now()->format('d/m/Y'),
            '{societe}'         => 'CREDIXA INVESTI',
        ];

        $allVars = array_merge($translationVars, $genderVars, $dataVars);
        $allVars = array_map(
            fn($v) => str_replace(array_keys($allVars), array_values($allVars), $v),
            $allVars
        );

        return [$allVars, $genderVars, $translationVars, $dataVars];
    }

    private function categorizedVariables(): array
    {
        return [
            'Données du dossier' => [
                '{reference}'       => 'Référence du dossier',
                '{archive}'         => 'Numéro d\'archive',
                '{nom_client}'      => 'Nom complet du client',
                '{adresse_client}'  => 'Adresse postale du client',
                '{date_naissance}'  => 'Date de naissance',
                '{type_identite}'   => 'Type de pièce d\'identité',
                '{numero_identite}' => 'Numéro de pièce d\'identité',
                '{agent_suivi}'     => 'Nom de l\'agent de suivi',
                '{montant}'         => 'Montant du prêt',
                '{devise}'          => 'Devise (EUR, PLN…)',
                '{duree}'           => 'Durée en mois',
                '{mensualite}'      => 'Mensualité calculée',
                '{taux}'            => 'Taux d\'intérêt (%)',
                '{frais_admin}'     => 'Frais administratifs',
                '{compte_bancaire}' => 'Coordonnées bancaires de règlement',
                '{date}'            => 'Date de validation du contrat',
                '{societe}'         => 'Nom de la société (CREDIXA INVESTI)',
            ],
            'Accord de genre' => [
                '{ne_e}'            => 'né / née selon la langue et le genre',
                '{denomme_e}'       => 'dénommé / dénommée (fr) · zwany/a (pl) · denominado/a (es)',
                '{zamieszkal_a}'    => 'Polonais : zamieszkały / zamieszkała',
                '{e}'               => 'Suffixe de genre : vide/"e" (fr) · "y"/"a" (pl) · "o"/"a" (es)',
            ],
            'Structure & Traductions' => [
                '{title}'           => 'Titre du contrat (traduit)',
                '{header}'          => 'En-tête pays (traduit)',
                '{between}'         => 'Section "Entre les parties" (traduit)',
                '{lender_label}'    => 'Libellé société prêteuse (traduit)',
                '{lender_desc}'     => 'Description de la société (traduit)',
                '{borrower_label}'  => 'Libellé emprunteur (traduit)',
                '{borrower_desc}'   => 'Description emprunteur avec adresse, naissance et ID',
                '{agent_label}'     => 'Libellé agent de suivi (traduit)',
                '{agent_desc}'      => 'Description agent (traduit)',
                '{finance_title}'   => 'Titre section financement (traduit)',
                '{amount_label}'    => 'Libellé montant (traduit)',
                '{duration_label}'  => 'Libellé durée (traduit)',
                '{months}'          => 'Mot "mois" traduit',
                '{monthly_label}'   => 'Libellé mensualité (traduit)',
                '{rate_label}'      => 'Libellé taux (traduit)',
                '{fees_label}'      => 'Libellé frais administratifs (traduit)',
                '{bank_label}'      => 'Libellé coordonnées bancaires (traduit)',
                '{made_at}'         => 'Libellé "Fait le" (traduit)',
                '{sig_lender}'      => 'Libellé signature prêteur (traduit)',
                '{sig_agent}'       => 'Libellé signature agent (traduit)',
                '{sig_borrower}'    => 'Libellé signature emprunteur (traduit)',
            ],
            'Articles du contrat' => [
                '{art1_title}'      => 'Titre Article 1 — Objet du prêt',
                '{art1_body}'       => 'Corps Article 1',
                '{art2_title}'      => 'Titre Article 2 — Remboursement',
                '{art2_body}'       => 'Corps Article 2',
                '{art3_title}'      => 'Titre Article 3 — Intérêts',
                '{art3_body}'       => 'Corps Article 3',
                '{art4_title}'      => 'Titre Article 4 — Engagements',
                '{art4_body}'       => 'Corps Article 4',
                '{art5_title}'      => 'Titre Article 5 — Frais',
                '{art5_body}'       => 'Corps Article 5',
                '{art6_title}'      => 'Titre Article 6 — Règlement amiable',
                '{art6_body}'       => 'Corps Article 6',
                '{art7_title}'      => 'Titre Article 7 — Litiges',
                '{art7_body}'       => 'Corps Article 7',
                '{art8_title}'      => 'Titre Article 8 — Signature',
                '{art8_body}'       => 'Corps Article 8',
            ],
        ];
    }

    /**
     * Retourne les balises du template qui ne sont pas dans la liste standard.
     * Utilisé en AJAX par le formulaire de création de demande.
     */
    public function missingVars(ContractTemplate $contractTemplate)
    {
        $knownKeys     = array_keys($this->variablesList()); // ex: ['{nom_client}', ...]
        $detectedTags  = $contractTemplate->detected_tags ?? []; // ex: ['{nom_client}', '{ville}']

        $unknownTags = array_values(array_filter($detectedTags, function ($tag) use ($knownKeys) {
            return !in_array($tag, $knownKeys, true);
        }));

        // Nettoyer les accolades pour retourner juste le nom
        $fields = array_map(fn($t) => trim($t, '{}'), $unknownTags);

        return response()->json(['fields' => $fields]);
    }

    private function variablesList(): array
    {
        return [
            // ── Données du dossier ─────────────────────────────────────────
            '{reference}'       => 'Référence du dossier',
            '{archive}'         => 'Numéro d\'archive',
            '{nom_client}'      => 'Nom complet du client',
            '{adresse_client}'  => 'Adresse du client',
            '{date_naissance}'  => 'Date de naissance',
            '{type_identite}'   => 'Type de pièce d\'identité (traduit selon la langue)',
            '{numero_identite}' => 'Numéro de pièce d\'identité',
            '{ne_e}'            => 'Accord genre : né/née (fr) · born (en) · urodzony/a (pl) · nacido/a (es)',
            '{denomme_e}'       => 'Accord genre : dénommé/dénommée (fr) · zwany/a (pl) · denominado/a (es)',
            '{zamieszkal_a}'    => 'Accord genre polonais : zamieszkały / zamieszkała',
            '{e}'               => 'Suffixe de genre : vide/"e" (fr) · "y"/"a" (pl) · "o"/"a" (es)',
            '{agent_suivi}'     => 'Nom de l\'agent (admin)',
            '{montant}'         => 'Montant du prêt',
            '{devise}'          => 'Devise (EUR, PLN…)',
            '{duree}'           => 'Durée en mois',
            '{mensualite}'      => 'Mensualité calculée',
            '{taux}'            => 'Taux d\'intérêt (5 %)',
            '{frais_admin}'     => 'Frais administratifs',
            '{compte_bancaire}' => 'Coordonnées bancaires de règlement',
            '{date}'            => 'Date de validation du contrat',
            '{societe}'         => 'Nom de la société (CREDIXA INVESTI)',
            // ── Traductions automatiques (résolues selon la langue du client) ─
            '{title}'           => 'Titre du contrat (traduit)',
            '{header}'          => 'En-tête pays (traduit)',
            '{between}'         => 'Section "Entre les parties" (traduit)',
            '{lender_label}'    => 'Libellé "Société prêteuse" (traduit)',
            '{lender_desc}'     => 'Description de la société prêteuse (traduit)',
            '{borrower_label}'  => 'Libellé "Emprunteur" (traduit)',
            '{borrower_desc}'   => 'Description emprunteur avec adresse, naissance et pièce d\'identité (traduit)',
            '{agent_label}'     => 'Libellé "Agent de suivi" (traduit)',
            '{agent_desc}'      => 'Description agent (traduit)',
            '{finance_title}'   => 'Titre section financement (traduit)',
            '{amount_label}'    => 'Libellé montant (traduit)',
            '{duration_label}'  => 'Libellé durée (traduit)',
            '{months}'          => 'Mot "mois" (traduit)',
            '{monthly_label}'   => 'Libellé mensualité (traduit)',
            '{rate_label}'      => 'Libellé taux (traduit)',
            '{fees_label}'      => 'Libellé frais admin (traduit)',
            '{bank_label}'      => 'Libellé coordonnées bancaires (traduit)',
            '{art1_title}'      => 'Titre Article 1 (traduit)',
            '{art1_body}'       => 'Corps Article 1 — Objet (traduit)',
            '{art2_title}'      => 'Titre Article 2 (traduit)',
            '{art2_body}'       => 'Corps Article 2 — Remboursement (traduit)',
            '{art3_title}'      => 'Titre Article 3 (traduit)',
            '{art3_body}'       => 'Corps Article 3 — Intérêts (traduit)',
            '{art4_title}'      => 'Titre Article 4 (traduit)',
            '{art4_body}'       => 'Corps Article 4 — Engagements (traduit)',
            '{art5_title}'      => 'Titre Article 5 (traduit)',
            '{art5_body}'       => 'Corps Article 5 — Frais (traduit)',
            '{art6_title}'      => 'Titre Article 6 (traduit)',
            '{art6_body}'       => 'Corps Article 6 — Règlement (traduit)',
            '{art7_title}'      => 'Titre Article 7 (traduit)',
            '{art7_body}'       => 'Corps Article 7 — Litiges (traduit)',
            '{art8_title}'      => 'Titre Article 8 (traduit)',
            '{art8_body}'       => 'Corps Article 8 — Signature (traduit)',
            '{made_at}'         => 'Libellé "Fait le" (traduit)',
            '{sig_lender}'      => 'Libellé signature prêteur (traduit)',
            '{sig_agent}'       => 'Libellé signature agent (traduit)',
            '{sig_borrower}'    => 'Libellé signature emprunteur (traduit)',
        ];
    }
}
