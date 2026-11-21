<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'client_ip')) {
                $table->string('client_ip', 45)->nullable()->after('registration_number');
            }

            if (!Schema::hasColumn('consents', 'client_location')) {
                $table->string('client_location')->nullable()->after('client_ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'client_location')) {
                $table->dropColumn('client_location');
            }

            if (Schema::hasColumn('consents', 'client_ip')) {
                $table->dropColumn('client_ip');
            }
        });
    }
};
