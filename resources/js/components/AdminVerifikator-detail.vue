<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header Bar -->
            <HeaderBar title="Detail Admin Verifikator" />
            <div class="separator"></div>

            <div class="container">
                <!-- Detail Data -->
                <div class="detail-container">
                    <h3>Detail Pengguna</h3>

                    <div class="form-group">
                        <label>Nama:</label>
                        <div class="value">{{ user.nama_lengkap }}</div>
                    </div>

                    <div class="form-group">
                        <label>Email:</label>
                        <div class="value">{{ user.email }}</div>
                    </div>

                    <div class="form-group">
                        <label>Posisi:</label>
                        <div class="value">{{ user.posisi }}</div>
                    </div>

                    <div class="form-group">
                        <label>Kontak:</label>
                        <div class="value">{{ user.kontak }}</div>
                    </div>

                    <div class="form-group">
                        <label>NIK:</label>
                        <div class="value">{{ user.nik }}</div>
                    </div>

                    <div class="form-group">
                        <label>Waktu Pengajuan:</label>
                        <div class="value">{{ user.waktu_pengajuan }}</div>
                    </div>

                    <div
                        class="form-group status-wrapper"
                        :class="{
                            'status-approved': user.status === 'success',
                            'status-denied': user.status === 'denied',
                            'status-pending': user.status === 'pending',
                        }"
                    >
                        <label>Status:</label>
                        <select
                            v-model="user.status"
                            @change="updateStatus"
                            class="custom-dropdown"
                        >
                            <option value="pending">Belum Disetujui</option>
                            <option value="success">Sudah Disetujui</option>
                            <option value="denied">Tidak Disetujui</option>
                        </select>
                    </div>

                    <div v-if="user.foto_ktp">
                        <h4>Foto KTP:</h4>
                        <img
                            :src="user.foto_ktp"
                            alt="Foto KTP"
                            class="photo-ktp"
                        />
                    </div>

                    <div class="button-group">
                        <router-link to="/admin-verifikator">
                            <button class="btn-back">Kembali</button>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import HeaderBar from "@/components/HeaderBar.vue";
import ModalConfirm from "@/components/ModalConfirm.vue";
import Sidebar from "@/components/Sidebar.vue";
import logoImage from "@/assets/posyandu.svg";
import axios from "axios";

export default {
    name: "VerifikatorComponentDetail",
    components: { HeaderBar, ModalConfirm, Sidebar },
    data() {
        return {
            activeMenu: "verifikasi",
            logoImage,
            user: {},
        };
    },
    async mounted() {
        const nik = this.$route.params.nik;
        await this.getUserDetail(nik);
    },
    methods: {
        async getUserDetail(nik) {
            try {
                const response = await axios.get(
                    `http://localhost:8000/api/auth/verifikator/${nik}`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            Accept: "application/json",
                        },
                    }
                );
                const userData = response.data.data;
                userData.foto_ktp = userData.foto_ktp
                    ? `${userData.foto_ktp}`
                    : null;
                this.user = userData;
            } catch (error) {
                console.error("Gagal mengambil detail pengguna:", error);
                alert("Gagal mengambil detail pengguna.");
            }
        },
        async updateStatus() {
            try {
                const newStatus = this.user.status;
                await axios.put(
                    `http://localhost:8000/api/auth/verifikator/${this.user.nik}`,
                    { status: newStatus },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );
                alert("Status berhasil diperbarui");
            } catch (error) {
                console.error("Gagal memperbarui status:", error);
                alert("Terjadi kesalahan saat memperbarui status");
            }
        },
    },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Protest+Strike&display=swap");

* {
    font-family: "Inter", sans-serif;
    margin: 0;
    padding: 0;
}

.layout {
    display: flex;
    min-height: 100vh;
    background-color: #f7f7f7;
}
/* Styling untuk status pada dropdown */
.status-wrapper .custom-dropdown {
    width: 20%;
    padding: px;
    margin-left: 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
    background-color: white;
}

.status-wrapper.status-approved .custom-dropdown {
    background-color: #e7fef8;
}

.status-wrapper.status-pending .custom-dropdown {
    background-color: #fff9c4;
}

.status-wrapper.status-denied .custom-dropdown {
    background-color: #ffcdd2;
}

.sidebar {
    width: 280px;
    height: auto;
    background-color: #ffffff;
    border-right: 1px solid #e5e7eb;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 5px;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 10px;
}

.logo-image {
    width: 35px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    margin: 0;
    margin-top: -5px;
}

.logo-text {
    font-family: "Protest Strike", sans-serif;
    font-size: 20px;
    color: #08607a;
}

.menu-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: -15px;
}

.menu-label {
    font-size: 12px;
    font-weight: 600;
    color: #b0b385;
    margin-bottom: 4px;
}

.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    color: #7d7f81;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.menu-list li:hover {
    background-color: #f1f5f9;
}

.menu-list li.active {
    background-color: #84bbd1;
    color: #074a5d;
}

.menu-list li.active a {
    font-weight: bold;
}

.menu-list a {
    text-decoration: none;
    color: inherit;
}

.main-content {
    flex: 1;
    padding: 20px;
    background-color: #ffffff;
    max-width: 100%;
    /* margin: auto; */
}

.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 32px;
    border-bottom: 1px solid #ccc;
}

/* .separator {
    border-bottom: 1px solid #e5e7eb;
    margin: 10px 0 20px 0;
} */

.page-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin-bottom: -3px;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: -10px;
}

.table-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 0px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    border: 1px solid #ccc;
    margin-top: 30px;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2px 20px;
    border-bottom: 1px solid #ccc;
}

table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    border: 1px solid #ccc;
}

thead {
    background-color: #f9f9f9;
}

th,
td {
    padding: 12px 16px;
    text-align: left;
    font-size: 14px;
    color: #333436;
    border: 1px solid #ccc;
    word-wrap: break-word;
}

th {
    font-size: 15px;
    color: #7d7f81;
    font-weight: bold;
}

th:last-child,
td:last-child {
    border-right: none;
}

th:first-child,
td:first-child {
    width: 50px;
}

th:nth-child(n + 2),
td:nth-child(n + 2) {
    width: 1fr;
}

.custom-dropdown {
    background-color: #e7f7f8;
    border-radius: 18px;
    height: 34px;
    width: 162px;
    text-align: center;
    border: 1px solid #e7f7f8;
    margin: 8px 0;
}

.detail-container {
    width: max;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 30px;
}

.detail-container h3 {
    font-size: 22px;
    font-weight: 600;
    color: #111827;
    padding-bottom: 16px;
    border-bottom: 1px solid #dbdbdb;
    margin-bottom: 10px;
}
.form-group {
    display: flex;
    margin-bottom: 12px;
    align-items: center;
    gap: 9rem;
}

.form-group label {
    width: 160px;
    font-weight: bold;
    padding: 8px 40px;
}

.form-group .value,
.form-group select {
    flex: 1;
}

.status-wrapper select {
    padding: 4px 8px;
}

.photo-ktp {
    max-width: 100%;
    margin-top: 8px;
}

.button-group {
    margin: 20px 0px;
}

.btn-back {
    padding: 10px 16px;
    border: none;
    background-color: #4f93af;
    color: white;
    border-radius: 10px;
    cursor: pointer;
}
@media (max-width: 768px) {
    .form-group {
        display: flex;
        margin-bottom: 5px;
        gap: 1rem;
    }
    .form-group label {
        width: 100px;
        font-weight: bold;
        padding: 5px;
    }
    .detail-container {
        padding: 20px;
        font-size: 13px;
    }
    .detail-container h3 {
        font-size: 18px;
    }
}
</style>
