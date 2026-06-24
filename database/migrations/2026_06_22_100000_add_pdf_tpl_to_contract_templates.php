<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->string('pdf_tpl_path')->nullable()->after('docx_path');
            $table->json('pdf_positions')->nullable()->after('pdf_tpl_path');
        });
    }

    public function down(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->dropColumn(['pdf_tpl_path', 'pdf_positions']);
        });
    }
};
