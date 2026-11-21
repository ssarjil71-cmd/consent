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
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'risk_acknowledgement_text')) {
                $table->text('risk_acknowledgement_text')->nullable()->after('risk_consent_text_mr');
            }

            if (!Schema::hasColumn('consents', 'risk_acknowledgement_text_en')) {
                $table->text('risk_acknowledgement_text_en')->nullable()->after('risk_acknowledgement_text');
            }

            if (!Schema::hasColumn('consents', 'risk_acknowledgement_text_hi')) {
                $table->text('risk_acknowledgement_text_hi')->nullable()->after('risk_acknowledgement_text_en');
            }

            if (!Schema::hasColumn('consents', 'risk_acknowledgement_text_mr')) {
                $table->text('risk_acknowledgement_text_mr')->nullable()->after('risk_acknowledgement_text_hi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            foreach ([
                'risk_acknowledgement_text_mr',
                'risk_acknowledgement_text_hi',
                'risk_acknowledgement_text_en',
                'risk_acknowledgement_text',
            ] as $column) {
                if (Schema::hasColumn('consents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
