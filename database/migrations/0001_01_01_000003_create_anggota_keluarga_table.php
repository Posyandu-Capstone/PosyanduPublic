<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('anggota_keluarga', function (Blueprint $table) {
            // $table->id();
            $table->string('nik', 16)->primary();
            // $table->foreignId('keluarga_id')->constrained('keluarga')->onDelete('cascade');
            $table->string('keluarga_no_kk', 16)->nullable();
            $table->foreign('keluarga_no_kk')
                  ->references('no_kk')->on('keluarga')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('nama_anggota_keluarga');
            $table->string('posisi_keluarga')->nullable();

            // DATA Kesehatan
            $table->string('posko_terdaftar')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->integer('Anak_Ke')->nullable();
            $table->string('nomor_telp')->nullable();
            $table->float('tinggi_lahir')->nullable();
            $table->float('berat_lahir')->nullable();
            $table->string('penyakit_bawaan')->nullable();
            $table->boolean('bpjs')->nullable();
            $table->boolean('gakin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('anggota_keluarga');
    }
};
