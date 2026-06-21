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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'bank_account')) {
                $table->string('bank_account')->nullable()->after('balance');
            }
            if (! Schema::hasColumn('users', 'bic')) {
                $table->string('bic', 20)->nullable()->after('bank_account');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_account', 'bic']);
        });
    }
};
