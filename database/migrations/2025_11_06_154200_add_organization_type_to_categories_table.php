<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // First, make organization_id nullable if it's not already
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->change();
        });
        
        // Then add the new columns
        Schema::table('categories', function (Blueprint $table) {
            // Add organization_type_id column with foreign key
            $table->foreignId('organization_type_id')->after('organization_id')
                  ->constrained('organization_types')
                  ->onDelete('cascade');
                  
            // Add created_by_admin column
            $table->foreignId('created_by_admin')->after('organization_type_id')
                  ->nullable()
                  ->constrained('admins')
                  ->onDelete('set null');
                  
            // Add indexes for better performance
            $table->index('organization_type_id');
            $table->index('created_by_admin');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['organization_type_id']);
            $table->dropForeign(['created_by_admin']);
            
            // Then drop the columns
            $table->dropColumn(['organization_type_id', 'created_by_admin']);
            
            // Revert organization_id to not nullable
            $table->foreignId('organization_id')->nullable(false)->change();
        });
    }
};
