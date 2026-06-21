<?php
/*
 * Migration storage → public/storage (sans symlink)
 * À SUPPRIMER après utilisation
 * Accès : https://credixa.eu/storage-link.php
 */
header('Content-Type: text/plain; charset=utf-8');

$publicStorage = __DIR__ . '/storage';          // public/storage/
$oldStorage    = __DIR__ . '/../storage/app/public'; // storage/app/public/

echo "=== CREDIXA STORAGE SETUP ===\n";
echo "public/storage : $publicStorage\n";
echo "old storage    : $oldStorage\n\n";

/* 1. Supprimer le symlink cassé s'il existe */
if (is_link($publicStorage)) {
    $target = readlink($publicStorage);
    echo "Symlink trouvé → $target\n";
    if (!is_dir($publicStorage)) {
        unlink($publicStorage);
        echo "✓ Symlink cassé supprimé\n";
    } else {
        echo "Symlink fonctionnel détecté (pas de changement nécessaire)\n";
    }
}

/* 2. Créer public/storage/ comme vrai dossier */
if (!file_exists($publicStorage)) {
    mkdir($publicStorage, 0755, true);
    echo "✓ Dossier public/storage/ créé\n";
} elseif (is_dir($publicStorage) && !is_link($publicStorage)) {
    echo "✓ Dossier public/storage/ existe déjà\n";
}

/* 3. Migrer les fichiers depuis storage/app/public/ si présents */
if (is_dir($oldStorage)) {
    echo "\n--- Migration storage/app/public/ → public/storage/ ---\n";
    $migrated = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($oldStorage, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        $relPath = substr($item->getPathname(), strlen($oldStorage));
        $dest    = $publicStorage . $relPath;
        if ($item->isDir()) {
            if (!is_dir($dest)) { mkdir($dest, 0755, true); }
        } else {
            if (!file_exists($dest)) {
                copy($item->getPathname(), $dest);
                $migrated++;
            }
        }
    }
    echo "✓ $migrated fichier(s) migré(s)\n";
} else {
    echo "\nAucun ancien storage à migrer\n";
}

/* 4. Vérification finale */
echo "\n=== RÉSULTAT ===\n";
$dirs = glob($publicStorage . '/*', GLOB_ONLYDIR);
echo "Sous-dossiers dans public/storage/ : " . count($dirs) . "\n";
foreach ($dirs as $d) {
    $count = count(glob($d . '/*'));
    echo "  " . basename($d) . "/ → $count fichier(s)\n";
}
echo "\nSTATUS : " . (is_dir($publicStorage) ? "OK ✓ - Supprimez ce fichier maintenant" : "ERREUR ✗") . "\n";
