<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop existing organizations table if it exists
        Schema::dropIfExists('organizations');

        // Create new organizations table with correct structure
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
    }

    public function down()
    {
        Schema::dropIfExists('organizations');
    }
};
