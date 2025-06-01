<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <div class="main-content">
            <HeaderBar title="Informasi Berita Acara Posyandu" />
            <div class="separator"></div>

            <div class="form-container">
                <h3 class="form-title">Informasi Berita Acara Posyandu</h3>
                <div class="card-separator"></div>
                <div class="form-grid" @submit.prevent="handleSubmit">
                    <div class="section">
                        <h4 class="no-border-top">Berita Acara Posyandu</h4>

                        <div class="form-row">
                            <label>Tanggal</label>
                            <input
                                type="date"
                                v-model="formData.tanggal"
                                placeholder="Tanggal Pelaksanaan"
                                readonly
                            />
                        </div>

                        <div class="form-row time-range">
                            <label>Waktu</label>
                            <div class="time-range">
                                <input
                                    type="time"
                                    v-model="formData.waktuStart"
                                    readonly
                                />
                                <span> - </span>
                                <input
                                    type="time"
                                    v-model="formData.waktuEnd"
                                    readonly
                                />
                                <span> WIB</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <label>Kategori</label>
                            <select
                                v-model="formData.kategori"
                                placeholder="Masukkan kategori"
                                disabled
                            >
                                <option value="">Pilih Kategori</option>
                                <option value="Balita">Balita</option>
                                <option value="Bumil">Ibu Hamil</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <label>Judul</label>
                            <input
                                type="text"
                                v-model="formData.judul"
                                placeholder="Judul Acara"
                                readonly
                            />
                        </div>

                        <div class="form-row">
                            <label>Tempat</label>
                            <input
                                type="text"
                                v-model="formData.tempat"
                                placeholder="Tempat Pelaksanaan"
                                readonly
                            />
                        </div>

                        <div class="form-row">
                            <label>Deskripsi</label>
                            <textarea
                                v-model="formData.deskripsi"
                                placeholder="Deskripsi Acara"
                                rows="4"
                                readonly
                            ></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="table-container">
                    <div class="table-header">
                        <h3>Daftar Antrian Acara Imunisasi</h3>
                        <router-link
                            :to="`/antrian-add/${$route.params.id}`"
                            class="add-button"
                            >Tambah Antrian</router-link
                        >
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Nama</th>
                                <th>NIK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(antrian, index) in antrianList"
                                :key="index"
                            >
                                <td>{{ index + 1 }}</td>
                                <td>{{ antrian.nama }}</td>
                                <!-- Tampilkan nama -->
                                <td>{{ antrian.nik }}</td>
                                <!-- Tampilkan nik -->
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
import axios from "axios";

export default {
    name: "BeritaEditComponent",
    components: {
        Sidebar,
        HeaderBar,
    },
    data() {
        return {
            activeMenu: "berita",
            formData: {
                tanggal: "",
                waktuStart: "",
                waktuEnd: "",
                kategori: "",
                judul: "",
                tempat: "",
                deskripsi: "",
            },
            antrianList: [], // ← Tambahkan ini
        };
    },
    mounted() {
        this.fetchAntrian();
        this.fetchBerita();
    },
    methods: {
        formatTanggal(date) {
            const dateObj = new Date(date);
            const year = dateObj.getFullYear();
            const month = (dateObj.getMonth() + 1).toString().padStart(2, "0");
            const day = dateObj.getDate().toString().padStart(2, "0");
            return `${year}-${month}-${day}`;
        },
        formatWaktu() {
            return `${this.formData.waktuStart} - ${this.formData.waktuEnd} WIB`;
        },
        fetchBerita() {
            const id = this.$route.params.id;
            const token = localStorage.getItem("token");

            axios
                .get(`http://localhost:8000/api/auth/berita/${id}`, {
                    headers: { Authorization: `Bearer ${token}` },
                    Accept: "application/json",
                })
                .then((res) => {
                    const data = res.data.data;
                    const [start, end] = (
                        data.waktu || "00:00 - 00:00 WIB"
                    ).split(" - ");
                    this.formData = {
                        tanggal: data.tanggal,
                        waktuStart: start.trim(),
                        waktuEnd: end.replace(" WIB", "").trim(),
                        kategori: data.kategori,
                        judul: data.judul,
                        tempat: data.tempat,
                        deskripsi: data.deskripsi,
                    };
                })
                .catch((err) => {
                    alert("Gagal memuat data.");
                    console.error(err);
                });
        },
        fetchAntrian() {
            const beritaId = this.$route.params.id;
            const token = localStorage.getItem("token");

            axios
                .get(`http://localhost:8000/api/auth/antrian/${beritaId}`, {
                    headers: { Authorization: `Bearer ${token}` },
                    Accept: "application/json",
                })
                .then((res) => {
                    this.antrianList = res.data.data;
                })
                .catch((err) => {
                    alert("Gagal memuat data antrian.");
                    console.error(err);
                });
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

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2px 20px;
    border-bottom: 1px solid #ccc;
}

.table-header h3 {
    font-size: 15px;
    color: #111216;
    font-weight: bold;
}

.container {
    min-height: 30vh;
}

.table-header .add-button {
    font-size: 15px;
    color: #074a5d;
    font-weight: bold;
}

.add-button {
    background-color: transparent;
    text-decoration: none;
    color: #0066cc;
    border: none;
    font-weight: bold;
    cursor: pointer;
}

.layout {
    display: flex;
    min-height: 50vh;
    background-color: #f7f7f7;
}
.form-container {
    background: #fff;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
    margin-top: 16px;
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
    margin: 10px 0 20px 0;
}
.container {
    min-height: 30vh;
}

.table-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 0px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    border: 1px solid #ccc;
    margin-top: 30px;
}

table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    border: 1px solid #ccc;
}

.form-title {
    font-size: 15px;
    color: #074a5d;
    font-weight: bold;
    margin-bottom: 16px;
    margin-top: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
}

.section {
    margin: 35px;
}

.section h4 {
    font-size: 15px;
    font-weight: 500;
    color: #070707;
    text-align: center;
}

.section h4.no-border-top {
    border-top: none;
    padding-bottom: 12px;
}

.time-range {
    display: flex;
    align-items: center;
    gap: 10px;
}

.time-range input {
    width: 100px;
}

.form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 16px;
    align-items: center;
}

.form-row label {
    min-width: 150px;
    margin-right: 8px;
}

.form-row textarea {
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fafafa;
    width: 100%;
    height: auto;
    resize: vertical;
    box-sizing: border-box;
    font-family: inherit;
    font-size: 14px;
}

.form-row input,
.form-row select {
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fafafa;
    width: 100%;
    box-sizing: border-box;
    height: 40px;
}

.form-row label {
    font-weight: 500;
    font-size: 13px;
    color: #070707;
    font-weight: bold;
    text-align: left;
}

.form-row .right-align {
    text-align: right;
}

.justify-end {
    justify-content: flex-end;
}

:deep(input::placeholder),
:deep(select::placeholder) {
    font-size: 13px;
    color: #9ca3af;
}

.card-separator {
    height: 1px;
    width: calc(100% + 47px);
    background-color: #d1d5db;
    margin: 10px 0 20px 0;
    margin-left: -25px;
}

@media (max-width: 768px) {
    .layout {
        flex-direction: column;
    }
    .menu-section {
        flex: 1;
        margin-top: 0;
    }
    .form-row {
        display: flex;
        margin-bottom: 20px;
        gap: 1rem;
    }
    .form-row label {
        min-width: 60px;
    }
    .section {
        margin: 20px;
    }
    .button-row {
        flex-direction: column; /* tombol jadi atas-bawah */
        align-items: center;
        gap: 1rem;
        align-items: stretch; /* agar tombol ikut lebar parent */
        width: 100%;
    }
    .section {
        margin: 0px;
    }
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
</style>
