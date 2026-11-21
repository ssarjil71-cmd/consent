<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'per_consent_amount')) {
                $table->decimal('per_consent_amount', 10, 2)->default(0)->after('transaction_amount');
            }

            if (!Schema::hasColumn('organizations', 'consent_label')) {
                $table->string('consent_label', 50)->nullable()->after('per_consent_amount');
            }

            if (!Schema::hasColumn('organizations', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'remember_token')) {
                $table->dropColumn('remember_token');
            }

            if (Schema::hasColumn('organizations', 'consent_label')) {
                $table->dropColumn('consent_label');
            }

            if (Schema::hasColumn('organizations', 'per_consent_amount')) {
                $table->dropColumn('per_consent_amount');
            }
        });
    }
};
