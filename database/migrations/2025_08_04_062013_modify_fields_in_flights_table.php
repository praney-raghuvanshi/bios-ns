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
        Schema::table('flights', function (Blueprint $table) {
            $table->dropConstrainedForeignId('aircraft_id');
            $table->foreignId('aircraft_type_id')
                ->nullable()
                ->constrained('aircraft_types')
                ->after('arrival_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flights', function (Blueprint $table) {
            $table->dropConstrainedForeignId('aircraft_type_id');
            $table->foreignId('aircraft_id')
                ->nullable()
                ->constrained('aircrafts')
                ->after('arrival_time');
        });
    }
};
