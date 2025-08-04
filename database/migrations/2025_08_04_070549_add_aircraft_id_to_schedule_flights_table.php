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
        Schema::table('schedule_flights', function (Blueprint $table) {
            $table->foreignId('aircraft_id')
                ->nullable()
                ->constrained('aircrafts')
                ->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_flights', function (Blueprint $table) {
            $table->dropConstrainedForeignId('aircraft_id');
        });
    }
};
