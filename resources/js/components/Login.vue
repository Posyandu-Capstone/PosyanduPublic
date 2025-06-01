<template>
    <div class="container">
        <!-- Kotak Hijau di Sisi Kiri (Setengah Layar) -->
        <div class="left-box">
            <img :src="babyImage" alt="Baby Image" class="left-image" />
            <img :src="dokterImage" alt="Dokter Image" class="left-image2" />
            <h2>Dashboard Admin Posyandu</h2>
            <p>
                Mengelola informasi kesehatan ibu dan anak, laporan kegiatan,
                dan data kunjungan dalam satu platform praktis.
            </p>
        </div>

        <!-- Form Sign-In di Sisi Kanan -->
        <div class="right-box">
            <div class="logo-wrapper">
                <img :src="logoImage" alt="Logo Image" class="logo-image" />
                <span class="logo-text">PosyanduCare</span>
            </div>

            <div class="signin-container">
                <h2>Masuk Akun PosyanduCare</h2>
                <p class="subtitle">Monitor, Manage, and Care Better</p>

                <form @submit.prevent="login">
                    <!-- Input Email -->
                    <div class="input-group">
                        <label>Email</label>
                        <div class="input-with-icon full-width">
                            <img src="@/assets/email.svg" alt="email icon" />
                            <input
                                type="email"
                                v-model="form.email"
                                placeholder="Enter your email"
                                required
                            />
                        </div>
                    </div>

                    <!-- Input Password -->
                    <div class="input-group">
                        <label>Password</label>
                        <div
                            class="input-with-icon full-width password-wrapper"
                        >
                            <img
                                src="@/assets/password.svg"
                                alt="password icon"
                                class="input-icon"
                            />
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
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="options">
                        <router-link
                            to="/forgot-password"
                            class="forgot-password"
                            >Forgot Password?</router-link
                        >
                    </div>

                    <!-- Tombol Sign In -->
                    <button type="submit" class="signin-btn">Sign In</button>
                </form>
                <p v-if="message" :class="messageClass">{{ message }}</p>

                <!-- Garis Pembatas -->
                <div class="divider">Or</div>

                <div class="social-login">
                    <button class="google-btn" @click="loginWithGoogle">
                        <img
                            :src="googleLogo"
                            alt="Google Logo"
                            class="social-icon"
                        />
                        <strong>Login with Google</strong>
                    </button>
                </div>

                <!-- Link Sign Up -->
                <p class="signup-text">
                    Don't have an account?
                    <router-link to="/registerstep1">Sign Up</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import babyImage from "@/assets/baby.jpg";
import dokterImage from "@/assets/dokter.png";
import logoImage from "@/assets/logo.png";
import googleLogo from "@/assets/Google.svg";
import appleLogo from "@/assets/Apple.svg";
import eyeIcon from "@/assets/eye.svg";
import eyeOffIcon from "@/assets/eye-off.svg";
import passwordIcon from "@/assets/password.svg";

export default {
    data() {
        return {
            form: {
                email: "",
                password: "",
                rememberMe: false,
            },
            message: "",
            messageClass: "",
            babyImage,
            dokterImage,
            logoImage,
            googleLogo,
            appleLogo,
            showPassword: false,
            eyeIcon,
            eyeOffIcon,
            passwordIcon,
        };
    },
    methods: {
        togglePassword() {
            this.showPassword = !this.showPassword;
        },
        loginWithGoogle() {
            const backendUrl =
                import.meta.env.VITE_APP_URL || "http://localhost:8000/api";
            window.location.href = `${backendUrl}/auth/google`;
        },
        async login() {
            try {
                const apiUrl =
                    import.meta.env.VITE_APP_URL || "http://localhost:8000/api";
                const { data } = await axios.post(
                    `${apiUrl}/auth/login`,
                    this.form,
                    {
                        headers: {
                            Accept: "application/json",
                            "Content-Type": "application/json",
                        },
                    }
                );

                const expiresIn = 7 * 24 * 60 * 60 * 1000;
                const expiryTime = Date.now() + expiresIn;

                localStorage.setItem("token", data.access_token);
                localStorage.setItem("token_expiry", expiryTime);

                axios.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${data.access_token}`;

                const meResponse = await axios.post(`${apiUrl}/auth/me`, {});
                const role = meResponse.data.posisi;
                const nama_lengkap = meResponse.data.nama_lengkap;
                localStorage.setItem("userRole", role);
                localStorage.setItem("userName", nama_lengkap);

                this.message = "Login berhasil!";
                this.messageClass = "text-green-600 font-bold";

                if (role === "Admin Verifikator") {
                    this.$router.push("/admin-verifikator");
                } else if (role === "Admin Desa" || role === "Kader") {
                    this.$router.push("/dashboard");
                } else {
                    this.message = `Login berhasil, tetapi role '${role}' tidak dikenali.`;
                    this.messageClass = "text-red-600 font-bold";
                }
            } catch (error) {
                this.message = error.response?.data?.message || "Login gagal.";
                this.messageClass = "text-red-600 font-bold";
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

.password-wrapper {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 50px;
    top: 55%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
}

.eye-icon {
    width: 20px !important;
    /* Ukuran Ikon */
    height: 20px !important;
    /* Ukuran Ikon */
}

.input-with-icon img.input-icon {
    position: absolute;
    top: 50%;
    left: 12px;
    transform: translateY(-50%);
    width: 25px;
    height: 25px;
    opacity: 0.6;
}

.container {
    display: flex;
    min-height: 100vh;
    overflow-y: auto;
}

/* Kotak Hijau - Bagian Kiri */
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

/* Bagian Kanan - Form Sign-In */
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
    margin-left: 50px;
    margin-bottom: 40px;
    align-self: flex-start;
    gap: 12px;
}

.logo-image {
    width: 75px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    margin: 0;
}

.logo-text {
    font-family: "Protest Strike", sans-serif;
    font-size: 26px;
    color: #08607a;
}

.signin-container {
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 10px;
}

h2 {
    font-size: 35px;
    margin-bottom: 10px;
    font-weight: bold;
}

.subtitle {
    font-size: 14px;
    color: gray;
    margin-bottom: 25px;
}

/* Input Form */
.input-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 20px;
}

.input-group label {
    font-size: 16px;
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
    /* Warna label input */
}

.input-group input {
    width: 95%;
    padding: 12px;
    /* Sesuaikan padding agar lebih besar */
    font-size: 16px;
    /* Ukuran font sesuai dengan Register */
    color: #333;
    /* Warna font input yang konsisten */
    border: 1px solid #ccc;
    border-radius: 10px;
    box-sizing: border-box;
}

.input-group input::placeholder {
    color: #b0b3b5;
    font-size: 15px;
}

.input-with-icon {
    position: relative;
    width: 100%;
}

.input-with-icon img {
    position: absolute;
    top: 50%;
    left: 12px;
    transform: translateY(-50%);
    width: 26px;
    height: 26px;
    opacity: 0.6;
}

.input-with-icon input {
    width: 100%;
    padding: 10px 10px 10px 50px;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-sizing: border-box;
}

/* Remember Me & Forgot Password */
.options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    font-size: 14px;
    margin-bottom: 20px;
    font-weight: bold;
}

.forgot-password {
    color: #ffad54;
    text-decoration: none;
    text-align: left;
}

.forgot-password:hover {
    text-decoration: underline;
}

/* Tombol Sign-In */
.signin-btn {
    width: 100%;
    padding: 15px;
    /* Pastikan ukuran tombol cukup besar */
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

/* Garis Pembatas */
.divider {
    text-align: center;
    margin: 20px 0;
    color: gray;
    font-size: 14px;
}

/* Tombol Social Login */
.social-login {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 20px;
}

.google-btn,
.apple-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 20px;
    background-color: white;
    cursor: pointer;
    font-size: 14px;
    text-align: center;
    width: 180px;
}

.google-btn strong,
.apple-btn strong {
    position: relative;
    /* Naikkan teks sedikit */
}

.google-btn:hover,
.apple-btn:hover {
    background-color: #f1f1f1;
}

.google-btn img,
.apple-btn img {
    width: 20px;
    /* Ukuran Ikon */
    height: 20px;
    /* Ukuran Ikon */
    margin-right: 10px;
    /* Ruang antara ikon dan teks */
}

/* Link Sign Up */
.signup-text {
    font-size: 14px;
    text-align: center;
}

.signup-text a {
    color: #ffad54;
    text-decoration: none;
    font-weight: bold;
    padding-top: 2px;
}

.signup-text a:hover {
    text-decoration: underline;
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
    .input-group input::placeholder {
        font-size: 13px;
    }
    .options {
        font-size: 12px;
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
        max-width: 100%;
        padding: 0 20px; /* padding horizontal supaya tetap nyaman di mobile */
        box-sizing: border-box;
    }

    .input-group input {
        width: 100%;
    }

    .signin-btn {
        width: 100%;
    }
}
</style>
