<template>
    <div class="layout">
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="updateActiveMenu"
        />
        <div class="main-content">
            <HeaderBar title="Profil" />
            <div class="separator"></div>

            <div class="container">
                <div class="table-container">
                    <div class="table-header">
                        <h3>Data Profil Pengguna</h3>
                    </div>
                    <div class="section">
                        <h4 class="no-border-top">Informasi Profil</h4>
                        <div class="form-row profile-picture">
                            <!-- Menampilkan gambar profil -->
                            <img
                                :src="profileImage"
                                alt="Foto Profil"
                                v-if="profileImage"
                            />
                            <img
                                :src="defaultProfileImage"
                                alt="Foto Profil Default"
                                v-else
                            />
                        </div>
                        <div class="form-row">
                            <label>Nama Lengkap</label>
                            <input
                                type="text"
                                v-model="formData.nama_lengkap"
                                readonly
                            />
                        </div>

                        <div class="form-row">
                            <label>NIK</label>
                            <input
                                type="text"
                                v-model="formData.nik"
                                readonly
                            />
                        </div>

                        <div class="form-row">
                            <label>No Telepon</label>
                            <input
                                type="text"
                                v-model="formData.kontak"
                                readonly
                            />
                        </div>

                        <div class="form-row">
                            <label>Email</label>
                            <input
                                type="email"
                                v-model="formData.email"
                                disabled
                            />
                        </div>
                        <div class="form-row">
                            <label>Ubah Password</label>
                            <router-link to="/ubah-password" class="btn"
                                >Ubah Password</router-link
                            >
                        </div>
                        <div class="button-row">
                            <router-link to="/profile-edit" class="btn"
                                >Edit</router-link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import axios from "axios";
// Import gambar default
import defaultProfileImage from "@/assets/user.png"; // Pastikan path sesuai
export default {
    name: "Profile",
    components: { Sidebar, HeaderBar },
    data() {
        return {
            activeMenu: "profile",
            formData: {
                nama_lengkap: "",
                nik: "",
                kontak: "",
                email: "",
                foto_profil: "", // Menggunakan foto_profil dari backend
            },
            profileImage: null, // URL gambar profil
            defaultProfileImage, // Gambar default jika foto tidak tersedia
            isLoading: false,
            error: null,
        };
    },
    mounted() {
        this.fetchProfile();
    },
    methods: {
        async fetchProfile() {
            this.isLoading = true;
            this.error = null;
            try {
                const token = localStorage.getItem("token");
                if (!token) {
                    this.error =
                        "Token tidak ditemukan, silakan login terlebih dahulu.";
                    this.isLoading = false;
                    return;
                }
                const response = await axios.get(
                    `http://localhost:8000/api/auth/profile`,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            Accept: "application/json",
                        },
                    }
                );
                if (response.data.status === "success") {
                    const user = response.data.data;
                    this.formData.nama_lengkap = user.nama_lengkap || "";
                    this.formData.nik = user.nik || "";
                    this.formData.kontak = user.kontak || "";
                    this.formData.email = user.email || "";
                    this.formData.foto_profil = user.foto_profil || "";
                    // Atur gambar profil: jika ada, gunakan URL dari backend
                    this.profileImage = user.foto_profil
                        ? user.foto_profil
                        : null;
                } else {
                    this.error =
                        response.data.message || "Gagal memuat data profil.";
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    error.message ||
                    "Terjadi kesalahan.";
            } finally {
                this.isLoading = false;
            }
        },
        updateActiveMenu(newMenu) {
            this.activeMenu = newMenu;
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
.table-container {
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
    align-items: center;
    padding: 2px 20px;
    border-bottom: 1px solid #ccc;
    /* Ganti ke warna yang sama seperti border tabel */
}
.table-header h3 {
    font-size: 13px;
    color: #111216;
    font-weight: bold;
}
.form-row {
    display: flex;
    gap: 50px;
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
.btn {
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    background: #074a5d;
    color: white;
    text-decoration: none;
}
.profile-picture {
    display: flex;
    justify-content: center; /* untuk horizontal center */
    margin: 20px;
}
.profile-picture img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #ddd;
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
}
</style>
