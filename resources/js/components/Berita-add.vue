<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <div class="main-content">
            <HeaderBar title="Tambah Berita Acara Posyandu" />
            <div class="separator"></div>

            <div class="form-container">
                <h3 class="form-title">
                    Formulir Tambah Berita Acara Posyandu
                </h3>
                <div class="card-separator"></div>
                <form class="form-grid" @submit.prevent="handleSubmit">
                    <div class="section">
                        <h4 class="no-border-top">Berita Acara Posyandu</h4>

                        <div class="form-row">
                            <label>Tanggal</label>
                            <input
                                type="date"
                                v-model="formData.tanggal"
                                placeholder="Tanggal Pelaksanaan"
                                required
                            />
                        </div>

                        <div class="form-row time-range">
                            <label>Waktu</label>
                            <div class="time-range">
                                <input
                                    type="time"
                                    v-model="formData.waktuStart"
                                    required
                                />
                                <span> - </span>
                                <input
                                    type="time"
                                    v-model="formData.waktuEnd"
                                    required
                                />
                                <span> WIB</span>
                            </div>
                        </div>

                        <div class="form-row">
                            <label>Kategori</label>
                            <select
                                v-model="formData.kategori"
                                placeholder="Masukkan kategori"
                                required
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
                                required
                            />
                        </div>

                        <div class="form-row">
                            <label>Tempat</label>
                            <input
                                type="text"
                                v-model="formData.tempat"
                                placeholder="Tempat Pelaksanaan"
                                required
                            />
                        </div>

                        <div class="form-row">
                            <label>Deskripsi</label>
                            <textarea
                                v-model="formData.deskripsi"
                                placeholder="Deskripsi Acara"
                                rows="4"
                                required
                            ></textarea>
                        </div>
                    </div>

                    <SuccessAlert
                        :visible="showSuccessAlert"
                        :message="successMessage"
                    />

                    <div class="button-row">
                        <button
                            type="button"
                            class="btn-cancel"
                            @click="handleCancel"
                        >
                            Batal
                        </button>
                        <button type="submit" class="btn-submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import SuccessAlert from "@/components/SuccessAlert.vue";
import axios from "axios";

export default {
    name: "BeritaAddComponent",
    components: {
        Sidebar,
        HeaderBar,
        SuccessAlert,
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
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    methods: {
        formatTanggal(date) {
            const dateObj = new Date(date);
            if (isNaN(dateObj.getTime())) {
                alert("Tanggal tidak valid!");
                return "";
            }
            const year = dateObj.getFullYear();
            const month = (dateObj.getMonth() + 1).toString().padStart(2, "0");
            const day = dateObj.getDate().toString().padStart(2, "0");
            return `${year}-${month}-${day}`;
        },

        formatWaktu() {
            return `${this.formData.waktuStart} - ${this.formData.waktuEnd} WIB`;
        },

        handleSubmit() {
            if (!this.validateForm()) return;

            const data = {
                tanggal: this.formatTanggal(this.formData.tanggal),
                waktu: this.formatWaktu(), // Format waktu
                kategori: this.formData.kategori,
                judul: this.formData.judul,
                tempat: this.formData.tempat,
                deskripsi: this.formData.deskripsi,
            };

            const token = localStorage.getItem("token");
            axios
                .post("http://localhost:8000/api/auth/berita", data, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                })
                .then((response) => {
                    console.log(
                        "Data berita acara berhasil ditambahkan:",
                        response.data
                    );
                    this.successMessage = "Berita acara berhasil disimpan!";
                    this.showSuccessAlert = true;

                    setTimeout(() => {
                        this.showSuccessAlert = false;
                        this.$router.push({ path: "/berita" });
                    }, 2000); // tampilkan alert 2 detik dulu
                })
                .catch((error) => {
                    if (error.response) {
                        console.error("Response API Error:", error.response);
                        this.errorMessage =
                            "Gagal menyimpan data. Periksa form Anda atau coba lagi nanti.";
                    } else {
                        console.error("Error:", error.message);
                        this.errorMessage = "Terjadi kesalahan jaringan.";
                    }
                    this.showErrorAlert = true;

                    setTimeout(() => {
                        this.showErrorAlert = false;
                    }, 3000);
                });
        },

        validateForm() {
            const requiredFields = [
                { key: "tanggal", message: "Tanggal wajib diisi!" },
                { key: "waktuStart", message: "Waktu mulai wajib diisi!" },
                { key: "waktuEnd", message: "Waktu selesai wajib diisi!" },
                { key: "kategori", message: "Kategori wajib dipilih!" },
                { key: "judul", message: "Judul wajib diisi!" },
                { key: "tempat", message: "Tempat pelaksanaan wajib diisi!" },
                { key: "deskripsi", message: "Deskripsi wajib diisi!" },
            ];

            for (const field of requiredFields) {
                if (!this.formData[field.key]) {
                    alert(field.message);
                    return false;
                }
            }
            return true;
        },

        handleCancel() {
            if (confirm("Apakah Anda yakin ingin membatalkan?")) {
                this.$router.go(-1);
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
    /* Adjust width as needed */
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
    /* Bisa diubah ukurannya secara vertikal */
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
    gap: 10px;
    width: 100%;
    border-top: 1px solid #edf2f7;
    padding-top: 35px;
}

.btn-cancel,
.btn-submit {
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    font-weight: 500;
    cursor: pointer;
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
</style>
