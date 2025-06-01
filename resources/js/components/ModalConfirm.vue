<template>
  <div v-if="visible" class="modal-overlay">
    <div class="modal">
      <!-- Menggunakan imported image dengan dynamic src binding -->
      <img :src="ConfirmDelete" alt="Confirm Delete" class="modal-icon" />
      <h3>{{ title }}</h3>
      <p>{{ message }}</p>
      <div class="modal-actions">
        <button @click="$emit('cancel')" class="btn-cancel">Tidak</button>
        <button @click="$emit('confirm')" class="btn-confirm">Ya</button>
      </div>
    </div>
  </div>
</template>

<script>
import ConfirmDelete from '@/assets/Confirm_delete.svg';  // Pastikan path sesuai dengan struktur folder kamu

export default {
  props: {
    visible: {
      type: Boolean,
      required: true
    },
    title: {
      type: String,
      default: 'Konfirmasi'
    },
    message: {
      type: String,
      default: 'Apakah Anda yakin?'
    }
  },
  data() {
    return {
      // Pastikan gambar yang diimport bisa dipakai di template
      ConfirmDelete: ConfirmDelete
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
  z-index: 999;
}

.modal {
  background: #fff;
  padding: 16px;
  border-radius: 10px;
  width: 90%;
  max-width: 320px;
  text-align: center;
  max-height: 80vh;
  box-sizing: border-box;
}

.modal-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 16px;
}

.btn-cancel, .btn-confirm {
  padding: 10px 20px;
  border-radius: 20px;
  cursor: pointer;
  width: 100%;
  font-size: 14px;
  transition: all 0.2s ease-in-out;
}

.btn-cancel {
  background: white;
  border: 2px solid #08607A;
  color: #08607A;
}

.btn-cancel:hover {
  background: #08607A;
  color: white;
}

.btn-confirm {
  background: #08607A;
  color: white;
  border: none;
}

.modal-icon {
  width: 48px;
  height: 48px;
  margin-bottom: 10px;
}

/* Responsive tweaks */
@media screen and (max-width: 768px) {
  .modal {
    width: 80%;
    max-width: 95%;
    padding: 9px;
  }

  .btn-cancel, .btn-confirm {
    font-size: 14px;
    padding: 10px 16px;
  }

  .modal-icon {
    width: 36px;
    height: 36px;
  }
}

</style>
