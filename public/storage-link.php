<?php
/*
 * Équivalent de : php artisan storage:link
 * À SUPPRIMER après utilisation
 * Accès : https://credixa.eu/storage-link.php
 */
header('Content-Type: text/plain; charset=utf-8');

// public/storage  →  storage/app/public
$link   = __DIR__ . '/storage';
$target = realpath(__DIR__ . '/../storage/app/public');

echo "=== php artisan storage:link (équivalent) ===\n\n";
echo "Lien    : $link\n";
echo "Cible   : " . (__DIR__ . '/../storage/app/public') . "\n\n";

/* 1. Créer storage/app/public/ si absent */
$targetPath = __DIR__ . '/../storage/app/public';
if (!is_dir($targetPath)) {
    if (mkdir($targetPath, 0755, true)) {
        echo "✓ Dossier storage/app/public/ créé\n";
    } else {
        echo "✗ Impossible de créer storage/app/public/\n";
        exit;
    }
}
$target = realpath($targetPath);
echo "Cible résolue : $target\n\n";

/* 2. Supprimer l'ancien lien/dossier public/storage si existant */
if (is_link($link)) {
    unlink($link);
    echo "✓ Ancien symlink supprimé\n";
} elseif (is_dir($link)) {
    echo "⚠ public/storage est un vrai dossier (pas un symlink).\n";
    echo "  Supprimez-le manuellement via cPanel puis relancez.\n";
    exit;
}

/* 3. Créer le symlink */
if (symlink($target, $link)) {
    echo "✓ Symlink créé : public/storage → storage/app/public\n\n";
    echo "=== VÉRIFICATION ===\n";
    echo "is_link  : " . (is_link($link)  ? 'OUI ✓' : 'NON ✗') . "\n";
    echo "is_dir   : " . (is_dir($link)   ? 'OUI ✓' : 'NON ✗') . "\n";
    echo "readlink : " . readlink($link) . "\n\n";
    echo "✓ Succès — supprimez ce fichier maintenant.\n";
} else {
    echo "✗ symlink() a échoué.\n";
    echo "  Votre hébergeur bloque peut-être symlink() en PHP.\n\n";
    echo "=== SOLUTION ALTERNATIVE ===\n";
    echo "Dans cPanel, créez un vrai dossier : public/storage/\n";
    echo "Puis modifiez config/filesystems.php :\n";
    echo "  'root' => public_path('storage'),\n";
}
