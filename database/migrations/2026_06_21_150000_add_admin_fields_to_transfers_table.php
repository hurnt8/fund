<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->after('user_id');
            $table->string('admin_note')->nullable()->after('note');
            $table->unsignedBigInteger('invoice_id')->nullable()->after('admin_note');
            $table->foreign('admin_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
        });

        // Extend the status enum to include fee_required
        DB::statement("ALTER TABLE transfers MODIFY COLUMN status ENUM('pending','completed','rejected','fee_required') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transfers MODIFY COLUMN status ENUM('pending','completed','rejected') NOT NULL DEFAULT 'pending'");

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropForeign(['invoice_id']);
            $table->dropColumn(['admin_id', 'admin_note', 'invoice_id']);
        });
    }
};
