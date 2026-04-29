<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('CustomerID')->primary();
            $table->string('CustomerCode')->nullable();
            $table->string('CustomerName');
            $table->string('CustomerEmail')->nullable();
            $table->string('CustomerContactNumber')->nullable();
            $table->string('CustomerAddressLine1')->nullable();
            $table->string('CustomerCity')->nullable();
            $table->string('CustomerProvince')->nullable();
            $table->timestamp('CustomerUpdateDate')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};