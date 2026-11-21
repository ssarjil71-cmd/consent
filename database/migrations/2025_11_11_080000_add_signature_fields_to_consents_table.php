<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'signature_method')) {
                $table->enum('signature_method', ['note', 'upload'])->default('note')->after('notes');
            }

            if (!Schema::hasColumn('consents', 'signature_note')) {
                $table->text('signature_note')->nullable()->after('signature_method');
            }

            if (!Schema::hasColumn('consents', 'signature_upload_path')) {
                $table->string('signature_upload_path')->nullable()->after('signature_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'signature_upload_path')) {
                $table->dropColumn('signature_upload_path');
            }

            if (Schema::hasColumn('consents', 'signature_note')) {
                $table->dropColumn('signature_note');
            }

            if (Schema::hasColumn('consents', 'signature_method')) {
                $table->dropColumn('signature_method');
            }
        });
    }
};
