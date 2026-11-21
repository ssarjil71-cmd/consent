<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (!Schema::hasColumn('consents', 'client_latitude')) {
                $table->decimal('client_latitude', 10, 7)->nullable()->after('client_location');
            }

            if (!Schema::hasColumn('consents', 'client_longitude')) {
                $table->decimal('client_longitude', 10, 7)->nullable()->after('client_latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            if (Schema::hasColumn('consents', 'client_longitude')) {
                $table->dropColumn('client_longitude');
            }

            if (Schema::hasColumn('consents', 'client_latitude')) {
                $table->dropColumn('client_latitude');
            }
        });
    }
};
