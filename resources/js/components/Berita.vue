<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="updateActiveMenu"
        />
        <div class="main-content">
            <HeaderBar title="Berita Acara Posyandu" />
            <div class="separator"></div>

            <div class="container">
                <!-- Tabel Data berita -->
                <div class="table-container">
                    <div class="table-header">
                        <h3>Data Berita Acara posyandu</h3>
                        <router-link to="/berita-add" class="add-button"
                            >Tambah Berita</router-link
                        >
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Kategori</th>
                                <th>Judul</th>
                                <th>Tempat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(news, index) in beritaList"
                                :key="index"
                            >
                                <td>{{ index + 1 }}</td>
                                <td>{{ news.tanggal }}</td>
                                <td>{{ news.waktu }}</td>
                                <td>{{ news.kategori }}</td>
                                <td>{{ news.judul }}</td>
                                <td>{{ news.tempat }}</td>
                                <td class="aksi">
                                    <button
                                        title="Informasi"
                                        @click="navigateTo('informasi', news)"
                                    >
                                        <img
                                            :src="informasiIcon"
                                            alt="Informasi"
                                        />
                                    </button>
                                    <button
                                        title="Edit"
                                        @click="navigateTo('edit', news)"
                                    >
                                        <img :src="updateIcon" alt="Update" />
                                    </button>
                                    <button
                                        title="Hapus"
                                        @click="confirmDelete(news)"
                                    >
                                        <img :src="deleteIcon" alt="Delete" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Pagination Controls -->
                    <!-- <div class="pagination-controls">
                        <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
                        <span>Halaman {{ currentPage }} dari {{ totalPages }}</span>
                        <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
                    </div> -->
                </div>
            </div>
        </div>

        <SuccessAlert :visible="showSuccessAlert" :message="successMessage" />

        <!-- Modal Konfirmasi Hapus -->
        <ModalConfirm
            :visible="showModal"
            title="Konfirmasi Hapus Data"
            message="Apakah Anda yakin ingin menghapus data ini?"
            @cancel="cancelDelete"
            @confirm="deleteBerita"
        />
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import ModalConfirm from "@/components/ModalConfirm.vue";
import informasiIcon from "@/assets/Informasi.svg";
import updateIcon from "@/assets/Edit.svg";
import deleteIcon from "@/assets/Delete.svg";
import SuccessAlert from "@/components/SuccessAlert.vue";
import axios from "axios";

export default {
    name: "DataBeritaDetail",
    components: { Sidebar, HeaderBar, ModalConfirm, SuccessAlert },
    data() {
        return {
            activeMenu: "berita",
            showModal: false,
            beritaToDelete: null,
            selectedPosko: "",
            beritaList: [],
            informasiIcon,
            updateIcon,
            deleteIcon,
            // Tambahan untuk paginasi
            currentPage: 1,
            itemsPerPage: 9,
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    // computed: {
    //     paginatedAdminList() {
    //         const start = (this.currentPage - 1) * this.itemsPerPage;
    //         const end = start + this.itemsPerPage;
    //         return this.adminList.slice(start, end);
    //     },
    //     totalPages() {
    //         return Math.ceil(this.adminList.length / this.itemsPerPage);
    //     },
    // },
    created() {
        this.fetchLaporanBerita();
    },
    methods: {
        updateActiveMenu(menu) {
            this.activeMenu = menu;
        },
        async fetchLaporanBerita() {
            try {
                const token = localStorage.getItem("token");
                if (!token) throw new Error("Token tidak ditemukan");
                const response = await axios.get(
                    "https://capstonesi.online/api/auth/berita",
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                this.beritaList = response.data.data.sort((a, b) => {
                    return new Date(b.tanggal) - new Date(a.tanggal);
                });
            } catch (error) {
                console.error("Gagal mengambil data:", error);
            }
        },
        navigateTo(action, berita) {
            localStorage.setItem(`dataBerita${action}`, JSON.stringify(berita));
            this.$router.push(`/Berita-${action}/${berita.id}`);
        },
        confirmDelete(berita) {
            this.beritaToDelete = berita;
            this.showModal = true;
        },
        cancelDelete() {
            this.showModal = false;
            this.beritaToDelete = null;
        },
        async deleteBerita() {
            try {
                const token = localStorage.getItem("token");
                if (!token) throw new Error("Token tidak ditemukan");
                await axios.delete(
                    `https://capstonesi.online/api/auth/berita/${this.beritaToDelete.id}`,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                // Tampilkan alert sukses
                this.successMessage = "Berita berhasil dihapus!";
                this.showSuccessAlert = true;

                setTimeout(() => {
                    this.showSuccessAlert = false;
                }, 2000);

                this.fetchLaporanBerita();
            } catch (error) {
                console.error("Gagal menghapus data:", error);

                // Tampilkan alert error
                this.errorMessage = "Gagal menghapus berita. Coba lagi nanti.";
                this.showErrorAlert = true;

                setTimeout(() => {
                    this.showErrorAlert = false;
                }, 3000);
            } finally {
                this.cancelDelete();
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

.main-content {
    padding: 30px;
    flex: 1;
    background-color: #ffffff;
    padding-top: 10px;
    flex-direction: column;
}
.container {
    padding-bottom: 3rem; /* atau lebih besar sesuai kebutuhan */
}

.separator {
    border-bottom: 1px solid #e5e7eb;
    margin: 10px 0 20px 0;
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
    /* Ganti ke warna yang sama seperti border tabel */
}

.table-header h3 {
    font-size: 13px;
    /* Ganti ukuran font judul */
    color: #111216;
    /* Ganti warna font judul */
    font-weight: bold;
}

.table-header .add-button {
    font-size: 13px;
    /* Ganti ukuran font tombol */
    color: #074a5d;
    /* Ganti warna font tombol */
    font-weight: bold;
    /* Memberikan ketebalan pada font tombol */
}

.separator1 {
    border-bottom: 1px solid #e5e7eb;
    margin: 30px 0 10px 0;
}

.add-button {
    background-color: transparent;
    text-decoration: none;
    color: #0066cc;
    border: none;
    font-weight: bold;
    cursor: pointer;
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

.aksi button {
    background: none;
    border: none;
    margin-right: 5px;
    font-size: 16px;
    cursor: pointer;
}

.aksi button img {
    width: 20px;
    /* Menyesuaikan ukuran ikon */
    height: 20px;
    /* Menyesuaikan ukuran ikon */
    object-fit: contain;
}

.aksi button:not(:last-child) {
    border-right: 1px solid #ccc;
}

.aksi button:last-child {
    margin-right: 0;
}

.aksi button:hover {
    opacity: 0.7;
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
    .table-header {
        border-bottom: none;
    }

    td:last-child {
        border-bottom: none;
    }
}
</style>
