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
        Schema::create('posyandu', function (Blueprint $table) {
            $table->id();
            $table->string('nama_posyandu');
            $table->string('alamat')->nullable();

            $table->string('desa_id')->nullable();
            $table->foreign('desa_id')->references('code')->on('desa');
            $table->string('kecamatan_id')->nullable();
            $table->foreign('kecamatan_id')->references('code')->on('kecamatan');

            // NAMA Kader & Kontak kader bisa ditaruh di users
            // $table->string('nama_kader')->nullable();
            // $table->string('kontak_kader')->nullable();

            $table->json('jenis_pelayanan')->nullable(); // Menyimpan layanan sebagai JSON
            $table->json('fasilitas')->nullable(); // Menyimpan fasilitas sebagai JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posyandu');
    }
};
