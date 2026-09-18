<?php
/*
 * Script de diagnostic — À SUPPRIMER après vérification
 * Accès : https://aurenzacapital.com/diag.php
 */
header('Content-Type: text/plain; charset=utf-8');

$checks = [
    'build/manifest.json',
    'build/assets/client-app-76849fea.css',
    'build/assets/client-app-6c55cfee.js',
    'build/assets/chart-ad2aba2b.js',
    'build/assets/app-64bc92c3.js',
    'images/icon-192.svg',
    'images/icon-512.svg',
    'sw.js',
];

echo "=== DIAGNOSTIC AURENZA CAPITAL ===\n";
echo "DOCUMENT_ROOT  : " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "__DIR__        : " . __DIR__ . "\n\n";

echo "--- Fichiers dans " . __DIR__ . "/ ---\n";
foreach ($checks as $file) {
    $path   = __DIR__ . '/' . $file;
    $exists = file_exists($path) ? 'OK  ✓' : 'MANQUANT ✗';
    echo sprintf("%-15s %s\n", $exists, $path);
}
