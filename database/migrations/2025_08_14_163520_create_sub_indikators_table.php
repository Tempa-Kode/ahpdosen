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
        Schema::create('sub_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')
                ->constrained('indikator')
                ->onDelete('cascade');
            $table->string('nama_sub_indikator', 50)->unique();
            $table->integer('skor_kredit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_indikator');
    }
};
