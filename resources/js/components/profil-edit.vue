<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="updateActiveMenu"
        />
        <div class="main-content">
            <HeaderBar title="Edit Profil" />
            <div class="separator"></div>

            <div class="container">
                <div class="form-section">
                    <h3>Edit Profil Pengguna</h3>

                    <form @submit.prevent="submitProfile">
                        <div class="form-row">
                            <label>Nama Lengkap</label>
                            <input
                                v-model="form.nama_lengkap"
                                type="text"
                                required
                            />
                        </div>

                        <div class="form-row">
                            <label>Email</label>
                            <input v-model="form.email" type="email" disabled />
                        </div>

                        <div class="form-row">
                            <label>Kontak</label>
                            <input v-model="form.kontak" type="text" />
                        </div>

                        <div class="form-row">
                            <label>Foto Profil</label>
                            <input
                                type="file"
                                @change="handleFileChange"
                                accept="image/*"
                            />
                        </div>

                        <div class="button-row">
                            <button type="submit" class="btn-save">
                                Simpan Perubahan
                            </button>
                            <router-link to="/profil" class="btn-cancel"
                                >Batal</router-link
                            >
                        </div>
                    </form>

                    <SuccessAlert
                        :visible="showSuccessAlert"
                        :message="successMessage"
                    />

                    <div class="separator"></div>
                    <div v-if="message" class="message">{{ message }}</div>
                    <div v-if="error" class="error">{{ error }}</div>
                </div>
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
    name: "ProfileEdit",
    components: { Sidebar, HeaderBar, SuccessAlert },
    data() {
        return {
            activeMenu: "profile",
            form: {
                nama_lengkap: "",
                email: "",
                posisi: "",
                kontak: "",
            },
            file: null, // Inisialisasi file di data
            message: "",
            error: "",
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    mounted() {
        this.fetchProfile();
    },
    methods: {
        updateActiveMenu(newMenu) {
            this.activeMenu = newMenu;
        },
        handleFileChange(e) {
            this.file = e.target.files[0];
        },
        async fetchProfile() {
            try {
                const token = localStorage.getItem("token");
                const res = await axios.get(
                    "https://capstonesi.online/api/auth/profile",
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );
                if (res.data.status === "success") {
                    const user = res.data.data;
                    this.form = {
                        nama_lengkap: user.nama_lengkap,
                        email: user.email,
                        posisi: user.posisi || "",
                        kontak: user.kontak || "",
                    };
                }
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Gagal mengambil data.";
            }
        },
        async submitProfile() {
            try {
                const token = localStorage.getItem("token");
                const formData = new FormData();
                formData.append("_method", "PUT");
                formData.append("nama_lengkap", this.form.nama_lengkap);
                formData.append("email", this.form.email);
                formData.append("posisi", this.form.posisi);
                formData.append("kontak", this.form.kontak);
                if (this.file) {
                    formData.append("foto_profil", this.file);
                }
                const res = await axios.post(
                    "https://capstonesi.online/api/auth/profile",
                    formData,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );
                if (res.data.status === "success") {
                    this.successMessage = "Profil berhasil diperbarui.";
                    this.showSuccessAlert = true;
                    this.error = "";

                    // ✅ Tutup alert dan redirect setelah 3 detik
                    setTimeout(() => {
                        this.showSuccessAlert = false;
                        this.$router.push("/profil");
                    }, 3000);
                } else {
                    this.error =
                        res.data.message || "Gagal memperbarui profil.";
                }
            } catch (err) {
                // Tampilkan error validasi dari Laravel jika ada
                if (
                    err.response?.status === 422 &&
                    err.response?.data?.errors
                ) {
                    const errors = err.response.data.errors;
                    this.error = Object.values(errors).flat().join(", ");
                } else {
                    this.error =
                        err.response?.data?.message ||
                        "Terjadi kesalahan saat memperbarui profil.";
                }
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
.container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 0px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    border: 1px solid #ccc;
    margin-top: 30px;
}
.form-section {
    align-items: center;
    padding: 20px 20px;
}
.form-section h3 {
    font-size: 13px;
    color: #111216;
    font-weight: bold;
    border-bottom: 1px solid #ccc;
    padding-bottom: 20px;
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
.form-row p,
.form-row input {
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fafafa;
    width: 100%;
    box-sizing: border-box;
    height: 40px;
}
.form-row select {
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fafafa;
    width: 100%;
    box-sizing: border-box;
    height: 40px;
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
.btn-save {
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
    background: #074a5d;
    color: white;
}
.btn-cancel {
    padding: 10px 20px;
    border-radius: 20px;
    background: white;
    border: 1px solid #074a5d;
    color: #074a5d;
    text-decoration: none;
    text-align: center;
}
.button-row {
    display: flex;
    justify-content: space-between;
    padding: 20px 0px;
}
@media (max-width: 768px) {
    .form-row {
        display: flex;
        margin-bottom: 20px;
        gap: 1rem;
    }
    .form-row label {
        min-width: 80px;
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
}
</style>
