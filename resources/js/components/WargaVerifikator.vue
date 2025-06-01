<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header Bar -->
            <HeaderBar title="Dashboard Warga Verifikator" />
            <div class="separator"></div>

            <div class="dashboard-header">
                <div class="welcome-text">
                    <h3>Selamat Datang, {{ userRole }} {{ userName }} 👋</h3>
                </div>
            </div>

            <div class="container">
                <!-- Tabel Data Register -->
                <div class="table-container">
                    <div class="table-header">
                        <h3>Data Register</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Waktu Pengajuan</th>
                                <th>Status</th>
                                <th>Detail</th>
                                <!-- Kolom baru -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(verif, index) in paginatedAdminList"
                                :key="index"
                            >
                                <td>
                                    {{
                                        (currentPage - 1) * itemsPerPage +
                                        index +
                                        1
                                    }}
                                </td>
                                <td>{{ verif.Nama }}</td>
                                <td>{{ verif.NIK }}</td>
                                <td>{{ verif.waktu_pengajuan }}</td>
                                <td>
                                    <router-link
                                        :to="{
                                            name: 'WargaVerifikatorDetail',
                                            params: { nik: verif.NIK },
                                        }"
                                    >
                                        <button class="btn-detail">
                                            Lihat Detail
                                        </button>
                                    </router-link>
                                </td>
                                <td>
                                    <div
                                        :class="{
                                            'status-approved':
                                                verif.status ===
                                                'Sudah Disetujui',
                                            'status-denied':
                                                verif.status ===
                                                'Tidak Disetujui',
                                            'status-pending':
                                                verif.status ===
                                                'Belum Disetujui',
                                        }"
                                        class="status-wrapper"
                                    >
                                        <select
                                            v-model="verif.status"
                                            @change="updateStatus(verif)"
                                            class="custom-dropdown"
                                        >
                                            <option>Sudah Disetujui</option>
                                            <option>Belum Disetujui</option>
                                            <option>Tidak Disetujui</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Pagination Controls -->
                    <div class="pagination-controls">
                        <button @click="prevPage" :disabled="currentPage === 1">
                            Previous
                        </button>
                        <span
                            >Halaman {{ currentPage }} dari
                            {{ totalPages }}</span
                        >
                        <button
                            @click="nextPage"
                            :disabled="currentPage === totalPages"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import HeaderBar from "@/components/HeaderBar.vue";
import Sidebar from "@/components/Sidebar.vue";
import ModalConfirm from "@/components/ModalConfirm.vue";
import logoImage from "@/assets/posyandu.svg";
import axios from "axios";

export default {
    name: "VerifikatorComponent",
    components: { HeaderBar, ModalConfirm, Sidebar },
    data() {
        return {
            userName: "",
            userRole: "",
            activeMenu: "verifikasi2",
            logoImage,
            adminList: [], // isi dari API
            showModal: false,
            // Tambahan untuk paginasi
            currentPage: 1,
            itemsPerPage: 9,
        };
    },
    computed: {
        paginatedAdminList() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.adminList.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.adminList.length / this.itemsPerPage);
        },
    },
    mounted() {
        this.userName = localStorage.getItem("userName") || "";
        this.userRole = localStorage.getItem("userRole") || "";
        this.getWargaList();
    },
    methods: {
        async viewDetail(user) {
            try {
                const response = await axios.get(
                    `https://capstonesi.online/api/auth/warga-verifikator/${user.NIK}`, // Ganti ID dengan NIK
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
                alert(`
                Nama: ${userData.nama_lengkap}
                Email: ${userData.email}
                Kontak: ${userData.no_telp}
                NIK: ${userData.anggota_keluarga_nik}
                Waktu Pengajuan: ${userData.waktu_pengajuan}
                Status: ${userData.status}
            `);
            } catch (error) {
                console.error("Gagal mengambil detail pengguna:", error);
                alert("Gagal mengambil detail pengguna.");
            }
        },
        async getWargaList() {
            try {
                const response = await axios.get(
                    "https://capstonesi.online/api/auth/warga-verifikator",
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                            Accept: "application/json",
                        },
                    }
                );

                this.adminList = response.data.data.map((item) => ({
                    Nama: item.nama_lengkap,
                    NIK: item.anggota_keluarga_nik,
                    waktu_pengajuan: item.waktu_pengajuan,
                    status: this.mapStatus(item.status),
                }));
            } catch (error) {
                console.error("Gagal mengambil data pengguna:", error);
                alert("Gagal mengambil data pengguna.");
            }
        },
        mapStatus(status) {
            if (!status) return "Belum Disetujui";
            switch (status) {
                case "pending":
                    return "Belum Disetujui";
                case "success":
                    return "Sudah Disetujui";
                case "denied":
                    return "Tidak Disetujui";
                default:
                    return status;
            }
        },
        reverseStatus(status) {
            switch (status) {
                case "Belum Disetujui":
                    return "pending";
                case "Sudah Disetujui":
                    return "success";
                case "Tidak Disetujui":
                    return "denied";
                default:
                    return status;
            }
        },
        async updateStatus(user) {
            try {
                const newStatus = this.reverseStatus(user.status);
                await axios.put(
                    `https://capstonesi.online/api/auth/warga-verifikator/${user.NIK}`,
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
                this.getAWargaList();
            } catch (error) {
                console.error("Gagal memperbarui status:", error);
                alert("Terjadi kesalahan saat memperbarui status");
            }
        },
        // Metode paginasi
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
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
}

.layout {
    display: flex;
    height: 100vh;
    background-color: #f7f7f7;
}

/* Status Sudah Disetujui (warna sebelumnya) */
/* Status Sudah Disetujui (warna sebelumnya) */
.status-wrapper.status-approved .custom-dropdown {
    background-color: #e7fef8;
    /* Warna sebelumnya */
}

/* Status Belum Disetujui */
.status-wrapper.status-pending .custom-dropdown {
    background-color: #fff9c4;
    /* Kuning muda */
}

/* Status Tidak Disetujui */
.status-wrapper.status-denied .custom-dropdown {
    background-color: #ffcdd2;
    /* Merah muda */
}

.btn-detail {
    background-color: #c6f8ff;
    color: black;
    padding: 8px 16px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
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
    padding: 30px;
    flex: 1;
    background-color: #ffffff;
    padding-top: 10px;
}

.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.separator {
    border-bottom: 1px solid #e5e7eb;
    margin: 10px 0 20px 0;
}

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
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2px 20px;
    border-bottom: 1px solid #ccc;
    /* Ganti ke warna yang sama seperti border tabel */
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
    text-align: center;
    /* Menengahkan teks */
    font-size: 14px;
    /* Ukuran font */
    color: #333436;
    /* Warna font */
    border: 1px solid #ccc;
    /* Semua garis sel akan memiliki border yang sama */
    word-wrap: break-word;
}

th {
    font-size: 15px;
    /* Ukuran font untuk header */
    color: #7d7f81;
    /* Warna font untuk header */
    font-weight: bold;
}

th:last-child,
td:last-child {
    border-right: none;
}

/* Set width untuk kolom pertama (Nomor) */
th:first-child,
td:first-child {
    width: 50px;
    /* Lebar tetap untuk nomor */
}

/* Set width untuk kolom-kolom lainnya supaya lebih proporsional */
th:nth-child(n + 2),
td:nth-child(n + 2) {
    width: 1fr;
    /* Kolom lainnya otomatis mengikuti lebar yang tersedia */
}

.custom-dropdown {
    background-color: #e7fef8;
    border-radius: 18px;
    height: 34px;
    width: 162px;
    text-align: center;
    border: 1px #e7fef8;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 16px;
}

.pagination-controls button {
    padding: 6px 12px;
    background-color: #074a5d;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.pagination-controls button:disabled {
    background-color: #bdc3c7;
    cursor: not-allowed;
}
@media (max-width: 768px) {
    table,
    thead,
    tbody,
    th,
    td,
    tr {
        display: block;
        border: none;
    }

    thead {
        display: none;
    }

    .table-container {
        border: none;
        box-shadow: none;
    }
    .table-header {
        border-bottom: none;
    }

    tr {
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 10px;
    }

    td {
        text-align: left;
        position: relative;
        border-bottom: 1px solid #eee;
    }

    td::before {
        content: attr(data-label);
        position: absolute;
        left: 16px;
        width: 45%;
        padding-left: 10px;
        font-weight: bold;
        text-align: left;
        color: #ffffff;
    }

    td:last-child {
        border-bottom: none;
    }
}
</style>
