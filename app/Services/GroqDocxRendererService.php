<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * Agent Groq + phpoffice pour le rendu DOCX.
 *
 * phpoffice gère : texte, tableaux, styles, listes, polices.
 * Groq gère      : analyse XML DOCX → positionnement précis des images ancrées.
 *
 * Pipeline :
 *   1. Extraire la structure DOCX (XML brut : anchors, page, marges, paragraphes)
 *   2. Envoyer à Groq → JSON des positions CSS corrigées pour chaque image
 *   3. Injecter les images avec les positions retournées par Groq
 *   4. Retourner le rapport de rendu (log de ce que Groq a décidé)
 */
class GroqDocxRendererService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl;

    // Dimensions de la page A4 preview (px à 96 dpi)
    private const PAGE_W  = 794;
    private const PAGE_H  = 1123;
    private const EMU_PX  = 9525; // 1 px = 9525 EMU à 96 dpi
    private const TWP_PX  = 15;   // 1 px = 15 twips

    public function __construct()
    {
        $this->apiKey = (string) config('services.groq.key', '');
        $this->model  = (string) config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->apiUrl = (string) config('services.groq.url', 'https://api.groq.com/openai/v1/chat/completions');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    // ── Point d'entrée principal ──────────────────────────────────────────────

    /**
     * Analyse le DOCX et retourne un tableau de positions CSS corrigées par Groq.
     * Chaque entrée : ['rid','left','top','width','height','zIndex','opacity','behind']
     * Retourne aussi une clé spéciale '__report' avec le rapport textuel de l'agent.
     */
    public function analyzeAndPosition(string $docxPath): array
    {
        $structure = $this->extractDocxStructure($docxPath);

        if (empty($structure['anchors'])) {
            return ['images' => [], '__report' => 'Aucune image ancrée détectée.'];
        }

        // Fallback si Groq non configuré : positions mathématiques simples
        if (!$this->isConfigured()) {
            Log::info('[GroqDocx] GROQ_API_KEY absent — fallback positions mathématiques.');
            return [
                'images'   => $this->fallbackPositions($structure),
                '__report' => '⚠️ GROQ_API_KEY non configuré. Positions calculées par conversion EMU simple.',
            ];
        }

        return $this->callGroq($structure);
    }

    /**
     * Injecte les images dans le HTML phpoffice selon les positions retournées par analyzeAndPosition().
     * Remplace la logique actuelle de injectDocxImages() pour les wp:anchor.
     *
     * @param  string $html     HTML généré par phpoffice
     * @param  string $docxPath Chemin du DOCX
     * @param  array  $groqResult Résultat de analyzeAndPosition()
     * @return array  ['html' => string, 'report' => string]
     */
    public function injectPositionedImages(string $html, string $docxPath, array $groqResult): array
    {
        $positions = $groqResult['images'] ?? [];
        $report    = $groqResult['__report'] ?? '';

        if (empty($positions)) {
            return ['html' => $html, 'report' => $report];
        }

        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return ['html' => $html, 'report' => $report . "\n❌ Impossible d'ouvrir le DOCX."];
        }

        // Charger la relation map
        $bodyRels = $zip->getFromName('word/_rels/document.xml.rels') ?: '';
        preg_match_all('/Id="([^"]+)"[^>]+Target="([^"]+)"/', $bodyRels, $rm);
        $relMap = array_combine($rm[1], $rm[2]);

        $injected  = 0;
        $divs      = '';
        $usedRids  = [];

        foreach ($positions as $pos) {
            $rid = $pos['rid'] ?? null;
            if (!$rid) continue;

            $target = $relMap[$rid] ?? null;
            if (!$target) continue;

            $imgPath = 'word/' . ltrim($target, '/');
            $imgData = $zip->getFromName($imgPath);
            if (!$imgData) continue;

            $ext  = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
            $mime = match($ext) { 'jpg','jpeg' => 'image/jpeg', 'gif' => 'image/gif', default => 'image/png' };
            $src  = 'data:' . $mime . ';base64,' . base64_encode($imgData);

            $left    = max(0, (int)($pos['left']    ?? 0));
            $top     = max(0, (int)($pos['top']     ?? 0));
            $width   = max(1, (int)($pos['width']   ?? 100));
            $height  = max(1, (int)($pos['height']  ?? 100));
            $zIndex  = (int)($pos['zIndex']  ?? 10);
            $opacity = number_format((float)($pos['opacity'] ?? 1.0), 2);

            $divs .= sprintf(
                '<img src="%s" data-docx-auto="1" '
                . 'style="position:absolute;left:%dpx;top:%dpx;width:%dpx;height:%dpx;'
                . 'z-index:%d;opacity:%s;object-fit:contain;pointer-events:none">',
                $src, $left, $top, $width, $height, $zIndex, $opacity
            );
            $usedRids[] = $rid;
            $injected++;
        }

        $zip->close();

        if ($divs) {
            $container = '<div class="crx-textboxes" '
                . 'style="position:absolute;inset:0;pointer-events:none;z-index:0">'
                . $divs . '</div>';
            $html = preg_replace('/<body([^>]*)>/i', '<body$1>' . $container, $html, 1) ?? $html;
        }

        $report .= "\n✅ $injected image(s) injectée(s) par l'agent Groq.";

        return ['html' => $html, 'report' => $report, 'usedRids' => $usedRids];
    }

    // ── Extraction de la structure DOCX ──────────────────────────────────────

    private function extractDocxStructure(string $docxPath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) return ['anchors' => []];

        $docXml    = $zip->getFromName('word/document.xml') ?: '';
        $stylesXml = $zip->getFromName('word/styles.xml')   ?: '';
        $bodyRels  = $zip->getFromName('word/_rels/document.xml.rels') ?: '';
        $zip->close();

        // Dimensions de la page réelle
        [$pageWpx, $pageHpx, $marginPx] = $this->extractPageDimensions($docXml);

        // Compter les paragraphes (pour l'estimation verticale)
        // On scanne via strpos pour éviter les limites de regex sur grands XML
        $totalPars = substr_count($docXml, '<w:p ') + substr_count($docXml, '<w:p>');

        // Espacement inter-lignes par défaut (twips/20 → pts → px)
        $lineSpacingPx = 18.5;
        if (preg_match('/<w:line w:val="(\d+)"/', $stylesXml, $lm)) {
            $lineSpacingPx = round((int)$lm[1] / 20 * 96 / 72, 1);
        }

        // Taille de police par défaut
        $defaultFontPt = 12;
        if (preg_match('/w:sz w:val="(\d+)"/', $stylesXml, $fm)) {
            $defaultFontPt = (int)$fm[1] / 2;
        }

        // Map rId → target
        preg_match_all('/Id="([^"]+)"[^>]+Target="([^"]+)"/', $bodyRels, $rm);
        $relMap = array_combine($rm[1], $rm[2]);

        // ── Scan direct de TOUS les <wp:anchor> dans le XML ──────────────────
        // On évite la décomposition par <w:p> qui rate les anchors dans les
        // paragraphes complexes (mc:AlternateContent, sectPr, etc.)
        $anchors = [];
        preg_match_all('/<wp:anchor\b[^>]*>.*?<\/wp:anchor>/s', $docXml, $allAnchors);

        foreach ($allAnchors[0] as $anchorXml) {
            // Estimer l'index de paragraphe : compter les <w:p> avant cet anchor dans le XML
            $anchorPos  = strpos($docXml, $anchorXml);
            $xmlBefore  = $anchorPos !== false ? substr($docXml, 0, $anchorPos) : '';
            $parIdx     = substr_count($xmlBefore, '<w:p ') + substr_count($xmlBefore, '<w:p>');
            $estimatedTopPx = $marginPx + ($parIdx * $lineSpacingPx * 1.05);

            preg_match('/r:embed="(rId[^"]+)"/', $anchorXml, $ridm);
            preg_match('/<wp:extent cx="(\d+)" cy="(\d+)"/', $anchorXml, $extm);
            preg_match('/<wp:positionH relativeFrom="(\w+)">.*?<wp:posOffset>(-?\d+)<\/wp:posOffset>/s', $anchorXml, $phm);
            preg_match('/<wp:positionV relativeFrom="(\w+)">.*?<wp:posOffset>(-?\d+)<\/wp:posOffset>/s', $anchorXml, $pvm);
            preg_match('/behindDoc="(\d)"/', $anchorXml, $bdm);
            preg_match('/<wp:wrap(\w+)/', $anchorXml, $wrm);
            preg_match('/<wp:docPr[^>]*name="([^"]+)"/', $anchorXml, $nmm);

            $rid     = $ridm[1]  ?? null;
            $widthPx = isset($extm[1]) ? (int)round((int)$extm[1] / self::EMU_PX) : 0;
            $heightPx= isset($extm[2]) ? (int)round((int)$extm[2] / self::EMU_PX) : 0;
            $relH    = $phm[1]   ?? 'margin';
            $offsetH = isset($phm[2]) ? (int)round((int)$phm[2] / self::EMU_PX) : 0;
            $relV    = $pvm[1]   ?? 'paragraph';
            $offsetV = isset($pvm[2]) ? (int)round((int)$pvm[2] / self::EMU_PX) : 0;
            $behind  = ($bdm[1]  ?? '0') === '1';
            $wrap    = $wrm[1]   ?? 'None';
            $name    = $nmm[1]   ?? "img_$parIdx";

            // Position mathématique de référence (utilisée par le fallback et comme indice pour Groq)
            $mathLeft = match($relH) {
                'page'   => $offsetH,
                default  => $marginPx + $offsetH,
            };
            $mathTop = match($relV) {
                'page'   => $offsetV,
                'margin' => $marginPx + $offsetV,
                default  => (int)round($estimatedTopPx) + $offsetV,
            };

            $anchors[] = [
                'rid'            => $rid,
                'name'           => $name,
                'parIndex'       => $parIdx,
                'totalPars'      => $totalPars,
                'estimatedTopPx' => (int)round($estimatedTopPx),
                'widthPx'        => $widthPx,
                'heightPx'     => $heightPx,
                'posH_relFrom' => $relH,
                'posH_offsetPx'=> $offsetH,
                'posV_relFrom' => $relV,
                'posV_offsetPx'=> $offsetV,
                'behind'       => $behind,
                'wrapType'     => $wrap,
                'mediaTarget'  => $rid ? ($relMap[$rid] ?? null) : null,
                'math_left'    => $mathLeft,
                'math_top'     => $mathTop,
            ];
        }

        return [
            'pageWidthPx'    => $pageWpx,
            'pageHeightPx'   => $pageHpx,
            'marginPx'       => $marginPx,
            'totalPars'      => $totalPars,
            'lineSpacingPx'  => $lineSpacingPx,
            'defaultFontPt'  => $defaultFontPt,
            'anchors'        => $anchors,
        ];
    }

    private function extractPageDimensions(string $docXml): array
    {
        $pageW  = self::PAGE_W;
        $pageH  = self::PAGE_H;
        $margin = 48;

        if (preg_match('/<w:pgSz\b[^>]*w:w="(\d+)"[^>]*w:h="(\d+)"/', $docXml, $m)) {
            $pageW = max(self::PAGE_W, (int)round((int)$m[1] / self::TWP_PX));
            $pageH = max(self::PAGE_H, (int)round((int)$m[2] / self::TWP_PX));
        }
        if (preg_match('/<w:pgMar\b[^>]*w:left="(\d+)"/', $docXml, $m)) {
            $margin = max(1, (int)round((int)$m[1] / self::TWP_PX));
        }

        return [$pageW, $pageH, $margin];
    }

    // ── Appel Groq ────────────────────────────────────────────────────────────

    private function callGroq(array $structure): array
    {
        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt   = $this->buildUserPrompt($structure);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->apiUrl, [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userPrompt],
                    ],
                    'temperature'     => 0.05,
                    'max_tokens'      => 2048,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (!$response->successful()) {
                Log::error('[GroqDocx] API error ' . $response->status() . ': ' . $response->body());
                return [
                    'images'   => $this->fallbackPositions($structure),
                    '__report' => '⚠️ Groq API error ' . $response->status() . ' — fallback positions mathématiques utilisées.',
                ];
            }

            $data    = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '{}';
            $result  = json_decode($content, true);

            if (!is_array($result) || empty($result['images'])) {
                Log::warning('[GroqDocx] Réponse Groq invalide : ' . $content);
                return [
                    'images'   => $this->fallbackPositions($structure),
                    '__report' => '⚠️ Réponse Groq invalide — fallback mathématique.',
                ];
            }

            $report = $result['report'] ?? 'Analyse Groq complète.';
            Log::info('[GroqDocx] ' . $report);

            return [
                'images'   => $result['images'],
                '__report' => "🤖 Agent Groq ({$this->model}) :\n" . $report,
            ];

        } catch (\Throwable $e) {
            Log::error('[GroqDocx] Exception : ' . $e->getMessage());
            return [
                'images'   => $this->fallbackPositions($structure),
                '__report' => '⚠️ Groq indisponible (' . $e->getMessage() . ') — fallback mathématique.',
            ];
        }
    }

    // ── Prompts ───────────────────────────────────────────────────────────────

    private function buildSystemPrompt(): string
    {
        return <<<'SYSTEM'
Tu es un expert en rendu DOCX vers HTML. Tu analyses la structure XML d'un document Word (wp:anchor, positions EMU, paragraphes) et tu calcules les coordonnées CSS pixel exactes pour afficher chaque image à la bonne position dans un aperçu HTML A4.

RÈGLES DE CONVERSION :
1. La page HTML fait exactement pageWidthPx × pageHeightPx pixels avec un conteneur CSS `position:relative`.
2. Le contenu commence à marginPx pixels du bord (top, left, right, bottom).
3. 1 pixel = 9525 EMU (96 dpi).
4. 1 pixel = 15 twips.

SYSTÈMES DE RÉFÉRENCE positionH/positionV :
- "page"   → left/top = posOffset (depuis bord de page, incluant les marges)
- "margin" → left/top = marginPx + posOffset (depuis intérieur des marges)
- "column" → identique à "margin" (document 1 colonne)
- "paragraph" → l'image est ancrée à un paragraphe. Utilise estimatedTopPx (hauteur cumulée des paragraphes précédents) + posOffset. C'est une estimation.
- "insideMargin","outsideMargin" → traiter comme "margin"

RÈGLES z-index :
- behind=true  → zIndex = 1  (derrière le texte, mais visible)
- behind=false → zIndex = 15 (devant le texte)

RÈGLES opacity :
- Image watermark ou décoration (name contient "Watermark","watermark","wm") → opacity 0.15
- Autres → opacity 1.0

RÈGLES wrapType :
- "None","Square","Through","TopAndBottom" → position:absolute (déjà géré)
- "Tight" → image en flux avec le texte ; utilise quand même position:absolute avec les coordonnées calculées (meilleure approximation possible)

RÈGLES MULTI-PAGE ET HORS-PAGE :
- Si top calculé > pageHeightPx : l'image est sur une page suivante. Ramène-la à top = top % pageHeightPx et note page=N dans le reasoning.
- Si top > pageHeightPx * 0.85 : l'image est en bas de page, laisse-la visible (ne la pousse pas hors page).
- Ne retourne JAMAIS top > pageHeightPx dans le JSON final.
- Inclus TOUTES les images ayant un rid non null, même celles difficiles à positionner.

RÉPONSE : JSON strict avec :
{
  "images": [
    {
      "rid": "rId8",
      "left": 103,
      "top": 746,
      "width": 122,
      "height": 122,
      "zIndex": 1,
      "opacity": 1.0,
      "reasoning": "image 3, page-relative, calcul direct"
    }
  ],
  "report": "Résumé de l'analyse et des décisions de positionnement prises."
}

Ne retourne QUE le JSON. N'inclus PAS les images sans rid (rid=null).
SYSTEM;
    }

    private function buildUserPrompt(array $s): string
    {
        $anchorsJson = json_encode($s['anchors'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Analyse ce document DOCX et calcule les positions CSS pour l'aperçu HTML.

DIMENSIONS PAGE :
- pageWidthPx  : {$s['pageWidthPx']}px
- pageHeightPx : {$s['pageHeightPx']}px
- marginPx     : {$s['marginPx']}px
- totalPars    : {$s['totalPars']} paragraphes dans le document
- lineSpacingPx: {$s['lineSpacingPx']}px par ligne (estimation)
- defaultFontPt: {$s['defaultFontPt']}pt

IMAGES ANCRÉES (wp:anchor) :
$anchorsJson

Pour chaque image ayant un `rid` non null, calcule left/top/width/height/zIndex/opacity.
Utilise les champs `math_left` et `math_top` comme base mathématique, et améliore-les si tu détectes des incohérences de layout (images qui se superposent inutilement, images hors page, etc.).
Explique tes décisions dans le champ `report`.
PROMPT;
    }

    // ── Fallback mathématique ─────────────────────────────────────────────────

    private function fallbackPositions(array $structure): array
    {
        $positions  = [];
        $pageH      = $structure['pageHeightPx'] ?? self::PAGE_H;
        $totalPars  = max(1, $structure['totalPars'] ?? 55);
        $lineH      = $structure['lineSpacingPx'] ?? 18.5;
        $margin     = $structure['marginPx']    ?? 48;

        foreach ($structure['anchors'] as $anchor) {
            if (!$anchor['rid']) continue;

            $relH = $anchor['posH_relFrom'];
            $relV = $anchor['posV_relFrom'];
            $offH = $anchor['posH_offsetPx'];
            $offV = $anchor['posV_offsetPx'];
            $parIdx = $anchor['parIndex'];

            // Position horizontale
            $left = match($relH) {
                'page'   => $offH,
                default  => $margin + $offH,
            };

            // Position verticale : calcul par page
            if ($relV === 'page') {
                // Relatif à la page DOCX → direct (toujours sur la même page)
                $top  = $offV;
                $page = 0;
            } elseif ($relV === 'margin') {
                $top  = $margin + $offV;
                $page = 0;
            } else {
                // paragraph / line / topMargin → estimer la position du paragraphe
                $parTopAbsolute = $margin + ($parIdx * $lineH * 1.05);
                $topAbsolute    = $parTopAbsolute + $offV;

                // Quelle page ?
                $page = (int)floor($topAbsolute / $pageH);
                // Top relatif à cette page
                $top  = (int)($topAbsolute - $page * $pageH);
            }

            $positions[] = [
                'rid'       => $anchor['rid'],
                'left'      => max(0, $left),
                'top'       => max(0, $top),
                'page'      => $page,  // page 0-based (pour injection multi-page future)
                'width'     => $anchor['widthPx'],
                'height'    => $anchor['heightPx'],
                'zIndex'    => $anchor['behind'] ? 1 : 15,
                'opacity'   => 1.0,
                'reasoning' => "Fallback EMU→px: relH=$relH offH={$offH}px relV=$relV offV={$offV}px parIdx=$parIdx page=$page",
            ];
        }

        return $positions;
    }
}
