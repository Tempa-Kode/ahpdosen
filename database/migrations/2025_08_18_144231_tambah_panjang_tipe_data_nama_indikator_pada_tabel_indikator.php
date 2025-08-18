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
        Schema::table('indikator', function (Blueprint $table) {
            $table->string('nama_indikator', 255)->change();
            $table->string('kd_indikator')->nullable()->after('kriteria_id');
            $table->decimal('bobot_indikator', 8, 2)->nullable()->after('nama_indikator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator', function (Blueprint $table) {
            $table->string('nama_indikator', 50)->change();
            $table->dropColumn('bobot_indikator');
        });
    }
};
