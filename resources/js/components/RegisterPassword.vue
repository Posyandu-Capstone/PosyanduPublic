<template>
    <div class="container">
        <!-- Kotak Hijau Kiri -->
        <div class="left-box">
            <img :src="babyImage" alt="Baby Image" class="left-image" />
            <img :src="dokterImage" alt="Dokter Image" class="left-image2" />
            <h2>Dashboard Admin Posyandu</h2>
            <p>
                Mengelola informasi kesehatan ibu dan anak, laporan kegiatan,
                dan data kunjungan dalam satu platform praktis.
            </p>
        </div>

        <!-- Kotak Kanan -->
        <div class="right-box">
            <div class="logo-wrapper">
                <img :src="logoImage" alt="Logo Image" class="logo-image" />
                <span class="logo-text">PosyanduCare</span>
            </div>

            <div class="register-container">
                <h2 :style="{ color: '#074A5D' }">Daftar Akun PosyanduCare</h2>
                <p class="subtitle">Lengkapi Data Akun Anda</p>

                <form @submit.prevent="goToStep3">
                    <!-- Form Email dan Password -->
                    <div class="input-group">
                        <label>Email</label>
                        <div class="input-with-icon full-width">
                            <img
                                :src="informIcon"
                                alt="Info Icon"
                                class="form-icon-right"
                            />
                            <input
                                type="email"
                                v-model="form.email"
                                placeholder="Masukkan email Anda"
                                required
                            />
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <div
                            class="input-with-icon full-width password-wrapper"
                        >
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                placeholder="Masukkan password Anda"
                                minlength="8"
                                required
                            />
                            <button
                                type="button"
                                class="toggle-password"
                                @click="togglePassword"
                            >
                                <img
                                    :src="showPassword ? eyeOffIcon : eyeIcon"
                                    alt="Toggle Password"
                                    class="eye-icon"
                                />
                            </button>
                        </div>
                        <small
                            v-if="form.password && form.password.length < 8"
                            class="error"
                        >
                            Password minimal 8 karakter.
                        </small>
                    </div>

                    <button type="submit" class="signin-btn">
                        Selanjutnya
                    </button>
                </form>

                <p v-if="message" :class="messageClass">{{ message }}</p>

                <div class="divider">Or</div>

                <div class="social-login">
                    <button class="google-btn" @click="googleLogin">
                        <img
                            :src="googleLogo"
                            alt="Google Logo"
                            class="social-icon"
                        />
                        <strong>Register with Google</strong>
                    </button>
                </div>

                <p v-if="message" :class="messageClass">{{ message }}</p>
                <p class="signup-text">
                    Sudah Punya Akun?
                    <router-link to="/login">Sign In</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { useRoute } from "vue-router";
import eyeIcon from "@/assets/eye.svg";
import eyeOffIcon from "@/assets/eye-off.svg";
import babyImage from "@/assets/baby.jpg";
import dokterImage from "@/assets/dokter.png";
import logoImage from "@/assets/logo.png";
import googleLogo from "@/assets/Google.svg";
import appleLogo from "@/assets/Apple.svg";
import informIcon from "@/assets/inform.svg";

export default {
    name: "RegisterStep2",
    data() {
        return {
            form: {
                email: "",
                password: "",
            },
            message: "",
            messageClass: "",
            showPassword: false,
            babyImage,
            dokterImage,
            logoImage,
            googleLogo,
            appleLogo,
            eyeIcon,
            eyeOffIcon,
            informIcon,
        };
    },
    created() {
        // Menangkap parameter token dan email dari URL
        const route = this.$route;
        const token = route.query.token;
        const email = route.query.email;

        if (token && email) {
            localStorage.setItem("token", token);
            localStorage.setItem("email", email);
            // Bisa menambahkan logika lanjutan setelah login
        }
    },
    methods: {
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        googleLogin() {
            const backendUrl =
                import.meta.env.VITE_APP_URL || "http://localhost:8000/api";
            window.location.href = `${backendUrl}/auth/google`;
        },

        appleLogin() {
            console.log("Apple Login");
        },

        async goToStep3() {
            if (
                this.form.password.length < 8 &&
                !localStorage.getItem("password")
            ) {
                this.message = "Password minimal 8 karakter.";
                this.messageClass = "error";
                return;
            }

            const nik = localStorage.getItem("nik");
            if (!nik) {
                this.message =
                    "NIK tidak ditemukan. Silakan ulangi dari langkah pertama.";
                this.messageClass = "error";
                return;
            }
            // Jika menggunakan login Google, tidak perlu password
            const payload = {
                email: this.form.email,
                password:
                    localStorage.getItem("password") || this.form.password,
                nik: nik,
            };

            try {
                const apiUrl =
                    import.meta.env.VITE_APP_URL || "http://localhost:8000/api";
                const response = await axios.post(`${apiUrl}/auth/register`, {
                    email: this.form.email,
                    password: this.form.password,
                    nik: nik,
                });

                this.message =
                    "Berhasil daftar! Lanjut ke pengisian data diri.";
                this.messageClass = "success";

                localStorage.setItem("email", this.form.email);
                localStorage.setItem("password", this.form.password);

                // Lanjutkan ke halaman step 3
                this.$router.push("/register");
            } catch (error) {
                if (error.response && error.response.data.errors) {
                    const firstError = Object.values(
                        error.response.data.errors
                    )[0][0];
                    this.message = firstError;
                } else {
                    this.message =
                        error.response?.data?.message ||
                        "Terjadi kesalahan saat mendaftar.";
                }
                this.messageClass = "error";
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

/* Tambahan gaya untuk select */
.styled-select {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    font-family: inherit;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    color: #333; /* default warna teks saat dipilih */
}

/* Kalau belum memilih posisi (placeholder aktif) */
.styled-select.placeholder {
    color: #aaa;
}

/* Option disabled = warna abu-abu di dropdown */
.styled-select option[disabled] {
    color: #aaa;
}

.container {
    display: flex;
    min-height: 100vh;
    overflow-y: auto;
}

.password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 10px;
    top: 55%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
}

.eye-icon {
    width: 20px;
    height: 20px;
}

.left-box {
    width: 45%;
    background-color: #1a4e44;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 0 40px 60px 40px;
    border-radius: 10px;
    color: white;
    box-sizing: border-box;
    overflow: hidden;
    position: relative;
}

.left-box h2 {
    font-size: 35px;
    margin-bottom: 5px;
}

.left-box p {
    font-size: 16px;
    max-width: 80%;
    line-height: 1.5;
}

.left-image,
.left-image2 {
    width: calc(100% + 80px);
    margin: 0 -40px;
    object-fit: cover;
    height: 300px;
}

.left-image2 {
    margin-bottom: 20px;
    border-radius: 0;
}

.right-box {
    width: 55%;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 10px;
    overflow: hidden;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-left: 50px;
    margin-bottom: 40px;
    align-self: flex-start;
}

.logo-image {
    width: 75px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    margin-left: 25px;
}

.logo-text {
    font-family: "Protest Strike", sans-serif;
    font-size: 26px;
    color: #08607a;
}

.register-container {
    width: 80%;
    max-width: 450px;
    text-align: center;
    padding-top: 70px;
}

h2 {
    font-size: 32px;
    margin-bottom: 10px;
    font-weight: bold;
}

.subtitle {
    font-size: 14px;
    color: gray;
    margin-bottom: 25px;
}

.input-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 20px;
}

.input-group label {
    font-size: 16px;
    margin-bottom: 5px;
    font-weight: bold;
}

.input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 10px;
}
.input-group input::placeholder {
    color: #b0b3b5;
    font-size: 15px;
}
.input-group input,
.input-group select {
    width: 100%;
    padding: 12px;
    font-size: 15px;
    box-sizing: border-box;
}
.input-group select {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 10px;
    background-color: white;
    font-size: 15px;
    color: #333;
}

.input-with-icon.full-width {
    width: 100%;
}
.input-with-icon {
    position: relative;
}

.form-icon-right {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    opacity: 0.6;
}

.input-with-icon input {
    padding-right: 40px; /* Tambah padding agar teks tidak tertimpa ikon */
}
.signin-btn {
    width: 100%;
    padding: 10px;
    background-color: #4f93af;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    margin-top: 15px;
}

.signin-btn:hover {
    background-color: #165d52;
}

.divider {
    text-align: center;
    margin: 20px 0;
    color: gray;
    font-size: 14px;
}

.social-login {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-bottom: 20px;
}

.social-login {
    display: flex;
    flex-direction: row; /* Mengatur tombol agar sejajar */
    justify-content: center; /* Pusatkan tombol */
    gap: 10px; /* Jarak antar tombol */
    margin-bottom: 20px;
}
.social-icon {
    width: 23px;
    height: 23px;
    margin-right: 10px;
    vertical-align: middle;
    flex-wrap: wrap;
}

.apple-icon {
    width: 27px;
    height: 23px;
    margin-right: 5px;
    vertical-align: middle;
}

.google-btn,
.apple-btn {
    padding: 7px;
    border: 1px solid #ccc;
    border-radius: 20px;
    background-color: white;
    cursor: pointer;
    font-size: 14px;
    text-align: center; /* Pusatkan teks */
    width: 200px;
}

.google-btn:hover,
.apple-btn:hover {
    background-color: #f1f1f1;
}

.signup-text {
    font-size: 14px;
    margin-top: 10px;
}

.signup-text a {
    color: #ffad54;
    text-decoration: none;
    font-weight: bold;
}

.signup-text a:hover {
    text-decoration: underline;
}

.success {
    color: green;
    margin-top: 10px;
}

.error {
    color: red;
    margin-top: 10px;
}

/* Media Query Responsiveness */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        padding: 20px;
    }

    .left-box {
        display: none;
    }

    .right-box {
        width: 100%;
        max-width: 100%;
    }

    .left-image,
    .left-image2 {
        height: 150px;
    }

    .left-box h2 {
        font-size: 24px;
    }

    .left-box p {
        font-size: 14px;
        max-width: 100%;
    }

    .logo-wrapper {
        margin-left: 0;
        margin-bottom: 40px;
        gap: 8px;
    }
    .logo-image {
        width: 50px; /* Ukuran lebih kecil untuk mobile */
    }

    .logo-text {
        font-size: 20px; /* Ukuran font nama logo lebih kecil */
    }
    h2 {
        margin-top: 2px;
        font-size: 27px;
    }

    .signin-container {
        width: 100%;
    }
    .input-group input {
        width: 100%;
        box-sizing: border-box;
    }

    .input-group {
        width: 100%;
        box-sizing: border-box;
    }

    .register-container {
        width: 100%;
        max-width: 100%;
        padding: 0 20px; /* padding horizontal supaya tetap nyaman di mobile */
        box-sizing: border-box;
    }

    .signin-btn {
        width: 100%;
    }
}
</style>
