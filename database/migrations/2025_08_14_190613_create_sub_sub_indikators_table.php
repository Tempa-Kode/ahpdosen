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
        Schema::create('sub_sub_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_indikator_id')->constrained('sub_indikator')->onDelete('restrict');
            $table->string('nama_sub_sub_indikator', 50)->unique();
            $table->integer('skor_kredit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_sub_indikator');
    }
};
