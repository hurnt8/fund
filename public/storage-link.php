<?php
/*
 * Crée le symlink public/storage → storage/app/public
 * À SUPPRIMER du serveur après utilisation
 * Accès : https://credixa.eu/storage-link.php
 */
header('Content-Type: text/plain; charset=utf-8');

$publicDir  = __DIR__;                              // /htdocs/max/public
$target     = realpath($publicDir . '/../storage/app/public');
$link       = $publicDir . '/storage';

echo "=== STORAGE LINK ===\n";
echo "Public dir : $publicDir\n";
echo "Target     : $target\n";
echo "Link path  : $link\n\n";

if (!$target || !is_dir($target)) {
    echo "ERREUR : storage/app/public introuvable à $target\n";
    exit;
}

if (is_link($link)) {
    echo "Symlink existe déjà → " . readlink($link) . "\n";
    echo "Test fichier : " . (is_dir($link) ? "OK ✓" : "BROKEN ✗") . "\n";
} elseif (is_dir($link)) {
    echo "ERREUR : public/storage est un dossier réel (pas un symlink)\n";
    echo "Supprimez-le manuellement via FTP et relancez ce script.\n";
} else {
    if (@symlink($target, $link)) {
        echo "✓ Symlink créé avec succès !\n";
        echo "Vérification : " . (is_dir($link) ? "OK ✓" : "FAILED ✗") . "\n";
    } else {
        echo "✗ symlink() échoué — votre hébergeur bloque les symlinks PHP.\n\n";
        echo "=== SOLUTION ALTERNATIVE ===\n";
        echo "Copiez le contenu de storage/app/public/ dans public/storage/ via FTP.\n";
        echo "Ensuite décommentez dans config/filesystems.php :\n";
        echo "   'root' => public_path('storage'),\n";
    }
}
