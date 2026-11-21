<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            $table->string('otp_code', 10)->nullable()->after('phone');
            $table->timestamp('otp_verified_at')->nullable()->after('otp_code');
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_verified_at']);
        });
    }
};
