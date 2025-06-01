<template>
    <div class="posko-container">
        <div class="main-content">
            <SideBar activeMenu="posko" />

            <div class="content-wrapper">
                <h2 class="page-title">Data Posko Posyandu</h2>

                <div class="search-bar">
                    <div class="search-input">
                        <img
                            src="@/assets/search-web.svg"
                            alt="Search"
                            class="search-icon"
                        />
                        <input
                            type="text"
                            placeholder="Cari Posyandu..."
                            v-model="searchQuery"
                            @input="searchPosyandu"
                        />
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h2 class="location-title">
                            Posko Posyandu Desa Jemawan
                        </h2>
                        <button class="add-btn" @click="goToTambahPosko">
                            Tambah Posko
                        </button>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Nama Posko Posyandu</th>
                                <th>Alamat</th>
                                <th>Nama Koordinator</th>
                                <th>Jumlah Anggota</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(posyandu, index) in posyanduList"
                                :key="posyandu.id || index"
                            >
                                <td data-label="Nomor">{{ index + 1 }}</td>
                                <td data-label="Nama Posko Posyandu">
                                    {{ posyandu.nama_posyandu }}
                                </td>
                                <td data-label="Alamat">
                                    {{ posyandu.nama_desa || "-" }}
                                </td>
                                <td data-label="Nama Koordinator">
                                    {{ posyandu.nama_koordinator || "-" }}
                                </td>
                                <td data-label="Jumlah Anggota">
                                    {{ posyandu.jumlah_anggota || "-" }}
                                </td>
                                <td data-label="Aksi" class="aksi">
                                    <button
                                        title="Informasi"
                                        @click="
                                            navigateTo('informasi', posyandu)
                                        "
                                    >
                                        <img
                                            :src="informasiIcon"
                                            alt="Informasi"
                                        />
                                    </button>
                                    <button
                                        title="Edit"
                                        @click="navigateTo('edit', posyandu)"
                                    >
                                        <img :src="updateIcon" alt="Update" />
                                    </button>
                                    <button
                                        title="Hapus"
                                        @click="confirmDelete(posyandu)"
                                    >
                                        <img :src="deleteIcon" alt="Delete" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="posyanduList.length === 0">
                                <td
                                    colspan="6"
                                    style="text-align: center; padding: 20px"
                                >
                                    Tidak ada data ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Konfirmasi Hapus -->
                <ModalConfirm
                    :visible="showModal"
                    title="Konfirmasi Hapus Data"
                    message="Apakah Anda yakin ingin menghapus data ini?"
                    @cancel="cancelDelete"
                    @confirm="deletePosyandu"
                />
                <SuccessAlert
                    :visible="showSuccessAlert"
                    :message="successMessage"
                />
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import SideBar from "@/components/SideBar.vue";
import ModalConfirm from "@/components/ModalConfirm.vue";
import informasiIcon from "@/assets/Informasi.svg";
import updateIcon from "@/assets/Edit.svg";
import deleteIcon from "@/assets/Delete.svg";
import SuccessAlert from "@/components/SuccessAlert.vue";

export default {
    name: "PoskoPosyandu",
    components: {
        SideBar,
        ModalConfirm,
        SuccessAlert,
    },
    data() {
        return {
            posyanduList: [],
            searchQuery: "",
            informasiIcon,
            updateIcon,
            deleteIcon,
            posyanduToDelete: null,
            showModal: false,
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    created() {
        this.fetchPosyandu();
    },
    methods: {
        goToTambahPosko() {
            this.$router.push("/tambah-posko");
        },
        async fetchPosyandu() {
            try {
                const token = localStorage.getItem("token");
                if (!token) throw new Error("Token tidak ditemukan");

                const response = await axios.get(
                    "http://localhost:8000/api/auth/posyandu",
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );

                const rawData = response.data.data || [];
                this.posyanduList = rawData.map((item) => ({
                    id: item.id ?? null,
                    nama_posyandu: item.nama_posyandu ?? "-",
                    nama_desa: item.nama_desa ?? "-",
                    nama_koordinator: item.nama_koordinator ?? "-",
                    jumlah_anggota: item.jumlah_anggota ?? "-",
                }));
            } catch (error) {
                console.error("Gagal mengambil data posyandu:", error);
            }
        },
        async searchPosyandu() {
            try {
                const token = localStorage.getItem("token");
                if (!token) throw new Error("Token tidak ditemukan");

                if (this.searchQuery.trim() === "") {
                    await this.fetchPosyandu();
                } else {
                    const response = await axios.get(
                        "http://localhost:8000/api/auth/posyandu/search",
                        {
                            params: { search: this.searchQuery },
                            headers: { Authorization: `Bearer ${token}` },
                        }
                    );

                    console.log("Response API Posyandu Search:", response.data);
                    this.posyanduList = response.data.map((item) => ({
                        id: item.id ?? null,
                        nama_posyandu: item.nama_posyandu ?? "-",
                        nama_desa: item.alamat ?? "-",
                        nama_koordinator: "-", // tidak tersedia di endpoint search
                        jumlah_anggota: "-", // tidak tersedia di endpoint search
                    }));
                }
            } catch (error) {
                console.error("Gagal mencari data posyandu:", error);
            }
        },
        navigateTo(action, posyandu) {
            if (!posyandu.id) {
                alert("ID Posyandu tidak ditemukan.");
                return;
            }
            this.$router.push(`/Posko-${action}/${posyandu.id}`);
        },
        confirmDelete(posyandu) {
            this.posyanduToDelete = posyandu;
            this.showModal = true;
        },
        cancelDelete() {
            this.posyanduToDelete = null;
            this.showModal = false;
        },
        async deletePosyandu() {
            try {
                const token = localStorage.getItem("token");
                if (!token) throw new Error("Token tidak ditemukan");

                if (!this.posyanduToDelete?.id) {
                    throw new Error("ID tidak tersedia");
                }

                await axios.delete(
                    `http://localhost:8000/api/auth/posyandu/${this.posyanduToDelete.id}`,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );

                this.showModal = false;
                // ✅ Tampilkan success alert
                this.successMessage = "Data posyandu berhasil dihapus.";
                this.showSuccessAlert = true;

                // ✅ Tutup alert otomatis setelah 3 detik (atau waktu fade animasi kamu)
                setTimeout(() => {
                    this.showSuccessAlert = false;
                }, 3000);

                // ✅ Refresh data
                this.fetchPosyandu();
            } catch (error) {
                console.error("Gagal menghapus posyandu:", error);
                this.showModal = false;
                alert("Terjadi kesalahan saat menghapus data.");
            }
        },
    },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap");

.posko-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    font-family: "Inter", sans-serif;
    background-color: #ffffff;
}

.main-content {
    display: flex;
    flex: 1;
}

.content-wrapper {
    flex: 1;
    padding: 0px 24px 0px 24px;
    overflow-x: auto;
}

.page-title {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
    padding-bottom: 18px;
    margin-bottom: 24px;
    margin-top: 24px;
    border-bottom: 1px solid #edf2f7;
}

.search-bar {
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #edf2f7;
    width: 100%;
}

.search-input {
    position: relative;
    width: 100%;
    max-width: 100%; /* ubah dari 400px ke 100% agar fleksibel */
    box-sizing: border-box;
}

.search-input input {
    width: 100%; /* Ubah dari 1088px jadi 100% */
    padding: 15px 16px 15px 40px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s;
    box-sizing: border-box; /* Pastikan padding tidak melebihi ukuran */
}

.search-input input:focus {
    outline: none;
    border-color: #3b82f6;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    opacity: 0.6;
}
.table-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 0px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    border: 1px solid #ccc;
    margin-top: 30px;
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

.table-header {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    align-items: center;
    padding: 2px 20px;
    border-bottom: 1px solid #ccc;
    /* Ganti ke warna yang sama seperti border tabel */
}
.table-header h2 {
    font-size: 13px;
    /* Ganti ukuran font judul */
    color: #111216;
    /* Ganti warna font judul */
    font-weight: bold;
    flex: 1;
}
.table-header .add-btn {
    font-size: 13px;
    /* Ganti ukuran font tombol */
    color: #074a5d;
    /* Ganti warna font tombol */
    font-weight: bold;
    /* Memberikan ketebalan pada font tombol */
    white-space: nowrap;
}

.separator1 {
    border-bottom: 1px solid #e5e7eb;
    margin: 30px 0 10px 0;
}

.add-btn {
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
    /* Menggabungkan semua border menjadi satu */
    border: 1px solid #ccc;
    /* Border luar tabel */
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

@media (max-width: 768px) {
    .search-input input {
        width: 100%;
        padding: 12px 16px 12px 40px;
    }
    table,
    thead,
    tbody,
    th,
    td,
    tr {
        display: block;
        border: none;
    }

    td:first-child {
        padding-right: 150px;
    }

    /* Sembunyikan header tabel */
    thead {
        display: none;
    }

    /* Setiap baris jadi seperti kartu */
    tr {
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 10px;
        background-color: #fff;
    }

    /* Cell berisi data + label */
    td {
        position: relative;
        padding-left: 45%;
        text-align: left;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        display: flex;
        align-items: center;
        min-height: 48px;
        box-sizing: border-box;
    }

    /* Label data tampil sebelum konten */
    td::before {
        position: absolute;
        top: 50%;
        left: 16px;
        transform: translateY(-50%);
        width: 40%;
        padding-right: 10px;
        white-space: normal;
        font-weight: 600;
        color: #555;
        content: attr(
            data-label
        ); /* Pastikan kamu pakai data-label di HTML-nya */
    }

    /* Hilangkan border bawah pada cell terakhir */
    td:last-child {
        border-bottom: none;
    }

    /* Perbaikan layout header atas jika perlu */
    .table-header {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        gap: 10px;
    }
    .dropdown-group {
        flex-direction: column;
    }
}
</style>
