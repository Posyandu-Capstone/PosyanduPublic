<template>
    <div class="layout">
        <!-- Sidebar -->
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <div class="main-content">
            <!-- Formulir Pemeriksaan -->
            <div class="form-container">
                <h3 class="form-title">Formulir Antrian Acara</h3>
                <div class="card-separator"></div>

                <form class="form-grid" @submit.prevent="handleSubmit">
                    <!-- Informasi Pemeriksaan -->
                    <div class="section">
                        <h4 class="no-border-top">
                            Informasi Pendaftaran Acara
                        </h4>

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
                                    placeholder="Masukkan NIK Pendaftar"
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
import SuccessAlert from "@/components/SuccessAlert.vue";
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
            activeMenu: "berita",
            formData: {
                WargaNIK: null,
                beritaId: "",
                anggotaKeluargaNik: "",
            },
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    mounted() {
        this.formData.beritaId = this.$route.params["beritaId"] || "";
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
                    // ✅ Set pesan dan tampilkan alert
                    this.successMessage = "Berhasil menambahkan pemeriksaan";
                    this.showSuccessAlert = true;

                    // ✅ Redirect setelah beberapa detik
                    setTimeout(() => {
                        this.showSuccessAlert = false;
                        this.$router.push({
                            path: `/Berita-informasi/${this.formData.beritaId}`,
                        });
                    }, 2500); // 2.5 detik, bisa kamu sesuaikan
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
/* Atur font dan basic */
* {
    font-family: "Inter", sans-serif;
}

.layout {
    display: flex;
    min-height: 50vh;
    background-color: #f7f7f7;
}

.main-content {
    padding: 30px 20px;
    flex: 1 1 100%;
    background-color: #ffffff;
    display: flex;
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
    max-width: 100%;
    box-sizing: border-box;
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
    margin-bottom: 35px;
}

.section h4 {
    font-size: 15px;
    font-weight: 500;
    color: #070707;
    font-weight: bold;
    margin-bottom: 12px;
    border-top: 1px solid #d1d5db;
    padding-top: 14px;
    text-align: center;
}

.section h4.no-border-top {
    border-top: none;
    padding-top: 0;
}

.form-row {
    display: flex;
    gap: 24px;
    margin-bottom: 16px;
    flex-wrap: wrap; /* agar kolom bisa wrap ke bawah di layar kecil */
}

.form-group {
    flex: 1 1 300px; /* minimal 300px dan bisa stretch */
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0; /* supaya flex-shrink jalan */
}

.form-group label {
    font-weight: bold;
    font-size: 13px;
    color: #070707;
    width: 120px;
    margin-bottom: 0;
    flex-shrink: 0; /* label tidak mengecil */
}

.form-group input {
    width: 100%; /* pakai full width dari flex container */
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fafafa;
    height: 40px;
    box-sizing: border-box;
    min-width: 0;
}

.form-row .right-align {
    text-align: right;
}

.justify-end {
    justify-content: flex-end;
}

.button-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    width: 100%;
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
    box-sizing: border-box;
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

/* RESPONSIVE MEDIA QUERIES */
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
        padding: 16px 12px;
    }

    .form-group label {
        width: 100px; /* sedikit kecil agar muat di mobile */
        font-size: 12px;
    }

    .button-row {
        flex-direction: column;
        align-items: stretch;
    }

    .btn-cancel,
    .btn-submit {
        width: 100%; /* Tombol full lebar di layar kecil */
        max-width: 100%; /* Hindari batasan max-width */
    }
}

.sidebar-footer {
    display: none;
}
</style>
