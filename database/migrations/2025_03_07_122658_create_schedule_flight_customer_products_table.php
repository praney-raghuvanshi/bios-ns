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
        Schema::create('schedule_flight_customer_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_flight_customer_id');
            $table->foreign('schedule_flight_customer_id', 'sfcp_sfc_fk')->references('id')->on('schedule_flight_customers');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id', 'sfcp_p_fk')->references('id')->on('products');
            $table->integer('uplifted_weight')->default(0);
            $table->integer('offloaded_weight')->default(0);
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
        Schema::dropIfExists('schedule_flight_customer_products');
    }
};
