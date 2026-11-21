<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'witness_signature_path')) {
                $table->string('witness_signature_path')->nullable()->after('thumb_upload_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'witness_signature_path')) {
                $table->dropColumn('witness_signature_path');
            }
        });
    }
};
