<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('contract_templates', 'docx_path')) {
                $columns[] = 'docx_path';
            }
            if (Schema::hasColumn('contract_templates', 'detected_tags')) {
                $columns[] = 'detected_tags';
            }
            if (Schema::hasColumn('contract_templates', 'pdf_tpl_path')) {
                $columns[] = 'pdf_tpl_path';
            }
            if (Schema::hasColumn('contract_templates', 'pdf_positions')) {
                $columns[] = 'pdf_positions';
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });

        // Force template_type to 'html' for all existing rows
        \DB::table('contract_templates')->update(['template_type' => 'html']);
    }

    public function down(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->string('docx_path')->nullable();
            $table->json('detected_tags')->nullable();
            $table->string('pdf_tpl_path')->nullable();
            $table->json('pdf_positions')->nullable();
        });
    }
};
