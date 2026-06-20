<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            // Références
            $table->string('reference')->unique()->nullable()->after('id');
            $table->string('archive_ref')->nullable()->after('reference');

            // Liaisons
            $table->foreignId('admin_id')->nullable()->after('archive_ref')
                  ->constrained('users')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->after('admin_id')
                  ->constrained('users')->nullOnDelete();
            $table->foreignId('contract_template_id')->nullable()->after('client_id')
                  ->constrained('contract_templates')->nullOnDelete();

            // Financier
            $table->decimal('interest_rate', 5, 2)->default(5.00)->after('amount');
            $table->string('currency', 10)->default('EUR')->after('interest_rate');
            $table->date('start_date')->nullable()->after('currency');
            $table->decimal('monthly_payment', 15, 2)->nullable()->after('start_date');
            $table->decimal('total_cost', 15, 2)->nullable()->after('monthly_payment');
            $table->decimal('total_with_interest', 15, 2)->nullable()->after('total_cost');
            $table->decimal('admin_fees', 15, 2)->nullable()->after('total_with_interest');
            $table->string('bank_account')->nullable()->after('admin_fees');

            // Conditions
            $table->text('special_conditions')->nullable()->after('bank_account');

            // Contrat
            $table->longText('contract_content')->nullable()->after('special_conditions');
            $table->string('contract_language', 5)->default('fr')->after('contract_content');
            $table->json('amortization_schedule')->nullable()->after('contract_language');

            // Suivi
            $table->text('notes')->nullable()->after('amortization_schedule');
            $table->timestamp('validated_at')->nullable()->after('notes');
            $table->timestamp('sent_at')->nullable()->after('validated_at');
            $table->timestamp('signed_received_at')->nullable()->after('sent_at');
        });

        // Modifier la colonne status en enum étendu
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['contract_template_id']);
            $table->dropColumn([
                'reference','archive_ref','admin_id','client_id','contract_template_id',
                'interest_rate','currency','start_date','monthly_payment','total_cost',
                'total_with_interest','admin_fees','bank_account','special_conditions',
                'contract_content','contract_language','amortization_schedule',
                'notes','validated_at','sent_at','signed_received_at',
            ]);
        });
    }
};
