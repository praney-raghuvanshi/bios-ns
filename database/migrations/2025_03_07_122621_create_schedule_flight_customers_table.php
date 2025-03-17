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
        Schema::create('schedule_flight_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_flight_id')->constrained('schedule_flights');
            $table->foreignId('customer_id')->constrained('customers');
            $table->integer('total_uplifted_weight')->default(0);
            $table->integer('total_offloaded_weight')->default(0);
            $table->boolean('active')->default(true);
            $table->foreignId('added_by')->constrained('users');
            $table->timestamps();
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_flight_customers');
    }
};
