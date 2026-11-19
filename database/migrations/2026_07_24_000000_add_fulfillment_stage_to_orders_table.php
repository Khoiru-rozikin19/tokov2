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
                if (!Schema::hasColumn('orders', 'fulfillment_stage')) {
                    // queued | vpn | supplier | done | failed_vpn | failed_supplier | failed
                    $table->string('fulfillment_stage', 30)->nullable()->after('status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'fulfillment_stage')) {
                    $table->dropColumn('fulfillment_stage');
                }
            });
        }
    }
};

