<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Le tableau d'amortissement était généré puis supprimé aussitôt après l'envoi.
 * On le conserve désormais, comme le contrat et l'attestation : la pièce jointe
 * devient fiable et le document reste téléchargeable depuis le dossier.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->string('amortization_pdf_path')->nullable()->after('insurance_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropColumn('amortization_pdf_path');
        });
    }
};
