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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->uuid('flight_pair_id')->index();
            $table->string('flight_number');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('from')->constrained('airports');
            $table->foreignId('to')->constrained('airports');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->foreignId('aircraft_id')->constrained('aircrafts');
            $table->date('effective_date');
            $table->tinyInteger('arrival_day')->default(0);
            $table->enum('flight_type', ['inbound', 'outbound']);
            $table->foreignId('corresponding_flight')->nullable()->constrained('flights');
            $table->uuid('cloned_from')->nullable();
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
        Schema::dropIfExists('flights');
    }
};
