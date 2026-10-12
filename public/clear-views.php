<?php
// Script jetable : vide le cache des vues Blade + opcache, puis se supprime.
header('Content-Type: text/plain; charset=utf-8');
$root = dirname(__DIR__);

$deleted = 0;
foreach (glob($root . '/storage/framework/views/*.php') ?: [] as $f) {
    if (@unlink($f)) { $deleted++; }
}
echo "Vues compilees supprimees : $deleted\n";

if (function_exists('opcache_reset')) {
    echo 'opcache_reset : ' . (opcache_reset() ? 'ok' : 'echec') . "\n";
}

echo "\nFichiers de layout sur le serveur :\n";
foreach (['client-shell', 'client-app'] as $n) {
    $p = "$root/resources/views/layouts/$n.blade.php";
    if (is_file($p)) {
        $s = file_get_contents($p);
        printf("- %s : %d lignes, md5=%s, @if/@hasSection=%d, @endif=%d\n", $n,
            substr_count($s, "\n") + 1, md5($s),
            preg_match_all('/@(if|hasSection)\b/', $s), preg_match_all('/@endif\b/', $s));
    } else {
        echo "- $n : ABSENT\n";
    }
}
$p = "$root/resources/views/client/app/home.blade.php";
echo "\nhome.blade.php : " . (is_file($p) ? (strpos(file_get_contents($p), "layouts.client-shell") !== false ? 'utilise client-shell (OK)' : 'utilise encore client-app (ANCIEN)') : 'absent') . "\n";

@unlink(__FILE__);
echo "\nCe script s'est supprime. Rechargez /app.\n";
