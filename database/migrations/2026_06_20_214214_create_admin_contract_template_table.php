<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_contract_template', function (Blueprint $table) {
            $table->foreignId('admin_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('contract_template_id')
                  ->constrained('contract_templates')
                  ->cascadeOnDelete();
            $table->primary(['admin_id', 'contract_template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_contract_template');
    }
};
