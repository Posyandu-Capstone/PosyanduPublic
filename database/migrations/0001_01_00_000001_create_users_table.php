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
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->string('google_id')->nullable();
                
                $table->string('nama_lengkap')->nullable();
                $table->string('nik', 16)->nullable(); 
                $table->string('foto_ktp')->nullable();
                $table->string('foto_profil')->nullable();
                // $table->foreign( 'anggota_keluarga_nik')->references('nik')->on('anggota_keluarga')->onDelete('cascade')->onUpdate('cascade');
                $table->enum('posisi', ['Admin Desa', 'Admin Verifikator', 'Kader'])->nullable();
                $table->string('status')->nullable();
                $table->string('kontak')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });

            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });

            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('users');
            Schema::dropIfExists('password_reset_tokens');
            Schema::dropIfExists('sessions');
        }
    };
