<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'CustomerCode')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('CustomerCode')->nullable()->after('CustomerID');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'CustomerCode')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('CustomerCode');
            });
        }
    }
};