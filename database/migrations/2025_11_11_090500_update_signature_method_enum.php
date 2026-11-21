<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('consents', 'signature_method')) {
            DB::statement("ALTER TABLE consents MODIFY signature_method ENUM('draw','upload','note') DEFAULT 'draw'");
            DB::statement("UPDATE consents SET signature_method = 'draw' WHERE signature_method = 'note'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('consents', 'signature_method')) {
            DB::statement("ALTER TABLE consents MODIFY signature_method ENUM('note','upload') DEFAULT 'note'");
        }
    }
};
