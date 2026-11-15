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
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'cost_price')) {
                    $table->bigInteger('cost_price')->nullable()->after('total_amount');
                }
                if (!Schema::hasColumn('orders', 'profit')) {
                    $table->bigInteger('profit')->nullable()->after('cost_price');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('orders', 'cost_price')) {
                    $cols[] = 'cost_price';
                }
                if (Schema::hasColumn('orders', 'profit')) {
                    $cols[] = 'profit';
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
