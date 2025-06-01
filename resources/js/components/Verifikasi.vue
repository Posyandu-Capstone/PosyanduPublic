<template>
  <div class="container">
    <!-- Kotak Hijau Kiri -->
    <div class="left-box">
      <img :src="babyImage" alt="Baby Image" class="left-image" />
      <img :src="dokterImage" alt="Dokter Image" class="left-image2" />
      <h2>Dashboard Admin Posyandu</h2>
      <p>
        Mengelola informasi kesehatan ibu dan anak, laporan kegiatan, dan data kunjungan dalam satu platform praktis.
      </p>
    </div>

    <!-- Kotak Kanan -->
    <div class="right-box">
      <!-- Logo -->
      <div class="logo-wrapper">
        <img :src="logoImage" alt="Logo Image" class="logo-image" />
        <span class="logo-text">PosyanduCare</span>
      </div>

      <div class="register-container">
        <h2>Masukkan Kode Verifikasi</h2>
        <p class="subtitle">Monitor, Manage, and Care Better</p>
        <p class="verification-label">Kode Verifikasi</p>

        <!-- Input kode -->
        <div class="token-input-wrapper">
          <input
            v-for="(digit, index) in code"
            :key="index"
            type="text"
            maxlength="1"
            class="token-underline"
            v-model="code[index]"
            @input="handleInput(index, $event)"
            ref="inputs"
          />
        </div>

        <!-- Tombol Sign In -->
        <button
          type="submit"
          class="signin-btn"
          :disabled="!isCodeComplete"
          @click="submitCode"
        >
          Verifikasi
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import babyImage from '@/assets/baby.jpg';
import dokterImage from '@/assets/dokter.png';
import logoImage from '@/assets/logo.png';

export default {
  name: 'VerifikasiComponent',
  data() {
    return {
      babyImage,
      dokterImage,
      logoImage,
      code: ['', '', '', '', ''] // 5 digit kode verifikasi
    };
  },
  computed: {
    isCodeComplete() {
      return this.code.every((char) => char !== '');
    }
  },
  methods: {
    handleInput(index, event) {
      const value = event.target.value;
      if (value.length === 1 && index < this.code.length - 1) {
        this.$refs.inputs[index + 1].focus();
      }
    },
    submitCode() {
      const joinedCode = this.code.join('');
      console.log('Kode verifikasi yang dimasukkan:', joinedCode);
      // TODO: Lanjutkan logika ke backend atau navigasi
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Protest+Strike&display=swap');

* {
  font-family: 'Inter', sans-serif;
}

.container {
  display: flex;
  min-height: 100vh;
  overflow-y: auto;
}

.left-box {
  width: 45%;
  background-color: #1a4e44;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  padding: 0 40px 60px 40px;
  border-radius: 10px;
  color: white;
  box-sizing: border-box;
  overflow: hidden;
  position: relative;
}

.left-box h2 {
  font-size: 35px;
  margin-bottom: 5px;
}

.left-box p {
  font-size: 16px;
  max-width: 80%;
  line-height: 1.5;
}

.left-image,
.left-image2 {
  width: calc(100% + 80px);
  margin: 0 -40px;
  object-fit: cover;
  height: 300px;
}

.left-image2 {
  margin-bottom: 20px;
  border-radius: 0;
}

.logo-wrapper {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-left: 50px;
  margin-bottom: 40px;
  align-self: flex-start;
}

.right-box {
  width: 55%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  padding-top: 10px;
  overflow: hidden;
}

.logo-image {
  width: 75px;
  object-fit: cover;
  border-radius: 10px 10px 0 0;
  margin-left: 25px;
}

.logo-text {
  font-family: 'Protest Strike', sans-serif;
  font-size: 26px;
  color: #08607A;
}

.register-container {
  width: 80%;
  max-width: 450px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start;
  margin-top: 100px;
}

h2 {
  font-size: 32px;
  margin-bottom: 10px;
}

.subtitle {
  font-size: 14px;
  color: gray;
}

.verification-label {
  font-weight: bold;
  font-size: 20px;
}

.token-input-wrapper {
  display: flex;
  gap: 16px;
  justify-content: center;
  margin: 30px 0;
}

.token-underline {
  width: 40px;
  font-size: 24px;
  text-align: center;
  border: none;
  border-bottom: 2px solid #ccc;
  outline: none;
  background: transparent;
  transition: border-color 0.3s;
}

.token-underline:focus {
  border-bottom: 2px solid #08607A;
}

.signin-btn {
  width: 100%;
  padding: 15px;
  background-color: #4F93AF;
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  margin-top: 15px;
  transition: background-color 0.3s;
}

.signin-btn:hover {
  background-color: #165d52;
}

.signin-btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

@media (max-width: 1024px) {
  .container {
    flex-direction: column;
    padding: 20px;
  }

  .left-box, .right-box {
    width: 100%;
    max-width: 100%;
  }

  .left-image,
  .left-image2 {
    height: 280px;
  }

  .logo-wrapper {
    margin-left: 0;
    margin-bottom: 40px;
  }
}
</style>
