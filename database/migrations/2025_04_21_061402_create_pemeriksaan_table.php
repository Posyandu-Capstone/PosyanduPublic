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
        Schema::create('pemeriksaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('warga')->onDelete('cascade');
            // $table->foreignId('anggota_keluarga_id')->constrained('anggota_keluarga')->onDelete('cascade');
            $table->string('anggota_keluarga_nik', 16)->nullable(); 
            $table->foreign('anggota_keluarga_nik')->references('nik')->on('anggota_keluarga')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('antrian_id')->unique()->constrained('antrian')->onDelete('cascade');
            // $table->foreignId('berita_id')->unique()->nullable()->constrained('berita')->onDelete('cascade');
            $table->string('tipe_pemeriksaan');
            $table->text('catatan')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('diagnosa')->nullable();

            // $table->date('tanggal_periksa');
            // $table->float('berat_badan'); 
            // $table->float('tinggi_badan'); 
            // $table->enum('bb_per_umur', ['Kurang', 'Normal', 'Lebih'])->nullable();
            // $table->enum('tb_per_umur', ['TB Pendek', 'TB Normal', 'TB Tinggi'])->nullable();
            // $table->enum('bb_per_tb', ['Gizi Buruk', 'Gizi Kurang', 'Gizi Baik', 'Gizi Lebih'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan');
    }
};
