<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {

            // PRIMARY KEY (AUTO-INCREMENT)
            $table->bigIncrements('CustomerID');

            // CUSTOMER INFORMATION
            $table->string('CustomerName');
            $table->string('CustomerAddressLine1')->nullable();
            $table->string('CustomerAddressLine2')->nullable();
            $table->string('CustomerStreet')->nullable();
            $table->string('CustomerCity')->nullable();
            $table->string('CustomerProvince')->nullable();
            $table->string('CustomerPostalCode')->nullable();
            $table->string('CustomerEmail')->nullable();
            $table->string('CustomerContactNumber')->nullable();

            // AUDIT FIELDS
            $table->string('CustomerUpdateBy')->nullable();
            $table->timestamp('CustomerUpdateDate')->nullable();

            // LARAVEL DEFAULT TIMESTAMPS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};