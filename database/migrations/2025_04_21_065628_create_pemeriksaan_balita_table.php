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
        Schema::create('pemeriksaan_balita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemeriksaan_id')->references('id')->on('pemeriksaan')->onDelete('cascade');
            $table->string('imunisasi')->nullable();
            $table->float('berat_badan_kg')->nullable();
            $table->float('tinggi_badan')->nullable();
            $table->date('tanggal_timbang')->nullable();
            $table->string('PMT')->nullable();
            $table->float('total_PMT')->nullable();
            $table->boolean('ASI')->default(false);
            $table->string('vit')->nullable();
            $table->string('PM')->nullable();
            $table->string('NTO_B')->nullable();
            $table->string('Suplemen')->nullable();
            $table->float('zscore_tb_u')->nullable();
            $table->float('zscore_bb_u')->nullable();
            $table->float('zscore_bb_tb')->nullable();
            $table->string('tb_u')->nullable();
            $table->string('bb_u')->nullable();
            $table->string('bb_tb')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_balita');
    }
};
