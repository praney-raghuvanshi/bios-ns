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
        Schema::table('aircrafts', function (Blueprint $table) {
            $table->dropColumn('capacity');
            $table->foreignId('aircraft_type_id')->nullable()->constrained('aircraft_types')->after('id');
            $table->renameColumn('name', 'registration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aircrafts', function (Blueprint $table) {
            $table->decimal('capacity', 10, 2)->nullable();
            $table->dropForeign(['aircraft_type_id']);
            $table->dropColumn('aircraft_type_id');
            $table->renameColumn('registration', 'name');
        });
    }
};
