<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('warga', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('keluarga_id')->nullable()->constrained('keluarga')->onDelete('cascade');
            $table->string('anggota_keluarga_nik', length: 16)->nullable(); 
            $table->foreign('anggota_keluarga_nik')->references('nik')->on('anggota_keluarga')->onDelete('cascade');
            $table->string('keluarga_no_kk', 16)->nullable();
            $table->foreign('keluarga_no_kk')->references('no_kk')->on('keluarga')->onDelete('cascade');
            $table->foreignId('posyandu_id')->nullable()->constrained('posyandu')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('password');
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->integer('Anak_Ke')->nullable();
            $table->string('no_telp')->nullable();
            $table->timestamps();

            //Masih bingung
            
        });
    }

    public function down(): void {
        Schema::dropIfExists('warga');
    }
};
