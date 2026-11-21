<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'thumb_upload_path')) {
                $table->string('thumb_upload_path')->nullable()->after('signature_upload_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'thumb_upload_path')) {
                $table->dropColumn('thumb_upload_path');
            }
        });
    }
};
