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
        Schema::table('buildings', function (Blueprint $table) {
            $table->decimal('efficiency', 5, 2)->default(0)->after('longitude');
            $table->decimal('traffic_density', 5, 2)->default(0)->after('efficiency');
            $table->decimal('signal_optimization', 5, 2)->default(0)->after('traffic_density');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn(['efficiency', 'traffic_density', 'signal_optimization']);
        });
    }
};
