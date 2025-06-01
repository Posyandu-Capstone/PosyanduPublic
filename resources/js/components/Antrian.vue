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

            <!-- Formulir Pemeriksaan -->
            <div class="form-container">
                <h3 class="form-title">Formulir Pemeriksaan</h3>
                <div class="card-separator"></div>

                <form class="form-grid" @submit.prevent="handleSubmit">
                    <!-- Informasi Pemeriksaan -->
                    <div class="section">
                        <h4 class="no-border-top">Informasi Pemeriksaan</h4>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="WargaNIK">Warga Wali NIK</label>
                                <input
                                    type="text"
                                    id="WargaNIK"
                                    v-model="formData.WargaNIK"
                                    placeholder="Masukkan Warga Wali NIK"
                                />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="beritaId">Acara ID</label>
                                <input
                                    type="text"
                                    id="beritaId"
                                    v-model="formData.beritaId"
                                    placeholder="Masukkan ID Acara"
                                />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="anggotaKeluargaNik">NIK</label>
                                <input
                                    type="text"
                                    id="anggotaKeluargaNik"
                                    v-model="formData.anggotaKeluargaNik"
                                    placeholder="Masukkan NIK"
                                />
                            </div>
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
                            Tambah ke Antrian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import SuccessAlert from "./SuccessAlert.vue";
import axios from "axios";

export default {
    name: "AntrianComponent",
    components: {
        Sidebar,
        HeaderBar,
        SuccessAlert,
    },
    data() {
        return {
            activeMenu: "pasien",
            anggota: null,
            formData: {
                nik: null,
                WargaNIK: null,
                beritaId: "",
                anggotaKeluargaNik: "",
            },
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    mounted() {
        // Ambil nik dari params dan userId dari query
        this.formData.anggotaKeluargaNik = this.$route.params.nik || null;
        this.formData.WargaNIK = this.$route.query.userId || null;

        // Kalau mau, juga isi nik jika ada (lihat kode kamu sepertinya formData.nik tidak dipakai)
        this.formData.nik = this.$route.params.nik || null;
    },
    methods: {
        handleSubmit() {
            localStorage.setItem(
                "selectedNik",
                this.formData.anggotaKeluargaNik
            );

            axios
                .post(
                    "http://localhost:8000/api/auth/pemeriksaan",
                    {
                        user_id: this.formData.WargaNIK,
                        berita_id: this.formData.beritaId,
                        anggota_keluarga_nik: this.formData.anggotaKeluargaNik,
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
                    console.log("Berhasil:", response.data);

                    // Tampilkan alert sukses
                    this.successMessage =
                        "Riwayat pemeriksaan berhasil ditambahkan.";
                    this.showSuccessAlert = true;

                    const pemeriksaanId = response.data.id;

                    setTimeout(() => {
                        this.showSuccessAlert = false;

                        this.$router.push({
                            path: `/riwayatpasien/${pemeriksaanId}`,
                        });
                    }, 3000);
                })
                .catch((error) => {
                    console.error(
                        "Gagal menambahkan pemeriksaan:",
                        error.response?.data || error.message
                    );
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
    display: flex;
    gap: 24px;
    /* Jarak antar input */
    margin-bottom: 16px;
}

.form-group {
    flex: 1;
    display: flex;
    align-items: center;
    /* vertikal tengah */
    gap: 12px;
    /* jarak antara label dan input */
}

.form-group label {
    font-weight: bold;
    font-size: 13px;
    color: #070707;
    width: 100px;
    /* lebar tetap */
    margin-bottom: 0;
    /* hilangkan jarak bawah karena sekarang sejajar */
}

.form-group input {
    width: 950px;
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fafafa;
    height: 40px;
    box-sizing: border-box;
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
    padding-top: 5px;
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
        flex-direction: column; /* dari row jadi kolom */
    }

    .main-content {
        padding: 16px;
    }

    .form-row {
        flex-direction: column; /* input dan label jadi vertikal */
        gap: 12px;
    }

    .form-group {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .form-group label {
        width: 100%;
    }

    .form-group input {
        width: 100%; /* jangan fixed besar */
    }

    .btn-cancel,
    .btn-submit {
        width: 48%; /* tombol jadi dua baris */
    }

    .button-row {
        flex-wrap: wrap;
        gap: 10px;
    }

    .card-separator {
        width: 100%;
        margin-left: 0;
    }
}
</style>
