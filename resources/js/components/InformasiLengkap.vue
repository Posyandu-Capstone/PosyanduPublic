<template>
    <div class="layout">
        <!-- Sidebar -->
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <!-- Main Content -->
        <div class="main-content">
            <HeaderBar title="Data Anggota" />

            <div class="separator"></div>

            <div class="form-container">
                <h3 class="form-title">Formulir Pendaftaran Anggota</h3>
                <div class="card-separator"></div>

                <form class="form-grid" @submit.prevent="handleSubmit">
                    <!-- Data Diri -->
                    <div class="section">
                        <h4 class="no-border-top">Data Diri</h4>
                        <div class="form-row">
                            <label>Nama Lengkap</label>
                            <input
                                type="text"
                                v-model="formData.nama"
                                disabled
                                placeholder="Masukkan Nama Lengkap"
                                id="nama"
                                required
                            />
                            <label class="right-align">Jenis Kelamin</label>
                            <select v-model="formData.jenisKelamin" disabled>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label>Tanggal Lahir</label>
                            <input
                                type="date"
                                v-model="formData.tanggalLahir"
                                disabled
                            />

                            <label class="right-align">No KK</label>
                            <input
                                type="text"
                                v-model="formData.noKK"
                                disabled
                                placeholder="Masukkan No KK"
                            />
                        </div>
                        <div class="form-row">
                            <label>NIK</label>
                            <input
                                type="text"
                                v-model="formData.nik"
                                disabled
                                placeholder="Masukkan NIK"
                            />
                            <label class="right-align">Anak Ke</label>
                            <input
                                v-model="formData.anakKe"
                                type="number"
                                disabled
                            />
                        </div>
                    </div>

                    <!-- Data Kesehatan -->
                    <div class="section">
                        <h4>Data Kesehatan</h4>
                        <div class="form-row">
                            <label>Tinggi Lahir (cm)</label>
                            <input
                                type="text"
                                v-model="formData.tinggiLahir"
                                disabled
                                placeholder="Masukkan Tinggi Lahir"
                            />
                            <label class="right-align">Berat Lahir</label>
                            <input
                                type="text"
                                v-model="formData.beratLahir"
                                disabled
                                placeholder="Masukkan Berat Lahir"
                            />
                        </div>
                        <div class="form-row">
                            <label>Penyakit Bawaan</label>
                            <input
                                type="text"
                                v-model="formData.penyakitBawaan"
                                disabled
                                placeholder="Masukkan Penyakit Bawaan"
                                style="flex: 3"
                            />
                        </div>
                    </div>

                    <!-- Data Wali -->
                    <div class="section">
                        <h4>Data Wali</h4>
                        <div class="form-row">
                            <label>Nama Ayah</label>
                            <input
                                type="text"
                                v-model="formData.namaAyah"
                                disabled
                                placeholder="Masukkan Nama Ayah"
                            />
                            <label class="right-align">NIK Ayah</label>
                            <input
                                type="text"
                                v-model="formData.nikAyah"
                                disabled
                                placeholder="Masukkan NIK Ayah"
                            />
                        </div>
                        <div class="form-row">
                            <label>Nama Ibu</label>
                            <input
                                type="text"
                                v-model="formData.namaIbu"
                                disabled
                                placeholder="Masukkan Nama Ibu"
                            />
                            <label class="right-align">NIK Ibu</label>
                            <input
                                type="text"
                                v-model="formData.nikIbu"
                                disabled
                                placeholder="Masukkan NIK Ibu"
                            />
                        </div>
                        <div class="form-row">
                            <label>Nomor Handphone</label>
                            <input
                                type="text"
                                v-model="formData.noHP"
                                disabled
                                placeholder="Masukkan Nomor Handphone"
                            />
                            <label class="right-align">Alamat</label>
                            <input
                                type="text"
                                v-model="formData.rtrw"
                                disabled
                                placeholder="Masukkan Alamat"
                            />
                        </div>
                        <div class="form-row">
                            <label>Dusun</label>
                            <input
                                type="text"
                                v-model="formData.dusun"
                                disabled
                                placeholder="Masukkan Dusun"
                                style="flex: 3"
                            />
                        </div>
                    </div>

                    <!-- Data Tambahan -->
                    <div class="section">
                        <h4>Data Tambahan</h4>
                        <div class="form-row">
                            <label>BPJS</label>
                            <select
                                v-model="formData.bpjs"
                                disabled
                                id="bpjs"
                                class="inputan"
                            >
                                <option value="">Pilih BPJS</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                            <label class="right-align">GAKIN</label>
                            <select
                                v-model="formData.gakin"
                                disabled
                                id="gakin"
                                class="inputan"
                            >
                                <option value="">Pilih GAKIN</option>
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
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

                        <!-- <button
                            v-if="!isEditing"
                            type="button"
                            class="btn-submit"
                            @click="goToEdit(formData.nik)"
                        >
                            Ubah
                        </button>

                        
                        <button v-else type="submit" class="btn-submit">
                            Simpan
                        </button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import axios from "axios";

export default {
    name: "InformasiLengkapComponent",
    components: { Sidebar, HeaderBar },
    data() {
        return {
            activeMenu: "pasien",
            formData: {
                nama: "",
                jenisKelamin: "",
                tanggalLahir: "",
                noKK: "",
                nik: "",
                anakKe: "",
                tinggiLahir: "",
                beratLahir: "",
                penyakitBawaan: "",
                namaAyah: "",
                nikAyah: "",
                namaIbu: "",
                nikIbu: "",
                noHP: "",
                rtrw: "",
                dusun: "",
                bpjs: "",
                gakin: "",
            },
        };
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        // goToEdit(id) {
        //     this.$router.push(`/editanggota/${id}`);
        // },
        sanitizeNumber(value) {
            return value !== "" ? Number(value) : null;
        },
        safeToString(value) {
            return value != null && value !== undefined ? value.toString() : "";
        },
        formatDateForInput(date) {
            const [year, month, day] = date.split("-");
            return `${year}-${month}-${day}`;
        },
        fetchData() {
            const token = localStorage.getItem("token");
            const nik = this.$route.params.id;

            if (nik && token) {
                axios
                    .get(
                        `http://localhost:8000/api/auth/laporan-kesehatan/${nik}`,
                        {
                            headers: { Authorization: `Bearer ${token}` },
                        }
                    )
                    .then((response) => {
                        if (response.data.status === "success") {
                            const data = response.data.data;
                            this.formData = {
                                nama: data.nama_lengkap || "",
                                jenisKelamin: data.jenis_kelamin || "",
                                tanggalLahir: data.tanggal_lahir
                                    ? this.formatDateForInput(
                                          data.tanggal_lahir
                                      )
                                    : "",
                                noKK: data.no_kk || "",
                                nik: data.nik || "",
                                anakKe: Number(data.Anak_Ke) || 1,
                                tinggiLahir: this.safeToString(
                                    data.tinggi_lahir
                                ),
                                beratLahir: this.safeToString(data.berat_lahir),
                                penyakitBawaan: data.penyakit_bawaan || "",
                                namaAyah: data.nama_ayah || "",
                                nikAyah: data.nik_ayah || "",
                                namaIbu: data.nama_ibu || "",
                                nikIbu: data.nik_ibu || "",
                                noHP: data.nomor_telp || "",
                                rtrw: data.alamat || "",
                                dusun: data.nama_dusun || "",
                                bpjs: data.bpjs === 1 ? "Ya" : "Tidak",
                                gakin: data.gakin === 1 ? "Ya" : "Tidak",
                            };
                        } else {
                            alert("Data anggota tidak ditemukan.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("Terjadi kesalahan saat mengambil data.");
                    });
            } else {
                alert("Token tidak ada atau NIK tidak valid.");
            }
        },
        handleCancel() {
            this.$router.go(-1);
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
    .form-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
        /* Jarak antara label dan input */
        margin-bottom: 20px;
    }

    .form-row label {
        text-align: left !important;
        /* Pastikan label selalu di kiri */
        width: 100%;
        /* Ambil seluruh lebar container */
    }

    .btn-cancel,
    .btn-submit {
        width: 100%;
    }

    .button-row {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }
}
</style>
