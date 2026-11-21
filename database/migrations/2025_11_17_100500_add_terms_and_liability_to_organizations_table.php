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
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'terms_text')) {
                $table->longText('terms_text')->nullable()->after('consent_label');
            }

            if (!Schema::hasColumn('organizations', 'liability_release_text')) {
                $table->longText('liability_release_text')->nullable()->after('terms_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'liability_release_text')) {
                $table->dropColumn('liability_release_text');
            }

            if (Schema::hasColumn('organizations', 'terms_text')) {
                $table->dropColumn('terms_text');
            }
        });
    }
};
