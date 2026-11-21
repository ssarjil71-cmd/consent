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
            if (!Schema::hasColumn('consents', 'risk_acknowledged')) {
                $table->boolean('risk_acknowledged')->default(false)->after('risk_acknowledgement_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'risk_acknowledged')) {
                $table->dropColumn('risk_acknowledged');
            }
        });
    }
};
