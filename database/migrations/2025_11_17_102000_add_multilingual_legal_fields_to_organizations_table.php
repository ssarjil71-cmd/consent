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
            if (!Schema::hasColumn('organizations', 'terms_text_hi')) {
                $table->longText('terms_text_hi')->nullable()->after('terms_text');
            }

            if (!Schema::hasColumn('organizations', 'terms_text_mr')) {
                $table->longText('terms_text_mr')->nullable()->after('terms_text_hi');
            }

            if (!Schema::hasColumn('organizations', 'liability_release_text_hi')) {
                $table->longText('liability_release_text_hi')->nullable()->after('liability_release_text');
            }

            if (!Schema::hasColumn('organizations', 'liability_release_text_mr')) {
                $table->longText('liability_release_text_mr')->nullable()->after('liability_release_text_hi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'liability_release_text_mr')) {
                $table->dropColumn('liability_release_text_mr');
            }

            if (Schema::hasColumn('organizations', 'liability_release_text_hi')) {
                $table->dropColumn('liability_release_text_hi');
            }

            if (Schema::hasColumn('organizations', 'terms_text_mr')) {
                $table->dropColumn('terms_text_mr');
            }

            if (Schema::hasColumn('organizations', 'terms_text_hi')) {
                $table->dropColumn('terms_text_hi');
            }
        });
    }
};
