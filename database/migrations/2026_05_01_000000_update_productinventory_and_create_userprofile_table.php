<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('productinventory')) {
            Schema::table('productinventory', function (Blueprint $table) {
                if (!Schema::hasColumn('productinventory', 'ProductBatchID')) {
                    $table->string('ProductBatchID')->nullable()->after('ProductInventoryID');
                }
                if (!Schema::hasColumn('productinventory', 'ProductBatchDeliveryDate')) {
                    $table->date('ProductBatchDeliveryDate')->nullable()->after('ProductQuantity');
                }
                if (!Schema::hasColumn('productinventory', 'ProductReceivedBy')) {
                    $table->string('ProductReceivedBy')->nullable()->after('ProductBatchExpiry');
                }
            });

            DB::table('productinventory')->whereNull('ProductBatchID')->update([
                'ProductBatchID' => DB::raw('UUID()'),
            ]);

            Schema::table('productinventory', function (Blueprint $table) {
                if (!Schema::hasColumn('productinventory', 'ProductBatchID')) {
                    return;
                }
                $table->unique('ProductBatchID');
            });
        }

        if (!Schema::hasTable('userprofile')) {
            Schema::create('userprofile', function (Blueprint $table) {
                $table->string('UserProfileID')->primary();
                $table->string('UserProfileName');
                $table->timestamp('UserProfileUpdateDate')->nullable();
                $table->string('UserProfileUpdateBy')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('productinventory')) {
            Schema::table('productinventory', function (Blueprint $table) {
                if (Schema::hasColumn('productinventory', 'ProductBatchID')) {
                    $table->dropUnique(['ProductBatchID']);
                    $table->dropColumn('ProductBatchID');
                }
                if (Schema::hasColumn('productinventory', 'ProductBatchDeliveryDate')) {
                    $table->dropColumn('ProductBatchDeliveryDate');
                }
                if (Schema::hasColumn('productinventory', 'ProductReceivedBy')) {
                    $table->dropColumn('ProductReceivedBy');
                }
            });
        }

        Schema::dropIfExists('userprofile');
    }
};
