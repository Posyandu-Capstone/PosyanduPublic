<template>
  <div class="menu-section">
    <p class="menu-label">Admin</p>
    <ul class="menu-list">
      <router-link to="/profil" class="router-link-wrapper">
        <li class="menu-item" :class="{ active: activeMenu === 'profil' }">
          <img src="@/assets/profil.svg" class="icon" alt="Profil" />
          <span>Profil</span>
        </li>
      </router-link>

      <li class="menu-item" @click="confirmLogout">
        <img src="@/assets/SignOut.svg" class="icon" alt="Keluar" />
        <span>Keluar</span>
      </li>
    </ul>

    <ModalConfirm :visible="showLogoutModal" title="Konfirmasi Keluar" message="Apakah Anda yakin ingin keluar?"
      @cancel="cancelLogout" @confirm="logout" />
  </div>
</template>

<script>
import ModalConfirm from "@/components/ModalConfirm.vue";

export default {
  name: "AdminSection",
  components: { ModalConfirm },
  data() {
    return {
      showLogoutModal: false,
    };
  },
  methods: {
    confirmLogout() {
      this.showLogoutModal = true;
    },
    cancelLogout() {
      this.showLogoutModal = false;
    },
    logout() {
      localStorage.removeItem("token");
      this.$router.push("/login");
      this.showLogoutModal = false;
    },
  },
};
</script>

<style scoped>
router-link {
  text-decoration: none;
  /* Menghilangkan garis bawah */
}

.menu-section {
  margin-top: 20px;
}

.menu-label {
  font-size: 12px;
  font-weight: bold;
  color: #B0B385;
  margin-bottom: 8px;
}

.menu-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.router-link-wrapper {
  text-decoration: none;
  color: inherit;
}
.router-link-wrapper:visited,
.router-link-wrapper:hover,
.router-link-wrapper:active {
  text-decoration: none;
  color: inherit;
}
.menu-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border-radius: 8px;
  color: #7D7F81;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.menu-item:hover {
  background-color: #f1f5f9;
}

.icon {
  width: 20px;
  height: 20px;
}
</style>
