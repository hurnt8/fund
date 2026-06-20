<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rendre subject nullable (était NOT NULL → violation contrainte à la création)
        DB::statement('ALTER TABLE loan_requests MODIFY COLUMN subject TEXT NULL');

        // Rendre objet nullable aussi (même problème potentiel)
        DB::statement('ALTER TABLE loan_requests MODIFY COLUMN objet VARCHAR(255) NULL');

        // Ajouter extra_fields pour les balises personnalisées du template
        if (!Schema::hasColumn('loan_requests', 'extra_fields')) {
            Schema::table('loan_requests', function (Blueprint $table) {
                $table->json('extra_fields')->nullable()->after('subject');
            });
        }
    }

    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            if (Schema::hasColumn('loan_requests', 'extra_fields')) {
                $table->dropColumn('extra_fields');
            }
        });
    }
};
