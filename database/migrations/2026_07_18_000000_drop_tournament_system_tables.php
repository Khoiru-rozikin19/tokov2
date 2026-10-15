<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('tournament_points_history');
        Schema::dropIfExists('tournament_matches');
        Schema::dropIfExists('tournament_participants');
        Schema::dropIfExists('tournament_registrations');
        Schema::dropIfExists('tournaments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fitur turnamen telah dihapus permanen.
    }
};
