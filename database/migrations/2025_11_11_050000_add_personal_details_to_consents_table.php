<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'age')) {
                $table->unsignedTinyInteger('age')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('consents', 'sex')) {
                $table->string('sex', 20)->nullable()->after('age');
            }
            if (!Schema::hasColumn('consents', 'address')) {
                $table->string('address')->nullable()->after('sex');
            }
            if (!Schema::hasColumn('consents', 'registration_number')) {
                $table->string('registration_number', 100)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'registration_number')) {
                $table->dropColumn('registration_number');
            }
            if (Schema::hasColumn('consents', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('consents', 'sex')) {
                $table->dropColumn('sex');
            }
            if (Schema::hasColumn('consents', 'age')) {
                $table->dropColumn('age');
            }
        });
    }
};
