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
                            <strong>DUSUN</strong>
                            <span class="value">{{
                                anggota ? anggota.nama_dusun : "Loading..."
                            }}</span>
                        </div>
                        <div>
                            <strong>Alamat</strong>
                            <span class="value">{{
                                anggota ? anggota.alamat : "Loading..."
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
                                    type="text"
                                    v-model="formData.tanggalTimbang"
                                    placeholder="Masukkan Tanggal Timbang"
                                />

                                <label class="right-align">Umur</label>
                                <input
                                    type="text"
                                    v-model="formData.umur"
                                    placeholder="Umur"
                                />
                            </div>

                            <div class="form-row">
                                <label>Berat Badan</label>
                                <input
                                    type="text"
                                    v-model="formData.beratBadan"
                                    placeholder="Masukkan Berat Badan"
                                />

                                <label class="right-align"
                                    >Lingkar Kepala</label
                                >
                                <input
                                    type="text"
                                    v-model="formData.lingkarKepala"
                                    placeholder="Masukkan Lingkar Kepala"
                                />
                            </div>

                            <div class="form-row">
                                <label>Tinggi Badan</label>
                                <input
                                    type="text"
                                    v-model="formData.tinggiBadan"
                                    placeholder="Masukkan Acara ID"
                                    disabled
                                />

                                <label class="right-align"
                                    >Lingkar Lengan Atas</label
                                >
                                <input
                                    type="text"
                                    v-model="formData.lingkarLengan"
                                    placeholder="Masukkan Diagnosa"
                                    disabled
                                />
                            </div>

                            <div class="form-row">
                                <label>Acara ID</label>
                                <input
                                    type="text"
                                    v-model="formData.beritaId"
                                    placeholder="Masukkan Tinggi Badan"
                                    disabled
                                />

                                <label class="right-align">Diagnosa</label>
                                <input
                                    type="text"
                                    v-model="formData.Diagnosa"
                                    placeholder="Masukkan Lingkar Lengan Atas"
                                    disabled
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

                            <div class="form-row">
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
                            </div>

                            <div class="form-row">
                                <label>ASI</label>
                                <select
                                    v-model.number="formData.ASI"
                                    style="flex: 3"
                                >
                                    <option :value="1">Ya</option>
                                    <option :value="0">Tidak</option>
                                </select>

                                <label class="right-align">Jenis</label>
                                <input
                                    type="text"
                                    v-model="formData.jenis"
                                    placeholder="Masukkan jenis"
                                />
                            </div>

                            <div class="form-row">
                                <label>VIT</label>
                                <select v-model="formData.vit" style="flex: 3">
                                    <option value="">Pilih VIT A</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label>2T</label>
                                <select v-model="formData.duaT" style="flex: 3">
                                    <option value="">Pilih 2T</option>
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
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
                                    v-model="formData.suplemen"
                                    placeholder="Masukkan suplemen"
                                />
                            </div>
                        </div>

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

export default {
    name: "TambahFormComponent",
    components: {
        Sidebar,
        HeaderBar,
    },
    data() {
        return {
            activeMenu: "pasien",
            anggota: null,
            formData: {
                tanggalTimbang: "",
                umur: "",
                beratBadan: "",
                tinggiBadan: "",
                pmt: "",
                jumlahPemberian: "",
                beritaId: "",
                Diagnosa: "",
                kurun: "",
                waktu: "",
                ASI: "",
                jenis: "",
                vit: "",
                duaT: "",
                imunisasi: "",
                anggota: null,
                ibu: null,
                alamat: null,
            },
        };
    },
    methods: {
        handleSubmit() {
            const id = this.$route.params.nik;
            const payload = {
                nik: id,
                tanggal_timbang: this.formData.tanggalTimbang,
                umur: this.formData.umur,
                berat_badan_kg: this.formData.beratBadan,
                tinggi_badan: this.formData.tinggiBadan,
                pmt: this.formData.pmt,
                jumlah_pemberian: this.formData.jumlahPemberian,
                beritaId: this.formData.beritaId,
                diagnosa: this.formData.Diagnosa,
                kurun: this.formData.kurun,
                waktu: this.formData.waktu,
                ASI: this.formData.ASI,
                jenis: this.formData.jenis,
                vit: this.formData.vit,
                dua_t: this.formData.duaT,
                imunisasi: this.formData.imunisasi,
                anggota: this.formData.anggota,
                ibu: this.formData.ibu,
                alamat: this.formData.alamat,
            };

            axios
                .post("http://localhost:8000/api/auth/pemeriksaan", payload, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                })
                .then((response) => {
                    console.log("Data berhasil dikirim:", response.data);
                    // bisa redirect atau tampilkan notifikasi
                    this.$router.push("/dashboard");
                })
                .catch((error) => {
                    console.error(
                        "Gagal menyimpan data pemeriksaan:",
                        error.response?.data || error
                    );
                });
        },
        handleCancel() {
            console.log("Form dibatalkan");
        },
    },
    mounted() {
        console.log("NIK dari route params:", this.$route.params.nik);
        const id = this.$route.params.nik;
        if (!id) {
            console.error("ID pemeriksaan tidak tersedia di URL!");
            return;
        }

        // Ambil data anggota dan detail pemeriksaan terbaru
        axios
            .get(`http://localhost:8000/api/auth/riwayat_pemeriksaan/${id}`, {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                },
            })
            .then((response) => {
                const data = response.data?.data;
                if (
                    data &&
                    data.pemeriksaan &&
                    data.pemeriksaan.anggota_keluarga
                ) {
                    this.anggota = data.pemeriksaan.anggota_keluarga;
                } else {
                    console.warn("Struktur data tidak sesuai.");
                }
            })
            .catch((error) => {
                console.error("Gagal mengambil data anggota:", error);
            });
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
    justify-content: space-between; /* Membuat elemen terpisah dengan ruang antara */
    align-items: flex-start;
    padding: 0px; /* Menghapus padding untuk meratakan posisi */
    width: 100%;
    font-size: 14px;
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
    border: 1px solid #d1d5db; /* garis luar */
    border-radius: 12px;
    padding: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); /* bayangan halus */
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
    width: 20px; /* Sesuaikan ukuran ikon notifikasi */
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
    flex-direction: column; /* Agar judul dan garis berada dalam satu kolom */
    gap: 8px; /* Memberikan jarak antara judul dan garis */
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
    border-top: 1px solid #d1d5db; /* Garis di bawah setiap bagian judul */
    padding-top: 14px; /* Memberikan ruang antara teks dan garis */
    text-align: center;
}
.section h4.no-border-top {
    border-top: none;
    padding-top: 0;
}

.form-row {
    display: grid;
    justify-content: flex-end;
    grid-template-columns: 150px 1fr 150px 1fr; /* Label dan input kiri-kanan */
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
    height: 40px; /* biar tinggi semua sama */
}
.form-row label {
    font-weight: 500;
    font-size: 13px;
    color: #070707;
    font-weight: bold;
    text-align: left;
}

.form-row .right-align {
    text-align: right; /* Menambahkan kelas khusus untuk label sebelah kanan */
}
.justify-end {
    justify-content: flex-end;
}

.button-row {
    display: flex;
    justify-content: space-between; /* Tombol di kiri dan kanan */
    gap: 10px; /* Jarak antar tombol */
    width: 100%; /* Pastikan lebar tombol 100% */
}

.btn-cancel,
.btn-submit {
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    width: 12%; /* Agar tombol sama besar */
    height: 40px; /* Tinggi tombol seragam */
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
    gap: 10px; /* Memberikan jarak antar tombol */
}
:deep(input::placeholder),
:deep(select::placeholder) {
    font-size: 13px;
    color: #9ca3af; /* Warna placeholder, opsional */
}
.card-separator {
    height: 1px;
    width: calc(100% + 47px); /* tambahkan sesuai padding parent */
    background-color: #d1d5db;
    margin: 10px 0 20px 0;
    margin-left: -25px; /* geser ke kiri sejauh padding parent */
}

@media (max-width: 768px) {
    .layout {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
        flex-direction: row;
        overflow-x: auto;
    }

    .menu-section {
        flex: 1;
        margin-top: 0;
    }

    .main-content {
        padding: 16px;
    }

    .sidebar-footer {
        display: none; /* Atau ubah tampilannya jadi floating button misalnya */
    }
}
</style>
