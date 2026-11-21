<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            $table->string('selfie_upload_path')->nullable()->after('thumb_upload_path');
            $table->string('id_proof_upload_path')->nullable()->after('selfie_upload_path');
            $table->string('thumb_video_path')->nullable()->after('id_proof_upload_path');
        });
    }

    public function down(): void
    {
        Schema::table('consents', function (Blueprint $table) {
            $table->dropColumn(['selfie_upload_path', 'id_proof_upload_path', 'thumb_video_path']);
        });
    }
};
