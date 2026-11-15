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
        if (Schema::hasTable('product_discounts')) {
            Schema::table('product_discounts', function (Blueprint $table) {
                if (!Schema::hasColumn('product_discounts', 'payment_methods')) {
                    $table->json('payment_methods')->nullable()->after('roles');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_discounts')) {
            Schema::table('product_discounts', function (Blueprint $table) {
                if (Schema::hasColumn('product_discounts', 'payment_methods')) {
                    $table->dropColumn('payment_methods');
                }
            });
        }
    }
};

