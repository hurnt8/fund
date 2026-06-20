<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->string('template_type', 10)->default('html')->after('is_default');
            $table->string('locale', 5)->nullable()->after('template_type');
            $table->string('docx_path')->nullable()->after('locale');
            $table->json('detected_tags')->nullable()->after('docx_path');
        });
    }

    public function down(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->dropColumn(['template_type', 'locale', 'docx_path', 'detected_tags']);
        });
    }
};
