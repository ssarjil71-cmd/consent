<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('consent_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('type', 50)->nullable();
            $table->string('description')->nullable();
            $table->decimal('balance_after', 10, 2)->nullable();
            $table->timestamps();

            $table->index('organization_id');
            $table->index('consent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
