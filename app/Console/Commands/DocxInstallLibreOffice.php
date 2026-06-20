<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DocxInstallLibreOffice extends Command
{
    protected $signature   = 'docx:install-libreoffice {--force : Réinstaller même si déjà configuré}';
    protected $description = 'Installe LibreOffice (via winget) et configure .env pour la conversion DOCX→HTML';

    /** Standard installation paths (Windows + Linux/Mac). */
    private array $standardPaths = [
        'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
        'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
        '/usr/bin/soffice',
        '/usr/lib/libreoffice/program/soffice',
        '/Applications/LibreOffice.app/Contents/MacOS/soffice',
    ];

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════');
        $this->info('  Configuration LibreOffice pour DOCX→HTML     ');
        $this->info('═══════════════════════════════════════════════');

        // 1. Check if already configured and valid
        $currentBin = env('LIBREOFFICE_BIN', 'soffice');
        if (!$this->option('force') && $this->binaryWorks($currentBin)) {
            $this->info("✓ LibreOffice déjà opérationnel : $currentBin");
            $this->activateInEnv($currentBin);
            return 0;
        }

        // 2. Search in standard locations
        $this->line('Recherche de LibreOffice sur le système…');
        foreach ($this->standardPaths as $path) {
            if (file_exists($path) && $this->binaryWorks($path)) {
                $this->info("✓ LibreOffice trouvé : $path");
                $this->activateInEnv($path);
                return 0;
            }
        }

        // 3. Try PATH
        if ($this->binaryWorks('soffice')) {
            $fullPath = trim(shell_exec(PHP_OS_FAMILY === 'Windows'
                ? 'where soffice 2>NUL'
                : 'which soffice 2>/dev/null') ?? 'soffice');
            $this->info("✓ LibreOffice dans le PATH : $fullPath");
            $this->activateInEnv($fullPath ?: 'soffice');
            return 0;
        }

        // 4. Not found — try winget (Windows)
        if (PHP_OS_FAMILY === 'Windows') {
            return $this->installViaWinget();
        }

        $this->error('LibreOffice introuvable. Installez-le manuellement :');
        $this->line('  Ubuntu/Debian : sudo apt-get install libreoffice');
        $this->line('  macOS         : brew install --cask libreoffice');
        return 1;
    }

    private function installViaWinget(): int
    {
        // Check winget is available
        exec('winget --version 2>NUL', $ver, $code);
        if ($code !== 0) {
            $this->error('winget non disponible. Installez LibreOffice manuellement depuis libreoffice.org');
            return 1;
        }

        $this->info('LibreOffice non trouvé. Installation via winget…');
        $this->line('(Téléchargement ~350 Mo, veuillez patienter…)');

        $cmd = 'winget install --id TheDocumentFoundation.LibreOffice --accept-source-agreements --accept-package-agreements 2>&1';
        $proc = popen($cmd, 'r');
        if ($proc) {
            while (!feof($proc)) {
                $line = fgets($proc, 512);
                if ($line !== false && trim($line) !== '') {
                    $this->line('  ' . rtrim($line));
                }
            }
            pclose($proc);
        }

        // After install, look again in standard paths
        foreach ($this->standardPaths as $path) {
            if (file_exists($path) && $this->binaryWorks($path)) {
                $this->info("✓ LibreOffice installé : $path");
                $this->activateInEnv($path);
                return 0;
            }
        }

        // winget may have installed a new version in a versioned subdirectory
        $pattern = 'C:\\Program Files\\LibreOffice*\\program\\soffice.exe';
        $found   = glob($pattern);
        if (!empty($found) && file_exists($found[0])) {
            $path = $found[0];
            $this->info("✓ LibreOffice installé : $path");
            $this->activateInEnv($path);
            return 0;
        }

        $this->warn('Installation winget terminée mais chemin introuvable.');
        $this->warn('Relancez la commande ou définissez LIBREOFFICE_BIN manuellement dans .env');
        return 1;
    }

    /** Test the binary works by running --version. */
    private function binaryWorks(string $bin): bool
    {
        if ($bin !== 'soffice' && !file_exists($bin)) return false;
        $quoted = (PHP_OS_FAMILY === 'Windows') ? '"' . $bin . '"' : $bin;
        exec($quoted . ' --version 2>&1', $out, $code);
        return $code === 0 && !empty($out);
    }

    /** Write DOCX_RENDERER=libreoffice and LIBREOFFICE_BIN to .env. */
    private function activateInEnv(string $bin): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            $this->warn('.env introuvable — mettez à jour LIBREOFFICE_BIN manuellement.');
            return;
        }

        $content = file_get_contents($envPath);

        // Escape backslashes for Windows paths
        $escaped = str_replace('\\', '\\\\', $bin);

        // DOCX_RENDERER
        if (str_contains($content, 'DOCX_RENDERER=')) {
            $content = preg_replace('/^DOCX_RENDERER=.*$/m', 'DOCX_RENDERER=libreoffice', $content) ?? $content;
        } else {
            $content .= "\nDOCX_RENDERER=libreoffice\n";
        }

        // LIBREOFFICE_BIN
        if (str_contains($content, 'LIBREOFFICE_BIN=')) {
            $content = preg_replace('/^LIBREOFFICE_BIN=.*$/m', "LIBREOFFICE_BIN=$escaped", $content) ?? $content;
        } else {
            $content .= "LIBREOFFICE_BIN=$escaped\n";
        }

        file_put_contents($envPath, $content);

        $this->info("✓ .env mis à jour : DOCX_RENDERER=libreoffice, LIBREOFFICE_BIN=$bin");
        $this->line('  Exécutez : php artisan config:clear');

        // Auto-clear config cache
        $this->call('config:clear');
        $this->info('✓ Cache de configuration vidé.');
    }
}
