<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('insurance_template_id')
                  ->nullable()
                  ->after('contract_template_id');

            $table->foreign('insurance_template_id')
                  ->references('id')
                  ->on('contract_templates')
                  ->nullOnDelete();

            $table->decimal('frais_assurance', 12, 2)
                  ->nullable()
                  ->after('admin_fees');

            $table->date('date_fin_assurance')
                  ->nullable()
                  ->after('frais_assurance');
        });
    }

    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropForeign(['insurance_template_id']);
            $table->dropColumn(['insurance_template_id', 'frais_assurance', 'date_fin_assurance']);
        });
    }
};
