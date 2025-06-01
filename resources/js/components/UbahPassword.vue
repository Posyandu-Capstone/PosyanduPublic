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
                    <h3>Ubah Password</h3>

                    <form @submit.prevent="submitPassword">
                        <div class="form-row">
                            <label>Password Saat Ini</label>
                            <div class="input-group">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="passwordForm.current_password"
                                    required
                                />
                                <span
                                    class="toggle-visibility"
                                    @click="togglePasswordVisibility"
                                >
                                    <i
                                        :class="
                                            showPassword
                                                ? 'fas fa-eye-slash'
                                                : 'fas fa-eye'
                                        "
                                    ></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-row">
                            <label>Password Baru</label>
                            <div class="input-group">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="passwordForm.password"
                                    required
                                />
                                <span
                                    class="toggle-visibility"
                                    @click="togglePasswordVisibility"
                                >
                                    <i
                                        :class="
                                            showPassword
                                                ? 'fas fa-eye-slash'
                                                : 'fas fa-eye'
                                        "
                                    ></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-row">
                            <label>Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="passwordForm.password_confirmation"
                                    required
                                />
                                <span
                                    class="toggle-visibility"
                                    @click="togglePasswordVisibility"
                                >
                                    <i
                                        :class="
                                            showPassword
                                                ? 'fas fa-eye-slash'
                                                : 'fas fa-eye'
                                        "
                                    ></i>
                                </span>
                            </div>
                        </div>

                        <div class="button-row">
                            <button type="submit" class="btn-ubah">
                                Ubah Password
                            </button>
                        </div>
                    </form>

                    <SuccessAlert
                        :visible="showSuccessAlert"
                        :message="successMessage"
                    />

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
    name: "UbahPassword",
    components: { Sidebar, HeaderBar, SuccessAlert },
    data() {
        return {
            activeMenu: "profile",
            passwordForm: {
                current_password: "",
                password: "",
                password_confirmation: "",
            },
            message: "",
            error: "",
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    methods: {
        updateActiveMenu(newMenu) {
            this.activeMenu = newMenu;
        },
        async submitPassword() {
            try {
                const token = localStorage.getItem("token");

                const res = await axios.post(
                    "https://capstonesi.online/api/auth/profile/password",
                    this.passwordForm,
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                );

                if (res.data.status === "success") {
                    this.successMessage = "Password berhasil diubah.";
                    this.showSuccessAlert = true;
                    this.error = null; // Bersihkan error jika ada sebelumnya

                    // Reset form
                    this.passwordForm = {
                        current_password: "",
                        password: "",
                        password_confirmation: "",
                    };

                    // Tutup alert dan redirect setelah 3 detik
                    setTimeout(() => {
                        this.showSuccessAlert = false;
                        this.$router.push({ name: "DataPasienDetail" });
                    }, 3000);
                } else {
                    this.error =
                        res.data.message || "Gagal memperbarui password.";
                    this.showSuccessAlert = false;
                }
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Terjadi kesalahan saat memperbarui password.";
                this.showSuccessAlert = false;
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
    padding: 20px;
}
.form-section h3 {
    font-size: 16px;
    color: #111216;
    font-weight: bold;
    padding-bottom: 20px;
    border-bottom: 1px solid #ccc;
}
.form-row {
    display: flex;
    gap: 200px;
    margin-bottom: 16px;
    align-items: center;
}
.form-row label {
    min-width: 200px;
    font-weight: 500;
    font-size: 13px;
    color: #070707;
    font-weight: bold;
    text-align: left;
    flex-shrink: 0;
}
.form-row .right-align {
    text-align: right;
}
.form-row input {
    padding: 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fafafa;
    flex-grow: 1;
    width: 500px;
    box-sizing: border-box;
    height: 40px;
}
.btn-ubah {
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    height: 40px;
    background: #074a5d;
    color: white;
}

/* Responsive untuk max-width 768px */
@media (max-width: 768px) {
    .layout {
        flex-direction: column;
        padding: 10px;
    }
    .main-content {
        padding: 15px;
    }
    .form-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    .form-row label,
    .form-row input {
        width: 130%; /* label dan input full container */
        flex-grow: 0; /* hilangkan flex-grow biar width:100% valid */
    }
    .btn-ubah {
        width: 100%;
        padding: 12px 0;
        font-size: 16px;
    }
}
</style>
