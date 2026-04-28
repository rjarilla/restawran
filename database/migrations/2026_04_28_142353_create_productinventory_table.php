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
        Schema::create('productinventory', function (Blueprint $table) {
            $table->id('ProductInventoryID');
            $table->unsignedBigInteger('ProductID');
            $table->integer('ProductQuantity')->default(0);
            $table->date('ProductBatchExpiry')->nullable();
            $table->timestamps();

            // OPTIONAL (enable only if product table is ready)
            // $table->foreign('ProductID')
            //       ->references('ProductID')
            //       ->on('product')
            //       ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productinventory');
    }
};