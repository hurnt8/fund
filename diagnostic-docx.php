<?php
/*
 * DIAGNOSTIC — upload des templates DOCX
 * -------------------------------------------------------------------------
 * À déposer temporairement dans public_html/ puis à SUPPRIMER aussitôt après.
 *
 *   https://aurenzacapital.com/diagnostic-docx.php?k=VOTRE_JETON
 *
 * Remplacez VOTRE_JETON ci-dessous par une valeur de votre choix : sans le bon
 * jeton la page ne renvoie rien, pour éviter d'exposer vos chemins serveur.
 *
 * Le script ne modifie rien, hormis un fichier de test qu'il supprime lui-même.
 */

const JETON = 'CHANGEZ_MOI';

if (($_GET['k'] ?? '') !== JETON) {
    http_response_code(404);
    exit;
}

header('Content-Type: text/plain; charset=utf-8');

function ligne(string $label, $valeur): void
{
    // str_pad compte les octets : on aligne sur le nombre de caractères
    // pour que les libellés accentués restent bien en colonne.
    $l = $label . ' :';
    echo $l . str_repeat(' ', max(1, 34 - mb_strlen($l))) . $valeur . "\n";
}

function ouinon(bool $b): string
{
    return $b ? 'OUI' : 'NON  <<<';
}

echo "=== DIAGNOSTIC UPLOAD DOCX ===\n\n";

/* ─── 1. Extension ZIP ────────────────────────────────────────────────────── */
echo "--- 1. Extension ZIP ---\n";
ligne('extension zip chargée', ouinon(extension_loaded('zip')));
ligne('classe ZipArchive', ouinon(class_exists('ZipArchive')));
echo "\n";

/* ─── 2. Limites PHP d'upload ─────────────────────────────────────────────── */
echo "--- 2. Limites PHP ---\n";
ligne('upload_max_filesize', ini_get('upload_max_filesize'));
ligne('post_max_size',       ini_get('post_max_size'));
ligne('memory_limit',        ini_get('memory_limit'));
ligne('file_uploads',        ouinon((bool) ini_get('file_uploads')));
ligne('upload_tmp_dir',      ini_get('upload_tmp_dir') ?: '(défaut système : ' . sys_get_temp_dir() . ')');
ligne('open_basedir',        ini_get('open_basedir') ?: '(aucune restriction)');
echo "\n";

/* ─── 3. Chemins Laravel ──────────────────────────────────────────────────── */
echo "--- 3. Chemins ---\n";

$autoload = __DIR__ . '/vendor/autoload.php';
$bootstrap = __DIR__ . '/bootstrap/app.php';
$racineDisque = null;

if (is_file($autoload) && is_file($bootstrap)) {
    require $autoload;
    $app = require $bootstrap;
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    ligne('base_path()',     base_path());
    ligne('storage_path()',  storage_path());
    ligne('public_path()',   public_path());
    $racineDisque = Illuminate\Support\Facades\Storage::disk('local')->path('');
    ligne("racine du disque 'local'", $racineDisque);
    ligne('correspond à storage/app', ouinon(
        rtrim(str_replace('\\', '/', $racineDisque), '/') === rtrim(str_replace('\\', '/', storage_path('app')), '/')
    ));
} else {
    echo "Laravel introuvable depuis ce dossier.\n";
    echo "Déposez ce fichier à la racine du projet (à côté de vendor/), ou ajustez les chemins.\n";
    ligne('dossier du script', __DIR__);
    $racineDisque = __DIR__ . '/storage/app/';
}
echo "\n";

/* ─── 4. Dossiers et droits ───────────────────────────────────────────────── */
echo "--- 4. Dossiers et droits ---\n";

$cible = rtrim(str_replace('\\', '/', $racineDisque), '/') . '/docx-templates';

foreach ([dirname(dirname($cible)), dirname($cible), $cible] as $d) {
    $existe = is_dir($d);
    printf(
        "%-52s existe:%-5s écriture:%-9s droits:%s\n",
        $d,
        $existe ? 'OUI' : 'NON',
        $existe ? ($existe && is_writable($d) ? 'OUI' : 'NON  <<<') : '-',
        $existe ? substr(sprintf('%o', fileperms($d)), -4) : '-'
    );
}
echo "\n";

/* ─── 5. Test d'écriture réel ─────────────────────────────────────────────── */
echo "--- 5. Test d'écriture + relecture ZIP ---\n";

if (!is_dir($cible)) {
    if (@mkdir($cible, 0755, true)) {
        echo "Dossier docx-templates/ créé.\n";
    } else {
        echo "ÉCHEC de création du dossier docx-templates/  <<< cause probable\n";
    }
}

if (is_dir($cible)) {
    $test = $cible . '/_diagnostic_' . time() . '.docx';

    // On fabrique un vrai petit ZIP pour reproduire le parcours complet.
    $zip = new ZipArchive();
    $ouvert = $zip->open($test, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    if ($ouvert === true) {
        $zip->addFromString('word/document.xml', '<?xml version="1.0"?><w:document>{test}</w:document>');
        $zip->close();

        ligne('écriture du ZIP', ouinon(is_file($test)));
        ligne('taille obtenue', is_file($test) ? filesize($test) . ' octets' : '-');

        $relecture = new ZipArchive();
        $code = $relecture->open($test);
        if ($code === true) {
            ligne('relecture du ZIP', 'OUI');
            ligne('word/document.xml présent', ouinon($relecture->locateName('word/document.xml') !== false));
            $relecture->close();
            echo "\n>>> La chaîne écriture + relecture fonctionne.\n";
            echo ">>> Le blocage vient donc d'ailleurs : relancez l'upload, le message\n";
            echo ">>> d'erreur indique maintenant la cause précise.\n";
        } else {
            ligne('relecture du ZIP', "NON — code $code  <<< cause probable");
        }

        @unlink($test);
        ligne('fichier de test supprimé', ouinon(!is_file($test)));
    } else {
        ligne('création du ZIP de test', "ÉCHEC — code $ouvert  <<< cause probable");
        echo "Le dossier n'est pas accessible en écriture par PHP.\n";
    }
}
echo "\n";

/* ─── 6. Espace disque ────────────────────────────────────────────────────── */
echo "--- 6. Espace disque ---\n";
$libre = @disk_free_space(dirname($cible));
ligne('espace libre', $libre === false ? 'indisponible' : round($libre / 1048576, 1) . ' Mo');
echo "\n";

/* ─── 7. Exposition web ───────────────────────────────────────────────────── */
echo "--- 7. Exposition web ---\n";

$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
ligne('DOCUMENT_ROOT', $docRoot ?: '(inconnu)');

if ($docRoot !== '' && function_exists('base_path')) {
    $base = rtrim(str_replace('\\', '/', base_path()), '/');
    ligne('base_path()', $base);

    if ($base === $docRoot || str_starts_with($base . '/', $docRoot . '/')) {
        echo "\n";
        echo "ATTENTION : l'application est à l'intérieur du dossier web.\n";
        echo "  .env, storage/ et vendor/ risquent d'être accessibles depuis Internet.\n";
        echo "  Vérifiez ces URL — elles doivent toutes renvoyer 403 ou 404 :\n";
        foreach (['/.env', '/storage/logs/laravel.log', '/composer.json'] as $u) {
            echo "    https://" . ($_SERVER['HTTP_HOST'] ?? 'votre-domaine') . $u . "\n";
        }
    } else {
        ligne('application hors du dossier web', 'OUI');
    }
}
echo "\n";

echo "=== FIN — supprimez ce fichier du serveur ===\n";
