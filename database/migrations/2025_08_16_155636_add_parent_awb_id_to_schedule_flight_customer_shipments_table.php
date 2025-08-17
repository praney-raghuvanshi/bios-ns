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
        Schema::table('schedule_flight_customer_shipments', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_awb_id')->nullable()->after('type');
            $table->foreign('parent_awb_id', 'sfcs_parent_awb_fk')->references('id')->on('schedule_flight_customer_shipments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_flight_customer_shipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_awb_id');
        });
    }
};
