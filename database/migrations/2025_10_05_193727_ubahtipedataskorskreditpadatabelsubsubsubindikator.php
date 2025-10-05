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
        Schema::table('sub_sub_sub_indikator', function (Blueprint $table) {
            $table->decimal('skor_kredit', 8, 1)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_sub_sub_indikator', function (Blueprint $table) {
            $table->integer('skor_kredit')->nullable()->change();
        });
    }
};
