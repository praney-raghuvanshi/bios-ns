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
        Schema::create('schedule_flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->foreignId('flight_id')->constrained('flights');
            $table->tinyInteger('status')->default(1);
            $table->time('estimated_departure_time')->nullable();
            $table->time('actual_departure_time')->nullable();
            $table->integer('departure_time_diff')->nullable();
            $table->time('estimated_arrival_time')->nullable();
            $table->time('actual_arrival_time')->nullable();
            $table->integer('arrival_time_diff')->nullable();
            $table->integer('uplifted')->nullable();
            $table->decimal('utilisation', 8, 2)->nullable();
            $table->integer('offloaded')->nullable();
            $table->string('latest_remark')->nullable();
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
        Schema::dropIfExists('schedule_flights');
    }
};
