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
        Schema::create('orderdetails', function (Blueprint $table) {
            $table->string('OrderDetailsID')->primary();
            $table->string('OrderID');
            $table->string('ProductID');
            $table->integer('OrderQuantity');
            $table->decimal('OrderQuantityPrice', 10, 2);
            $table->decimal('OrderItemTotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orderdetails');
    }
};
