<template>
    <div class="layout">
        <!-- Sidebar -->
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <div class="main-content">
            <HeaderBar title="Data Anggota" />
            <div class="separator"></div>

            <div class="container">
                <!-- Informasi Anggota -->
                <div class="card info-card">
                    <div class="table-header">
                        <h3>Informasi Anggota</h3>
                    </div>
                    <div class="card-separator"></div>
                    <div class="info-grid">
                        <div>
                            <strong>NAMA LENGKAP</strong>
                            <span class="value">{{
                                anggota
                                    ? anggota.nama_anggota_keluarga
                                    : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>JENIS KELAMIN</strong>
                            <span class="value">{{
                                anggota ? anggota.jenis_kelamin : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>TANGGAL LAHIR</strong>
                            <span class="value">{{
                                anggota ? anggota.tanggal_lahir : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>ANAK KE</strong>
                            <span class="value">{{
                                anggota ? anggota.Anak_Ke : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>NAMA IBU</strong>
                            <span class="value">{{
                                anggota ? anggota.nama_ibu : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>ALAMAT</strong>
                            <span class="value">{{
                                alamat || "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>GAKIN</strong>
                            <span class="value">{{
                                anggota
                                    ? anggota.gakin === 1
                                        ? "Ya"
                                        : "Tidak"
                                    : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>BPJS</strong>
                            <span class="value">{{
                                anggota
                                    ? anggota.bpjs === 1
                                        ? "Ya"
                                        : "Tidak"
                                    : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>PENYAKIT BAWAAN</strong>
                            <span class="value">{{
                                anggota ? anggota.penyakit_bawaan : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>TINGGI LAHIR</strong>
                            <span class="value">{{
                                anggota ? anggota.tinggi_lahir : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>BB LAHIR</strong>
                            <span class="value">{{
                                anggota ? anggota.berat_lahir : "Loading..."
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Pemeriksaan -->
                <div class="card table-card">
                    <div class="table-header">
                        <h3>Data Riwayat Pemeriksaan</h3>
                        <button title="Tambah" @click="goToFormTambah(anggota)">
                            Tambah Pemeriksaan
                        </button>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tgl Periksa</th>
                                <th>Berat Badan</th>
                                <th>Tinggi Badan</th>
                                <th>BB/U</th>
                                <th>TB/U</th>
                                <th>BB/TB</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="riwayatPemeriksaan.length === 0">
                                <td colspan="8" style="text-align: center">
                                    Tidak ada data.
                                </td>
                            </tr>
                            <tr
                                v-for="(item, index) in riwayatPemeriksaan"
                                :key="item.id || index"
                            >
                                <td :data-label="'No'">{{ index + 1 }}</td>
                                <td :data-label="'Tanggal Timbang'">
                                    {{
                                        item.tanggal_timbang || "Silahkan diisi"
                                    }}
                                </td>
                                <td :data-label="'Berat Badan (kg)'">
                                    {{
                                        item.berat_badan_kg || "Silahkan diisi"
                                    }}
                                </td>
                                <td :data-label="'Tinggi Badan (cm)'">
                                    {{ tampilkanNilai(item.tinggi_badan) }}
                                </td>
                                <td :data-label="'BB/U'">
                                    {{ item.bb_u || "Silahkan diisi" }}
                                </td>
                                <td :data-label="'TB/U'">
                                    {{ item.tb_u || "Silahkan diisi" }}
                                </td>
                                <td :data-label="'BB/TB'">
                                    {{ item.bb_tb || "Silahkan diisi" }}
                                </td>
                                <td :data-label="'Aksi'" class="aksi">
                                    <button
                                        title="Informasi"
                                        @click="lihatInformasiLengkap(item)"
                                    >
                                        <img
                                            :src="informasiIcon"
                                            alt="Informasi"
                                        />
                                    </button>
                                    <button
                                        v-if="canEdit"
                                        title="Edit"
                                        @click="editAnggota(item)"
                                    >
                                        <img :src="updateIcon" alt="Edit" />
                                    </button>

                                    <button
                                        title="Hapus"
                                        @click="openConfirmModal(item)"
                                    >
                                        <img :src="deleteIcon" alt="Delete" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <SuccessAlert :visible="showSuccessAlert" :message="successMessage" />

        <ModalConfirm
            :visible="showConfirmModal"
            title="Konfirmasi Hapus"
            message="Apakah Anda yakin ingin menghapus data ini?"
            @cancel="cancelDelete"
            @confirm="confirmDelete"
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
    name: "RiwayatPasienComponent",
    components: {
        Sidebar,
        HeaderBar,
        ModalConfirm,
        SuccessAlert,
    },
    data() {
        return {
            activeMenu: "pasien",
            userRole: localStorage.getItem("userRole") || null,
            informasiIcon,
            updateIcon,
            deleteIcon,
            riwayatPemeriksaan: [],
            anggota: null,
            alamat: "",
            userId: null,
            showConfirmModal: false,
            itemToDelete: null,
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    computed: {
        canEdit() {
            return this.userRole === "Kader";
        },
    },
    mounted() {
        this.userRole = localStorage.getItem("userRole");
        const id = this.$route.params.nik;
        if (!id) {
            console.error("NIK tidak ditemukan di route params!");
            return;
        }

        // Ambil data anggota
        axios
            .get(
                `https://capstonesi.online/api/auth/riwayat_pemeriksaan/${id}`,
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                }
            )
            .then((response) => {
                const data = response.data?.data;
                if (data?.pemeriksaan?.anggota_keluarga) {
                    this.anggota = data.pemeriksaan.anggota_keluarga;
                    this.userId = data.user_id;
                    this.alamat = data.alamat || "";
                    console.log("User ID disimpan:", this.userId);
                    console.log("Data anggota:", this.anggota);
                    console.log("Alamat:", this.alamat);
                } else {
                    console.warn("Struktur data tidak sesuai.");
                }
            })
            .catch((error) => {
                console.error("Gagal mengambil data anggota:", error);
            });

        // Ambil data riwayat pemeriksaan
        axios
            .get(`https://capstonesi.online/api/auth/pemeriksaan/${id}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            })
            .then((res) => {
                console.log("Respons API:", res);
                this.riwayatPemeriksaan = Array.isArray(res.data.data)
                    ? res.data.data
                    : [];
            })
            .catch((err) => {
                console.error("Gagal memuat riwayat pemeriksaan:", err);
            });
    },
    methods: {
        tampilkanNilai(value) {
            return value !== null && value !== "" ? value : "Silahkan diisi";
        },
        lihatInformasiLengkap(item) {
            console.log("Item yang diklik:", item); // DEBUG
            if (this.anggota?.nik) {
                const pemeriksaanId = item.id;
                this.$router.push(
                    `/formpemeriksaan/${this.anggota.nik}/${pemeriksaanId}`
                );
            } else {
                alert("NIK tidak ditemukan.");
            }
        },
        editAnggota(item) {
            if (this.anggota?.nik) {
                const pemeriksaanId = item.id;
                this.$router.push(
                    `/updatepemeriksaan/${this.anggota.nik}/${pemeriksaanId}`
                );
            } else {
                alert("NIK tidak ditemukan untuk edit.");
            }
        },
        goToFormTambah(anggota) {
            if (anggota?.nik && this.userId) {
                this.$router.push({
                    name: "antrian",
                    params: { nik: anggota.nik },
                    query: { userId: this.userId },
                });
            } else {
                alert("Data anggota tidak valid");
            }
        },
        openConfirmModal(item) {
            this.itemToDelete = item; // simpan data yang ingin dihapus
            this.showConfirmModal = true; // tampilkan modal
        },
        cancelDelete() {
            this.showConfirmModal = false; // sembunyikan modal
            this.itemToDelete = null;
        },
        confirmDelete() {
            if (!this.itemToDelete) return;

            axios
                .delete(
                    `https://capstonesi.online/api/auth/pemeriksaan/${this.itemToDelete.id}`,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                )
                .then(() => {
                    this.riwayatPemeriksaan = this.riwayatPemeriksaan.filter(
                        (item) => item.id !== this.itemToDelete.id
                    );
                    this.showConfirmModal = false;
                    this.itemToDelete = null;

                    this.successMessage =
                        "Riwayat pemeriksaan berhasil dihapus.";
                    // Tampilkan alert sukses
                    this.showSuccessAlert = true;

                    // Jika mau hilangkan alert setelah beberapa detik, bisa:
                    setTimeout(() => {
                        this.showSuccessAlert = false;
                    }, 3000); // 3 detik
                })
                .catch((err) => {
                    console.error("Gagal hapus data:", err);
                    alert("Gagal menghapus data");
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
    width: 100%;
    padding: 0;
    font-family: "Inter", sans-serif;
}

.info-card {
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
    align-items: center; /* ubah flex-start jadi center supaya sejajar vertikal */
    padding: 0px;
    width: 100%;
    font-size: 14px;
}

.table-header button {
    position: relative;
    top: 7px; /* geser 5px ke bawah */
    background: transparent; /* transparan sesuai request sebelumnya */
    border: none;
    border-radius: 5px;
    padding: 5px 25px; /* kasih padding lebih buat teks */
    cursor: pointer;
    color: #074a5d;
    font-weight: bold;
    font-size: 12px;
}

.table-header h3 {
    font-size: 14px;
    color: #111216;
    font-weight: bold;
    line-height: 1.1;
    margin: 0; /* Menghapus margin agar h3 lebih rata kiri dengan elemen lain */
}

.table-header .add-button {
    color: #074a5d;
    font-weight: bold;
    margin-left: 10px; /* Memberikan jarak antar elemen */
    text-decoration: none;
}

.card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    border: 1px solid #e5e5e5;
}
.card-separator {
    height: 1px;
    width: calc(100% + 40px);
    background-color: #e5e5e5;
    margin: 10px 0 20px 0;
    margin-left: -20px; /* Geser ke kiri sejauh padding card */
}

.table-card {
    padding: 0;
    height: 100%; /* Pastikan card mengisi seluruh tinggi container */
}
.info-card h3 {
    margin-top: 5px;
    color: #070707;
    font-size: 15px;
}

.table-card h3 {
    margin-top: 20px;
    font-size: 15px;
    color: #070707;
    padding-left: 20px;
}

.table-card table {
    width: 100%;
    margin-top: 10px;
    border-collapse: collapse;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px 20px;
    font-size: 14px;
}
.info-grid div strong {
    display: block;
    margin-bottom: 7px; /* opsional, buat jarak antar label dan isi */
    font-weight: 600;
    color: #111827;
}
.info-grid .value {
    color: #333436;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

thead th {
    background-color: #fafafa;
    color: #7d7f81;
    font-weight: 600;
    padding: 8px;
    text-align: center;
}

/* Selang-seling warna baris */
tbody tr:nth-child(odd) td {
    background-color: #ffffff;
    text-align: center; /* Menengahkan teks sel */
    vertical-align: middle;
    color: #000000;
}

tbody tr:nth-child(even) td {
    background-color: #f9fafb;
    text-align: center; /* Menengahkan teks sel */
    vertical-align: middle;
}

/* Styling tambahan opsional */
td,
th {
    padding: 8px;
    border: 1px solid #d1d5db;
}

.aksi button {
    background: none;
    border: none;
    margin-right: 5px;
    font-size: 16px;
    cursor: pointer;
}

.aksi button img {
    width: 20px; /* Menyesuaikan ukuran ikon */
    height: 20px; /* Menyesuaikan ukuran ikon */
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
        display: block;
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
}
</style>
