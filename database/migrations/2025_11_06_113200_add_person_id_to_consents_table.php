<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'person_id')) {
                $table->foreignId('person_id')->nullable()->constrained('persons')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'person_id')) {
                $table->dropForeign(['person_id']);
                $table->dropColumn('person_id');
            }
        });
    }
};
