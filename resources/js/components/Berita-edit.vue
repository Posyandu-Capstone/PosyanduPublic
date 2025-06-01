<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <div class="main-content">
            <HeaderBar title="Edit Berita Acara Posyandu" />
            <div class="separator"></div>

            <div class="form-container">
                <h3 class="form-title">Formulir Edit Berita Acara Posyandu</h3>
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
                        <button type="submit" class="btn-submit">Simpan</button>
                        <button
                            type="button"
                            class="btn-cancel"
                            @click="handleCancel"
                        >
                            Batal
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
    name: "BeritaEditComponent",
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
    mounted() {
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
        handleSubmit() {
            if (!this.validateForm()) return;

            const id = this.$route.params.id;
            const token = localStorage.getItem("token");

            const data = {
                tanggal: this.formatTanggal(this.formData.tanggal),
                waktu: this.formatWaktu(),
                kategori: this.formData.kategori,
                judul: this.formData.judul,
                tempat: this.formData.tempat,
                deskripsi: this.formData.deskripsi,
            };

            axios
                .put(`http://localhost:8000/api/auth/berita/${id}`, data, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                    },
                })
                .then(() => {
                    this.successMessage = "Data berhasil diperbarui!";
                    this.showSuccessAlert = true;

                    setTimeout(() => {
                        this.showSuccessAlert = false;
                        this.$router.push("/berita");
                    }, 2000);
                })
                .catch((err) => {
                    console.error(err);
                    this.errorMessage = "Gagal memperbarui data.";
                    this.showErrorAlert = true;

                    setTimeout(() => {
                        this.showErrorAlert = false;
                    }, 3000);
                });
        },
        handleCancel() {
            if (confirm("Batalkan perubahan?")) {
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

.button-row {
    display: flex;
    justify-content: space-between;
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
    height: 40px;
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

.button-container {
    display: flex;
    justify-content: space-between;
    gap: 10px;
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
</style>
