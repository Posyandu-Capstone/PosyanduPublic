<template>
  <div class="container">
    <div v-if="status === 'pending'" class="card pending">
      <h1>Akun Anda Masih Diproses</h1>
      <p>Silakan tunggu hingga akun Anda diverifikasi oleh admin.</p>
    </div>
    <div v-else-if="status === 'denied'" class="card denied">
      <h1>Akun Anda Ditolak</h1>
      <p>Mohon hubungi admin untuk informasi lebih lanjut.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const status = ref('');

onMounted(() => {
  status.value = route.path.includes('pending') ? 'pending' : 'denied';
});
</script>

<style scoped>
body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #74ebd5, #acb6e5);
  margin: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.container {
  text-align: center;
}

.card {
  background-color: #fff;
  padding: 30px;
  margin: 20px;
  border-radius: 15px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  animation: fadeIn 0.8s ease-in-out;
}

.card h1 {
  font-size: 2rem;
  margin-bottom: 10px;
}

.card p {
  font-size: 1.1rem;
}

.pending {
  border-left: 8px solid #ffd700;
  color: #555;
}

.denied {
  border-left: 8px solid #ff4d4d;
  color: #b22222;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>