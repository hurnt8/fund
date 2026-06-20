<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('address');
            $table->string('currency', 10)->default('EUR')->after('birth_date');
            $table->string('locale', 5)->default('fr')->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address', 'birth_date', 'currency', 'locale']);
        });
    }
};
