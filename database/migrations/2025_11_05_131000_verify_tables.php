<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Verify organization_types table
        if (!Schema::hasTable('organization_types')) {
            Schema::create('organization_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                $table->timestamps();
            });
            echo "Created organization_types table\n";
        }

        // Verify organizations table
        if (!Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('type_id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->decimal('transaction_amount', 10, 2)->default(0);
                $table->rememberToken();
                $table->timestamps();

                $table->foreign('type_id')
                      ->references('id')
                      ->on('organization_types')
                      ->onDelete('cascade');
            });
            echo "Created organizations table\n";
        } else {
            // Check if type_id column exists
            if (!Schema::hasColumn('organizations', 'type_id')) {
                Schema::table('organizations', function (Blueprint $table) {
                    $table->unsignedBigInteger('type_id')->after('id');
                    
                    // Add foreign key constraint if organization_types exists
                    if (Schema::hasTable('organization_types')) {
                        $table->foreign('type_id')
                              ->references('id')
                              ->on('organization_types')
                              ->onDelete('cascade');
                    }
                });
                echo "Added type_id column to organizations table\n";
            }
        }
    }

    public function down()
    {
        // No need to drop tables in this migration
    }
};
