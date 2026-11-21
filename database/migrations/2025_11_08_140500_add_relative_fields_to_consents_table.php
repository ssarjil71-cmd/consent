<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'consent_for')) {
                $table->enum('consent_for', ['self', 'relative'])->default('self')->after('lang');
            }
            if (!Schema::hasColumn('consents', 'relative_name')) {
                $table->string('relative_name', 255)->nullable()->after('consent_for');
            }
            if (!Schema::hasColumn('consents', 'relative_relation')) {
                $table->string('relative_relation', 255)->nullable()->after('relative_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'relative_relation')) {
                $table->dropColumn('relative_relation');
            }
            if (Schema::hasColumn('consents', 'relative_name')) {
                $table->dropColumn('relative_name');
            }
            if (Schema::hasColumn('consents', 'consent_for')) {
                $table->dropColumn('consent_for');
            }
        });
    }
};
