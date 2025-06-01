<template>
    <div class="layout">
        <!-- Sidebar -->
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <!-- Main Content -->
        <div class="main-content">
            <div class="top-header">
                <h2 class="page-title">Pemeriksaan Kesehatan</h2>
                <div class="top-right"></div>
            </div>

            <div class="separator"></div>

            <div class="container">
                <div class="filters">
                    <!-- Filters (Posko & Usia) -->
                    <div class="dropdown-group">
                        <div
                            class="select-with-icon custom-dropdown"
                            @click="toggleDropdown('posko')"
                        >
                            <img
                                src="@/assets/posko1.svg"
                                alt="posko icon"
                                class="icon"
                            />
                            <div class="dropdown-label">
                                {{ selectedPosko || "Pilih Posko Posyandu" }}
                            </div>
                            <img
                                src="@/assets/arrow-down.svg"
                                alt="arrow"
                                class="arrow-icon"
                            />
                            <ul
                                v-show="dropdownOpen === 'posko'"
                                class="dropdown-options"
                            >
                                <li @click.stop="selectOption('posko', '')">
                                    <em>Hilangkan Filter Posko</em>
                                </li>
                                <li
                                    v-for="(posko, index) in poskoOptions"
                                    :key="index"
                                    @click.stop="
                                        selectOption(
                                            'posko',
                                            posko.nama_posyandu
                                        )
                                    "
                                >
                                    {{ posko.nama_posyandu }}
                                </li>
                            </ul>
                        </div>
                        <!-- Dropdown Usia -->
                        <div
                            class="select-with-icon custom-dropdown"
                            @click="toggleDropdown('usia')"
                        >
                            <img
                                src="@/assets/posko1.svg"
                                alt="usia icon"
                                class="icon"
                            />
                            <div class="dropdown-label">
                                {{ selectedUsia || "Pilih Kelompok Usia" }}
                            </div>
                            <img
                                src="@/assets/arrow-down.svg"
                                alt="arrow"
                                class="arrow-icon"
                            />
                            <ul
                                v-show="dropdownOpen === 'usia'"
                                class="dropdown-options"
                            >
                                <li @click.stop="selectOption('usia', '')">
                                    <em>Hilangkan Filter Usia</em>
                                </li>
                                <li
                                    @click.stop="selectOption('usia', 'Balita')"
                                >
                                    Balita
                                </li>
                                <li
                                    @click.stop="selectOption('usia', 'Remaja')"
                                >
                                    Remaja
                                </li>
                                <li
                                    @click.stop="selectOption('usia', 'Dewasa')"
                                >
                                    Dewasa
                                </li>
                                <li
                                    @click.stop="selectOption('usia', 'Lansia')"
                                >
                                    Lansia
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Pencarian -->
                    <div class="input-wrapper">
                        <input
                            type="text"
                            placeholder="Cari Anggota"
                            v-model="searchQuery"
                            @input="onInputSearch"
                        />
                        <img
                            src="@/assets/search.svg"
                            alt="Search Icon"
                            class="search-icon"
                        />
                    </div>
                </div>

                <div class="separator1"></div>

                <!-- Tabel Data Pasien -->
                <div class="table-container">
                    <div class="table-header">
                        <h3>Data Pasien</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Nama</th>
                                <th>NIK</th>
                                <th>Alamat</th>
                                <th>Nama Ibu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(pasien, index) in pasienList"
                                :key="pasien.nik"
                            >
                                <td data-label="No">{{ index + 1 }}</td>
                                <td data-label="Nama">
                                    {{ pasien.nama || "-" }}
                                </td>
                                <td data-label="NIK">{{ pasien.nik }}</td>
                                <td data-label="Alamat">{{ pasien.alamat }}</td>
                                <td data-label="Nama Ibu">
                                    {{ pasien.nama_ibu }}
                                </td>
                                <td class="aksi" data-label="Aksi">
                                    <button
                                        title="Informasi"
                                        @click="goToRiwayatPasien(pasien)"
                                    >
                                        <img
                                            :src="informasiIcon"
                                            alt="Informasi"
                                        />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import logoImage from "@/assets/posyandu.svg";
import informasiIcon from "@/assets/Informasi.svg";
import axios from "axios";

export default {
    name: "PemeriksaanKesehatan",
    components: {
        Sidebar,
    },
    props: ["nik"],
    data() {
        return {
            logoImage,
            informasiIcon,
            activeMenu: "pasien",
            dropdownOpen: null,
            debouncedSearch: null,
            searchQuery: "",
            selectedPosko: "",
            selectedUsia: "",
            pasienList: [],
            poskoOptions: [], // ← PENTING
        };
    },

    created() {
        this.debouncedSearch = this.debounce(this.searchPasien, 500);
        this.fetchPoskoOptions();
    },

    mounted() {
        this.fetchLaporanKesehatan();
    },

    watch: {
        selectedPosko() {
            this.searchPasien();
        },
        selectedUsia() {
            this.searchPasien();
        },
    },

    methods: {
        debounce(func, wait) {
            let timeout;
            return function (...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(this, args);
                }, wait);
            };
        },

        onInputSearch() {
            this.debouncedSearch();
        },

        //    toggleDropdown(menuName) {
        //   if (this.dropdownOpen === menuName) {
        //     this.dropdownOpen = null; // close dropdown kalau sama
        //   } else {
        //     this.dropdownOpen = menuName; // buka dropdown yang diklik
        //   }
        // },

        searchPasien() {
            const token = localStorage.getItem("token");

            if (
                !this.searchQuery.trim() &&
                !this.selectedPosko &&
                !this.selectedUsia
            ) {
                return this.fetchLaporanKesehatan();
            }

            // URL pencarian
            let url = "https://capstonesi.online/api/auth/pemeriksaan/search?";
            if (this.searchQuery) {
                url += `nama=${encodeURIComponent(this.searchQuery)}&`;
            }
            if (this.selectedPosko) {
                url += `posko=${encodeURIComponent(this.selectedPosko)}&`;
            }
            if (this.selectedUsia) {
                url += `age=${encodeURIComponent(this.selectedUsia)}&`;
            }

            // Hapus & di akhir
            url = url.endsWith("&") ? url.slice(0, -1) : url;

            axios
                .get(url, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                })
                .then((response) => {
                    if (response.data.status === "success") {
                        this.pasienList =
                            response.data.data.length > 0
                                ? response.data.data
                                : [];
                        if (this.pasienList.length === 0) {
                            console.log("Data tidak ditemukan");
                        }
                    } else {
                        console.log("Error:", response.data.message);
                        this.pasienList = [];
                    }
                })
                .catch((error) => {
                    console.error("Gagal mencari pasien:", error);
                });
        },

        fetchLaporanKesehatan() {
            const token = localStorage.getItem("token");

            axios
                .get("https://capstonesi.online/api/auth/pemeriksaan", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                })
                .then((response) => {
                    this.pasienList = this.removeDuplicateByNIK(
                        response.data.data
                    );
                })
                .catch((error) => {
                    console.error(
                        "Ada masalah saat mengambil data laporan kesehatan:",
                        error
                    );
                });
        },

        removeDuplicateByNIK(data) {
            const seen = new Set();
            return data.filter((pasien) => {
                if (seen.has(pasien.nik)) return false;
                seen.add(pasien.nik);
                return true;
            });
        },
        toggleDropdown(dropdown) {
            // Toggle dropdown yang terbuka
            if (this.dropdownOpen === dropdown) {
                this.dropdownOpen = null; // Menutup dropdown jika sudah terbuka
            } else {
                this.dropdownOpen = dropdown; // Membuka dropdown yang dipilih
            }
        },

        selectOption(type, value) {
            if (type === "posko") {
                this.selectedPosko = value;
            } else if (type === "usia") {
                this.selectedUsia = value;
            }
            this.dropdownOpen = null;
        },

        fetchPoskoOptions() {
            const token = localStorage.getItem("token");

            axios
                .get("https://capstonesi.online/api/auth/poskoAllow", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                })
                .then((response) => {
                    this.poskoOptions = response.data.data;
                })
                .catch((error) => {
                    console.error("Gagal mengambil daftar posko:", error);
                });
        },

        goToRiwayatPasien(pasien) {
            if (pasien && pasien.nik) {
                this.$router.push(`/riwayatpasien/${pasien.nik}`);
            } else {
                alert("Data pasien tidak valid");
            }
        },

        beforeRouteEnter(to, from, next) {
            next((vm) => {
                vm.fetchLaporanKesehatan();
            });
        },

        beforeRouteUpdate(to, from, next) {
            this.fetchLaporanKesehatan();
            next();
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
    min-height: 50vh;
    background-color: #f7f7f7;
}

.main-content {
    padding: 30px;
    flex: 1;
    background-color: #ffffff;
    padding-top: 10px;
    flex-direction: column;
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

.divider-vertical {
    width: 1px;
    height: 24px;
    background-color: #d1d5db;
}

.notif-btn {
    background-color: #ffffff;
    border: 1px solid #d1d5db;
    /* garis luar */
    border-radius: 12px;
    padding: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    /* bayangan halus */
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: box-shadow 0.2s;
}

.notif-btn:hover {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.notif-btn .icon {
    width: 20px;
    /* Sesuaikan ukuran ikon notifikasi */
    height: 16px;
}

.top-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.profile-pic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

.filters {
    display: flex;
    flex-direction: column;
    gap: 16px;
    width: 100%;
}

.dropdown-group {
    display: flex;
    gap: 16px;
    width: 100%;
}

.custom-dropdown {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    background-color: #fff;
    flex: 1;
    position: relative;
}

.custom-dropdown .icon {
    width: 20px;
    height: 20px;
}

.dropdown-label {
    flex: 1;
    color: #5a5a5a;
    font-size: 14px;
}

.dropdown-options {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 100;
    width: 100%;
    margin: 4px 0 0 0;
    padding: 0;
    border: 1px solid #ccc;
    border-radius: 6px;
    list-style: none;
    background-color: #fff;
}

.dropdown-options li {
    padding: 8px 12px;
    cursor: pointer;
    color: #5a5a5a;
    font-size: 14px;
}

.dropdown-options li:hover {
    background-color: #f5f5f5;
}

.select-with-icon {
    position: relative;
}

.arrow-icon {
    position: absolute;
    right: 10px;
    /* Sesuaikan dengan posisi yang diinginkan */
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    /* Sesuaikan ukuran ikon */
    height: 20px;
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

.input-wrapper {
    position: relative;
    display: flex;
    flex: 1;
}

.search-icon {
    position: absolute;
    top: 50%;
    left: 12px;
    /* Atur jarak dari kiri */
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
}

input[type="text"] {
    padding-left: 40px;
    /* Memberikan ruang di kiri untuk ikon */
    width: 100%;
    height: 40px;
    font-size: 14px;
    border: 1px solid #e0e0e0;
    color: #7d7f81;
    border-radius: 6px;
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

    tr {
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 10px;
        background-color: #fff;
    }

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

    td::before {
        position: absolute;
        top: 10px;
        left: 16px;
        width: 40%;
        padding-right: 10px;
        white-space: normal;
        font-weight: 600;
        color: #555;
        content: attr(data-label);
        text-align: left; /* pastikan label-nya juga rata kiri */
    }

    td:first-child {
        padding-right: 155px;
    }

    td:last-child {
        border-bottom: none;
    }

    .table-header {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        gap: 10px;
    }

    .table-header .add-button {
        align-self: center;
        margin-bottom: 0;
    }
    .info-grid {
        grid-template-columns: repeat(
            2,
            1fr
        ); /* Mobile: 2 kolom supaya padat */
        gap: 30px 20px;
    }

    .layout {
        flex-direction: column;
    }

    .main-content {
        padding: 16px;
    }

    .input-wrapper {
        width: 100%;
    }

    /* Tambahan agar input lebih mudah diketik di mobile */
    .input-wrapper input {
        width: 100%;
        font-size: 14px;
    }
    .dropdown-group {
        flex-direction: column;
    }
}
</style>
