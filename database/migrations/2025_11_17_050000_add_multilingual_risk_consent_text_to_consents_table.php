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
            $table->text('risk_consent_text_en')->nullable()->after('risk_consent_text');
            $table->text('risk_consent_text_hi')->nullable()->after('risk_consent_text_en');
            $table->text('risk_consent_text_mr')->nullable()->after('risk_consent_text_hi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            $table->dropColumn([
                'risk_consent_text_en',
                'risk_consent_text_hi',
                'risk_consent_text_mr',
            ]);
        });
    }
};
