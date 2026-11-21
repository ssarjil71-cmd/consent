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
        Schema::table('organization_types', function (Blueprint $table) {
            $table->longText('terms_text')->nullable()->after('description');
            $table->longText('liability_release_text')->nullable()->after('terms_text');
            $table->longText('risk_acknowledgement_text')->nullable()->after('liability_release_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organization_types', function (Blueprint $table) {
            $table->dropColumn([
                'terms_text',
                'liability_release_text',
                'risk_acknowledgement_text'
            ]);
        });
    }
};
