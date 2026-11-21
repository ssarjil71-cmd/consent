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
            if (!Schema::hasColumn('organizations', 'risk_acknowledgement_text')) {
                $table->longText('risk_acknowledgement_text')->nullable()->after('liability_release_text_mr');
            }

            if (!Schema::hasColumn('organizations', 'risk_acknowledgement_text_hi')) {
                $table->longText('risk_acknowledgement_text_hi')->nullable()->after('risk_acknowledgement_text');
            }

            if (!Schema::hasColumn('organizations', 'risk_acknowledgement_text_mr')) {
                $table->longText('risk_acknowledgement_text_mr')->nullable()->after('risk_acknowledgement_text_hi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'risk_acknowledgement_text_mr')) {
                $table->dropColumn('risk_acknowledgement_text_mr');
            }

            if (Schema::hasColumn('organizations', 'risk_acknowledgement_text_hi')) {
                $table->dropColumn('risk_acknowledgement_text_hi');
            }

            if (Schema::hasColumn('organizations', 'risk_acknowledgement_text')) {
                $table->dropColumn('risk_acknowledgement_text');
            }
        });
    }
};
