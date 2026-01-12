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
        Schema::table('production_trends', function (Blueprint $table) {
            $table->foreignId('cctv_id')->nullable()->after('building_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_trends', function (Blueprint $table) {
            $table->dropForeign(['cctv_id']);
            $table->dropColumn('cctv_id');
        });
    }
};
