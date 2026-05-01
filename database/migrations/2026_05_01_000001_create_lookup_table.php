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
        if (!Schema::hasTable('lookup')) {
            Schema::create('lookup', function (Blueprint $table) {
                $table->string('LookupID')->primary();
                $table->string('LookupCategory');
                $table->string('LookupName');
                $table->string('LookupValue');
                $table->timestamp('LookupUpdateDate')->nullable();
                $table->unsignedBigInteger('LookupUpdateBy')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lookup');
    }
};
