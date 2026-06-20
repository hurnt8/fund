<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->string('watermark_path')->nullable()->after('detected_tags');
            $table->string('logo_left_path')->nullable()->after('watermark_path');
            $table->string('logo_right_path')->nullable()->after('logo_left_path');
            $table->string('stamp_path')->nullable()->after('logo_right_path');
            $table->string('signature_admin_path')->nullable()->after('stamp_path');
            $table->string('signature_agent_path')->nullable()->after('signature_admin_path');
        });
    }

    public function down(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->dropColumn([
                'watermark_path', 'logo_left_path', 'logo_right_path',
                'stamp_path', 'signature_admin_path', 'signature_agent_path',
            ]);
        });
    }
};
