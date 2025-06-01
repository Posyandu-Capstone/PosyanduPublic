<template>
    <div v-if="visible" class="modal-overlay">
      <div class="modal">
        <img :src="SuccessIcon" alt="Success" class="modal-icon" />
        <h3>{{ title }}</h3>
        <p>{{ message }}</p>
        <div class="modal-actions">
          <button @click="$emit('close')" class="btn-confirm">OK</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import SuccessIcon from '@/assets/success.svg'; // Ganti dengan ikon kamu sendiri
  
  export default {
    props: {
      visible: {
        type: Boolean,
        required: true
      },
      title: {
        type: String,
        default: 'Berhasil'
      },
      message: {
        type: String,
        default: 'Registrasi berhasil dilakukan.'
      }
    },
    data() {
      return {
        SuccessIcon
      };
    }
  };
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  
  .modal {
    background: #fff;
    padding: 15px;
    border-radius: 10px;
    width: 300px;
    max-height: 80vh;
    text-align: center;
  }
  
  .modal-actions {
    display: flex;
    justify-content: center;
  }
  
  .btn-confirm {
    padding: 10px 24px;
    border-radius: 20px;
    background: #08607A;
    color: white;
    cursor: pointer;
    width: 100%;
    max-width: 150px;
  }
  
  .modal-icon {
    width: 50px;
    height: 50px;
    margin-bottom: 10px;
  }
  </style>
  