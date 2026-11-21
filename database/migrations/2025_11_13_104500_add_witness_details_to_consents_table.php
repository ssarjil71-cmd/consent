<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'witness_name')) {
                $table->string('witness_name')->nullable()->after('signature_method');
            }

            if (!Schema::hasColumn('consents', 'witness_phone')) {
                $table->string('witness_phone', 20)->nullable()->after('witness_name');
            }

            if (!Schema::hasColumn('consents', 'witness_email')) {
                $table->string('witness_email')->nullable()->after('witness_phone');
            }

            if (!Schema::hasColumn('consents', 'witness_relation')) {
                $table->string('witness_relation')->nullable()->after('witness_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'witness_relation')) {
                $table->dropColumn('witness_relation');
            }

            if (Schema::hasColumn('consents', 'witness_email')) {
                $table->dropColumn('witness_email');
            }

            if (Schema::hasColumn('consents', 'witness_phone')) {
                $table->dropColumn('witness_phone');
            }

            if (Schema::hasColumn('consents', 'witness_name')) {
                $table->dropColumn('witness_name');
            }
        });
    }
};
