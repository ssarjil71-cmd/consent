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
            if (!Schema::hasColumn('consents', 'liability_release_text')) {
                $table->text('liability_release_text')->nullable()->after('terms_text');
            }

            if (!Schema::hasColumn('consents', 'liability_release_acknowledged')) {
                $table->boolean('liability_release_acknowledged')->default(false)->after('liability_release_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'liability_release_acknowledged')) {
                $table->dropColumn('liability_release_acknowledged');
            }

            if (Schema::hasColumn('consents', 'liability_release_text')) {
                $table->dropColumn('liability_release_text');
            }
        });
    }
};
