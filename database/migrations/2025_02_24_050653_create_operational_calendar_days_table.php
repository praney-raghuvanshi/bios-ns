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
        Schema::create('operational_calendar_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operational_calendar_id')->constrained('operational_calendars');
            $table->date('day');
            $table->integer('week');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_calendar_days');
    }
};
