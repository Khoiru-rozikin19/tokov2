<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'supplier_response')) {
                    $table->text('supplier_response')->nullable()->after('sn');
                }
                if (!Schema::hasColumn('orders', 'refunded_at')) {
                    $table->timestamp('refunded_at')->nullable()->after('paid_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'supplier_response')) {
                    $table->dropColumn('supplier_response');
                }
                if (Schema::hasColumn('orders', 'refunded_at')) {
                    $table->dropColumn('refunded_at');
                }
            });
        }
    }
};
