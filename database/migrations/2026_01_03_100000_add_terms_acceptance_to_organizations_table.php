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
            if (!Schema::hasColumn('organizations', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('risk_acknowledgement_text_mr');
            }
            
            if (!Schema::hasColumn('organizations', 'liability_accepted_at')) {
                $table->timestamp('liability_accepted_at')->nullable()->after('terms_accepted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'liability_accepted_at')) {
                $table->dropColumn('liability_accepted_at');
            }

            if (Schema::hasColumn('organizations', 'terms_accepted_at')) {
                $table->dropColumn('terms_accepted_at');
            }
        });
    }
};
