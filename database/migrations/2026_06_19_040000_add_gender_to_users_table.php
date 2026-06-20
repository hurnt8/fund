<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // M = Monsieur / Mr. / Sr. / Pan
            // F = Madame   / Ms. / Sra./ Pani
            // N = Non précisé (neutre)
            $table->enum('gender', ['M', 'F', 'N'])->default('N')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
