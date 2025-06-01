<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabel Kecamatan
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->String('code')->primary();
            $table->string('Nama_Kecamatan')->notNull();
            $table->timestamps();
        });

        // Tabel Desa
        Schema::create('desa', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('kecamatan_id');
            $table->foreign('kecamatan_id')->references('code')->on('kecamatan')->onDelete('cascade');
            $table->string('Nama_Desa')->notNull();
            $table->timestamps();
        });
        

        // Tabel Dusun
        Schema::create('dusun', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('desa_id');
            $table->foreign('desa_id')->references('code')->on('desa')->onDelete('cascade');
            $table->string('Nama_Dusun')->notNull();
            $table->timestamps();
        });

        // Tabel RW
        Schema::create('rw', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('dusun_id');
            $table->foreign('dusun_id')->references('code')->on('dusun')->onDelete('cascade');
            $table->integer('No_RW')->notNull();
            $table->timestamps();
        });

        // Tabel RT
        Schema::create('rt', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('rw_id');
            $table->foreign('rw_id')->references('code')->on('rw')->onDelete('cascade');
            $table->integer('No_RT')->notNull();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rt');
        Schema::dropIfExists('rw');
        Schema::dropIfExists('dusun');
        Schema::dropIfExists('desa');
        Schema::dropIfExists('kecamatan');
    }
};
