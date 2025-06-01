<template>
    <div class="logo-wrapper">
      <img :src="logoImage" alt="Logo Image" class="logo-image" />
      <span class="logo-text">PosyanduCare</span>
    </div>
  
    <div class="center-wrapper">
      <div class="register-container">
        <img src="@/assets/ConfirmNIK.svg" alt="Confirm NIK Illustration" class="confirmnik-image" />
        <h2 :style="{ color: '#074A5D' }">NIK Anda Valid</h2>
        <p class="subtitle">Silakan lanjutkan untuk membuat akun</p>
  
        <form @submit.prevent="goToStep2">
          <button type="submit" class="signin-btn">Lanjutkan</button>
        </form>
  
        <p v-if="message" :class="messageClass">{{ message }}</p>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import logoImage from '@/assets/logo.png';
  
  export default {
    name: 'ConfirmNIK',
    data() {
      return {
        form: {
          nik: ''
        },
        message: '',
        messageClass: '',
        logoImage
      };
    },
    mounted() {
      // Ambil NIK dari localStorage kalau sudah ada sebelumnya
      const nik = localStorage.getItem('nik');
      if (nik) this.form.nik = nik;
    },
    methods: {
  goToStep2() {
    const nik = localStorage.getItem('nik');
    if (nik) {
      this.$router.push('/registerstep2');
    } else {
      this.message = 'NIK belum tersedia. Silakan isi NIK terlebih dahulu.';
      this.messageClass = 'error-message';
    }
  }
}
  }

  </script>
  
  <style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap');
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
  
  * {
    font-family: 'Inter', sans-serif;
  }
  
  .logo-wrapper {
    position: absolute;
    top: 20px;
    left: 30px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  
  .logo-image {
    width: 60px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
  }
  
  .logo-text {
    font-family: 'Protest Strike', sans-serif;
    font-size: 26px;
    color: #08607A;
  }
  
  .center-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }
  
  .register-container {
  width: 100%;
  max-width: 450px;
  text-align: center;
}

  .confirmnik-image {
    width: 100%;
    max-width: 250px;
    margin: 0 auto 5px;
  }
  
  h2 {
    font-size: 23px;
    margin-bottom: 10px;
    font-weight: bold;
  }
  
  .subtitle {
    font-size: 14px;
    color: gray;
    margin-bottom: 25px;
  }
  
  .signin-btn {
    width: 100%;
    padding: 10px;
    background-color: #4F93AF;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    margin-top: 15px;
  }
  
  .signin-btn:hover {
    background-color: #165d52;
  }
  
  .error-message {
    color: red;
    margin-top: 10px;
    font-size: 14px;
  }

  /* === Responsive for tablets and below === */
@media (max-width: 768px) {
  .logo-wrapper {
    top: 15px;
    left: 20px;
    gap: 8px;
  }

  .logo-image {
    width: 45px;
  }

  .logo-text {
    font-size: 20px;
  }

  h2 {
    font-size: 20px;
  }

  .subtitle {
    font-size: 13px;
  }

  .signin-btn {
    padding: 10px;
    font-size: 15px;
    width: 300px;
  }

  .error-message {
    font-size: 13px;
  }
}
  </style>