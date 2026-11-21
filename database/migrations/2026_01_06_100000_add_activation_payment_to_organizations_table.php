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
        Schema::table('organizations', function (Blueprint $table) {
            // Change default status to inactive
            $table->enum('status', ['active', 'inactive'])->default('inactive')->change();
            
            // Add payment fields for activation
            $table->decimal('activation_fee', 10, 2)->default(0.00)->after('status');
            $table->decimal('paid_amount', 10, 2)->default(0.00)->after('activation_fee');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('paid_amount');
            $table->string('payment_id')->nullable()->after('payment_status');
            $table->timestamp('payment_date')->nullable()->after('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'activation_fee',
                'paid_amount', 
                'payment_status',
                'payment_id',
                'payment_date'
            ]);
            
            // Revert default status to active
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });
    }
};
