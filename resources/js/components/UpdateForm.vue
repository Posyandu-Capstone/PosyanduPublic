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

                <!-- Formulir Pemeriksaan -->
                <div class="form-container">
                    <h3 class="form-title">Formulir Pemeriksaan</h3>
                    <div class="card-separator"></div>

                    <form class="form-grid" @submit.prevent="handleSubmit">
                        <!-- Informasi Pemeriksaan -->
                        <div class="section">
                            <h4 class="no-border-top">Informasi Pemeriksaan</h4>
                            <div class="form-row">
                                <label>Tanggal Timbang</label>
                                <input
                                    type="date"
                                    v-model="formData.tanggalTimbang"
                                />

                                <label class="right-align">Berat Badan</label>
                                <input
                                    type="text"
                                    v-model="formData.beratBadan"
                                    placeholder="Masukkan Berat Badan"
                                />
                            </div>

                            <div class="form-row">
                                <label>Tinggi Badan</label>
                                <input
                                    type="text"
                                    v-model="formData.tinggiBadan"
                                    placeholder="Masukkan Tinggi Badan"
                                />
                            </div>
                        </div>

                        <!-- Asupan Gizi -->
                        <div class="section">
                            <h4>Asupan Gizi</h4>
                            <div class="form-row">
                                <label>Pemberian Makanan Tambahan</label>
                                <input
                                    type="text"
                                    v-model="formData.pmt"
                                    placeholder="Jenis makanan"
                                />

                                <label class="right-align"
                                    >Jumlah Pemberian</label
                                >
                                <input
                                    type="text"
                                    v-model="formData.jumlahPemberian"
                                    placeholder="Masukkan jumlah"
                                />
                            </div>

                            <!-- <div class="form-row">
                                <label>Kurun</label>
                                <input
                                    type="text"
                                    v-model="formData.kurun"
                                    placeholder="Masukkan kurun waktu"
                                />

                                <label class="right-align">Waktu</label>
                                <input
                                    type="text"
                                    v-model="formData.waktu"
                                    placeholder="Masukkan waktu"
                                />
                            </div> -->

                            <div class="form-row">
                                <label>ASI</label>
                                <select
                                    v-model.number="formData.ASI"
                                    style="flex: 3"
                                >
                                    <option :value="true">Ya</option>
                                    <option :value="false">Tidak</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label>VIT</label>
                                <select v-model="formData.vit" style="flex: 3">
                                    <option value="">Pilih VIT</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                        </div>

                        <!-- Imunisasi dan Suplemen -->
                        <div class="section">
                            <h4>Imunisasi dan Suplemen</h4>
                            <div class="form-row">
                                <label>Imunisasi</label>
                                <input
                                    type="text"
                                    v-model="formData.imunisasi"
                                    placeholder="Jenis imunisasi"
                                />

                                <label class="right-align"
                                    >Suplemen Tambahan</label
                                >
                                <input
                                    type="text"
                                    v-model="formData.Suplemen"
                                    placeholder="Masukkan suplemen"
                                />
                            </div>
                        </div>

                        <SuccessAlert
                            :visible="showSuccessAlert"
                            :message="successMessage"
                        />

                        <!-- Tombol -->
                        <div class="button-row">
                            <button
                                type="button"
                                class="btn-cancel"
                                @click="handleCancel"
                            >
                                Batal
                            </button>
                            <button type="submit" class="btn-submit">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import axios from "axios";
import SuccessAlert from "@/components/SuccessAlert.vue";

export default {
    name: "UpdatePemeriksaanComponent",
    components: {
        Sidebar,
        HeaderBar,
        SuccessAlert,
    },
    data() {
        return {
            activeMenu: "pasien",
            anggota: null,
            alamat: "",
            showSuccessAlert: false,
            successMessage: "",
            formData: {
                tanggalTimbang: "",
                beratBadan: "",
                tinggiBadan: "",
                pmt: "",
                jumlahPemberian: "",
                kurun: "",
                waktu: "",
                ASI: "",
                vit: "",
                imunisasi: "",
                Suplemen: "",
                anggota: null,
                ibu: null,
                alamat: "",
            },
        };
    },
    mounted() {
        const id = this.$route.params.nik;
        const pemeriksaanId = this.$route.params.id;

        if (!id || !pemeriksaanId) {
            console.error("ID pemeriksaan tidak tersedia di URL!");
            return;
        }

        // Ambil detail pemeriksaan
        axios
            .get(
                `http://localhost:8000/api/auth/pemeriksaan_balita/${pemeriksaanId}`,
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                }
            )
            .then((response) => {
                console.log("Data response:", response.data);
                const detail = response.data?.data;

                if (detail) {
                    this.formData = {
                        idPemeriksaan: detail.id,
                        tanggalTimbang: detail.tanggal_timbang || "",
                        beratBadan: detail.berat_badan_kg || "",
                        tinggiBadan: detail.tinggi_badan || "",
                        pmt: detail.PMT || "",
                        jumlahPemberian: detail.total_PMT || "",
                        ASI: detail.ASI ?? "",
                        vit: detail.vit || "",
                        imunisasi: detail.imunisasi || "",
                        Suplemen: detail.Suplemen || "",
                    };
                }
            })
            .catch((error) => {
                console.error("Gagal mengambil data pemeriksaan:", error);
            });

        // Ambil data anggota
        axios
            .get(`http://localhost:8000/api/auth/riwayat_pemeriksaan/${id}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            })
            .then((response) => {
                const data = response.data?.data;
                if (data?.pemeriksaan?.anggota_keluarga) {
                    this.anggota = data.pemeriksaan.anggota_keluarga;
                    this.alamat = data.alamat || "";
                    console.log("Alamat:", this.alamat);
                }
            })
            .catch((error) => {
                console.error("Gagal mengambil data anggota:", error);
            });
    },
    methods: {
        parseTanggal(tanggal) {
            // Jika format input 'dd/mm/yyyy'
            const parts = tanggal.split("/");
            if (parts.length === 3) {
                const [dd, mm, yyyy] = parts;
                return `${yyyy}-${mm.padStart(2, "0")}-${dd.padStart(2, "0")}`; // jadi 'yyyy-mm-dd'
            }
            return tanggal; // fallback, kalau bukan dd/mm/yyyy
        },
        validateForm() {
            const fields = [
                "tanggalTimbang",
                "beratBadan",
                "tinggiBadan",
                "pmt",
                "jumlahPemberian",
                "ASI",
                "vit",
                "imunisasi",
                "Suplemen",
            ];

            for (const field of fields) {
                const value = this.formData[field];
                if (value === null || value === undefined || value === "") {
                    alert(`Field '${field}' harus diisi!`);
                    return false;
                }
            }

            return true;
        },

        handleSubmit() {
            if (!this.validateForm()) {
                return;
            }

            const id = this.formData.idPemeriksaan;
            if (!id) {
                alert("ID Pemeriksaan tidak ditemukan!");
                return;
            }

            const tanggalFormatted = this.parseTanggal(
                this.formData.tanggalTimbang
            );
            if (!tanggalFormatted || tanggalFormatted.trim() === "") {
                alert(
                    "Tanggal timbang tidak boleh kosong atau format tidak valid!"
                );
                return;
            }

            const asiBoolean =
                this.formData.ASI === "true" || this.formData.ASI === true;

            let umur = null;
            if (this.anggota && this.anggota.tanggal_lahir) {
                const today = new Date();
                const birth = new Date(this.anggota.tanggal_lahir);
                umur = today.getFullYear() - birth.getFullYear();
                const m = today.getMonth() - birth.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                    umur--;
                }
            }

            axios
                .patch(
                    `http://localhost:8000/api/auth/pemeriksaan/${id}`,
                    {
                        tanggal_periksa: tanggalFormatted,
                        berat_badan: this.formData.beratBadan,
                        tinggi_badan: this.formData.tinggiBadan,
                        PMT: this.formData.pmt,
                        total_PMT: this.formData.jumlahPemberian,
                        ASI: asiBoolean,
                        vit: this.formData.vit,
                        imunisasi: this.formData.imunisasi,
                        Suplemen: this.formData.Suplemen || "",
                        umur: umur,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                )
                .then((response) => {
                    console.log("Berhasil update pemeriksaan:", response.data);
                    this.showModal = false;

                    // ✅ Tampilkan success alert
                    this.successMessage =
                        "Pemeriksaan Kesehatan Berhasil Diupdate";
                    this.showSuccessAlert = true;

                    // ✅ Tutup alert otomatis setelah 3 detik (atau waktu fade animasi kamu)
                    setTimeout(() => {
                        this.showSuccessAlert = false;
                        this.$router.back();
                    }, 3000); // 3 detik
                })
                .catch((error) => {
                    console.error("Gagal update:", error);
                });
        },
        handleCancel() {
            this.$router.back();
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
    /* Membuat elemen terpisah dengan ruang antara */
    align-items: flex-start;
    padding: 0px;
    /* Menghapus padding untuk meratakan posisi */
    width: 100%;
    font-size: 14px;
}

.table-header h3 {
    font-size: 14px;
    color: #111216;
    font-weight: bold;
    line-height: 1.1;
    margin: 0;
    /* Menghapus margin agar h3 lebih rata kiri dengan elemen lain */
}

.table-header .add-button {
    color: #074a5d;
    font-weight: bold;
    margin-left: 10px;
    /* Memberikan jarak antar elemen */
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
    margin-left: -20px;
    /* Geser ke kiri sejauh padding card */
}

.table-card {
    padding: 0;
    height: 100%;
    /* Pastikan card mengisi seluruh tinggi container */
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
    margin-bottom: 7px;
    /* opsional, buat jarak antar label dan isi */
    font-weight: 600;
    color: #111827;
}

.info-grid .value {
    color: #333436;
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

.form-container {
    background: #fff;
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
    margin-top: 16px;
}

.form-title {
    font-size: 15px;
    color: #074a5d;
    font-weight: bold;
    margin-bottom: 16px;
    margin-top: 0;
    display: flex;
    flex-direction: column;
    /* Agar judul dan garis berada dalam satu kolom */
    gap: 8px;
    /* Memberikan jarak antara judul dan garis */
    width: 100%;
}

.section {
    margin-bottom: 35px;
}

.section h4 {
    font-size: 15px;
    font-weight: 500;
    color: #070707;
    font-weight: bold;
    margin-bottom: 12px;
    border-top: 1px solid #d1d5db;
    /* Garis di bawah setiap bagian judul */
    padding-top: 14px;
    /* Memberikan ruang antara teks dan garis */
    text-align: center;
}

.section h4.no-border-top {
    border-top: none;
    padding-top: 0;
}

.form-row {
    display: grid;
    justify-content: flex-end;
    grid-template-columns: 150px 1fr 150px 1fr;
    /* Label dan input kiri-kanan */
    gap: 16px;
    width: 100%;
    margin-bottom: 16px;
    align-items: center;
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
    /* biar tinggi semua sama */
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
    /* Menambahkan kelas khusus untuk label sebelah kanan */
}

.justify-end {
    justify-content: flex-end;
}

.button-row {
    display: flex;
    justify-content: space-between;
    /* Tombol di kiri dan kanan */
    gap: 10px;
    /* Jarak antar tombol */
    width: 100%;
    /* Pastikan lebar tombol 100% */
}

.btn-cancel,
.btn-submit {
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    width: 12%;
    /* Agar tombol sama besar */
    height: 40px;
    /* Tinggi tombol seragam */
}

.btn-cancel {
    background: white;
    border: 1px solid #074a5d;
    color: #074a5d;
}

.btn-submit {
    background: #074a5d;
    color: white;
}

/* Untuk memastikan tombol berada di kiri dan kanan */
.button-container {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    /* Memberikan jarak antar tombol */
}

:deep(input::placeholder),
:deep(select::placeholder) {
    font-size: 13px;
    color: #9ca3af;
    /* Warna placeholder, opsional */
}

.card-separator {
    height: 1px;
    width: calc(100% + 47px);
    /* tambahkan sesuai padding parent */
    background-color: #d1d5db;
    margin: 10px 0 20px 0;
    margin-left: -25px;
    /* geser ke kiri sejauh padding parent */
}

@media (max-width: 768px) {
    .layout {
        flex-direction: column;
    }

    .menu-section {
        flex: 1;
        margin-top: 0;
    }

    .main-content {
        padding: 16px;
    }

    .form-row {
        grid-template-columns: 1fr; /* 1 kolom full width */
        gap: 12px;
    }

    /* Label dan input tumpuk jadi blok */
    .form-row label {
        text-align: left;
        grid-column: 1;
        font-weight: 600;
    }
    .form-row input,
    .form-row select {
        width: 100%;
        grid-column: 1;
        height: 40px;
    }

    /* Kalau ada kelas khusus misal right-align, override supaya tetap kiri */
    .form-row .right-align {
        text-align: left !important;
    }
    .info-grid {
        grid-template-columns: repeat(
            2,
            1fr
        ); /* Mobile: 2 kolom supaya padat */
        gap: 30px 20px;
    }

    .btn-cancel,
    .btn-submit {
        width: 100%; /* lebar penuh */
        margin-bottom: 10px; /* jarak antar tombol */
    }

    .button-container {
        flex-direction: column; /* tumpuk vertikal */
        gap: 10px;
    }
}
</style>
