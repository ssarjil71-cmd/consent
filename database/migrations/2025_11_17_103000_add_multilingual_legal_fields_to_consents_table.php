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
            if (!Schema::hasColumn('consents', 'terms_text_en')) {
                $table->text('terms_text_en')->nullable()->after('terms_text');
            }

            if (!Schema::hasColumn('consents', 'terms_text_hi')) {
                $table->text('terms_text_hi')->nullable()->after('terms_text_en');
            }

            if (!Schema::hasColumn('consents', 'terms_text_mr')) {
                $table->text('terms_text_mr')->nullable()->after('terms_text_hi');
            }

            if (!Schema::hasColumn('consents', 'liability_release_text_en')) {
                $table->text('liability_release_text_en')->nullable()->after('liability_release_text');
            }

            if (!Schema::hasColumn('consents', 'liability_release_text_hi')) {
                $table->text('liability_release_text_hi')->nullable()->after('liability_release_text_en');
            }

            if (!Schema::hasColumn('consents', 'liability_release_text_mr')) {
                $table->text('liability_release_text_mr')->nullable()->after('liability_release_text_hi');
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
                'liability_release_text_mr',
                'liability_release_text_hi',
                'liability_release_text_en',
                'terms_text_mr',
                'terms_text_hi',
                'terms_text_en',
            ] as $column) {
                if (Schema::hasColumn('consents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
