<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <div class="main-content">
            <HeaderBar title="Laporan Kesehatan" />

            <div class="separator"></div>

            <div class="container">
                <!-- Tabel Data Laporan -->
                <div class="table-container">
                    <div class="table-header">
                        <h3>Data Rekapitulasi Kesehatan Warga</h3>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Posko Posyandu</th>
                                <th>Sasaran</th>
                                <th>Datang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(laporan, index) in laporanList"
                                :key="index"
                            >
                                <td>{{ index + 1 }}</td>
                                <td>{{ laporan.nama_posyandu }}</td>
                                <td>{{ laporan.sasaran }}</td>
                                <td>{{ laporan.datang }}</td>
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
import HeaderBar from "@/components/HeaderBar.vue";
import informasiIcon from "@/assets/info.svg";
import updateIcon from "@/assets/edit.svg";
import axios from "axios";

export default {
    name: "DataLaporanKesehatan",
    components: { Sidebar, HeaderBar },
    data() {
        return {
            activeMenu: "laporan",
            selectedMonth: "",
            laporanList: [],
            informasiIcon,
            updateIcon,
        };
    },
    methods: {
        updateActiveMenu(newMenu) {
            this.activeMenu = newMenu;
        },
        async fetchLaporanKesehatan() {
            try {
                const token = localStorage.getItem("token");
                const response = await axios.get(
                    "http://localhost:8000/api/auth/laporan",
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                this.laporanList = response.data.data;
                console.log(
                    "URL endpoint yang dipanggil:",
                    response.config.url
                );
                console.log("Response data utuh:", response.data);
            } catch (error) {
                console.error("Gagal mengambil data laporan:", error);
            }
        },
        handleChange() {
            console.log("Bulan dipilih:", this.selectedMonth);
        },
    },
    mounted() {
        this.fetchLaporanKesehatan();
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

.separator {
    border-bottom: 1px solid #e5e7eb;
    margin: 20px 0 20px 0;
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
    text-decoration: none;
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
