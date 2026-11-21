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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('organization_type_id')->constrained('organization_types')->onDelete('cascade');
            $table->foreignId('created_by_admin')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('organization_id');
            $table->index('organization_type_id');
            $table->index('created_by_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
