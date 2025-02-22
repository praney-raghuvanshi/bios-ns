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
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('iata', 3);
            $table->string('name', 50);
            $table->string('city', 50)->nullable();
            $table->string('country', 50)->nullable();
            $table->decimal('summer_difference', 5, 2);
            $table->decimal('winter_difference', 5, 2);
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
        Schema::dropIfExists('airports');
    }
};
