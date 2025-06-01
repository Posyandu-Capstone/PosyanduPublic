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
                <h2>Reset Password</h2>
                <p class="subtitle">
                    Silahkan isi email untuk dikirim link reset password
                </p>
                <form @submit.prevent="resetPassword">
                    <div v-if="message" class="message">{{ message }}</div>
                    <div class="input-group">
                        <label for="password">Password Baru</label>
                        <input type="password" v-model="password" required />
                    </div>
                    <div class="input-group">
                        <label for="password_confirmation"
                            >Konfirmasi Password</label
                        >
                        <input
                            type="password"
                            v-model="password_confirmation"
                            required
                        />
                    </div>
                    <button type="submit" class="submit">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import babyImage from "@/assets/baby.jpg";
import dokterImage from "@/assets/dokter.png";
import logoImage from "@/assets/logo.png";

export default {
    data() {
        return {
            form: {
                password: "",
                password_confirmation: "",
                message: "",
                email: "", // Email diambil dari URL
                token: "", // Token dari URL
            },
            babyImage,
            dokterImage,
            logoImage,
        };
    },
    mounted() {
        // Mengambil token dan email dari URL
        const pathSegments = window.location.pathname.split("/");
        this.token = pathSegments[pathSegments.length - 1]; // Token dari URL

        const urlParams = new URLSearchParams(window.location.search);
        this.email = urlParams.get("email"); // Email dari query string

        if (!this.token || !this.email) {
            this.message =
                "Token atau email tidak ditemukan, silakan coba lagi.";
        }
    },
    methods: {
        async resetPassword() {
            if (!this.token) {
                this.message = "Token tidak ditemukan, silakan coba lagi.";
                return;
            }

            try {
                const response = await axios.post(
                    "http://localhost:8000/api/auth/reset-password",
                    {
                        token: this.token,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                    }
                );

                this.message =
                    "Password berhasil direset. Anda akan diarahkan ke halaman login.";
                setTimeout(() => {
                    this.$router.push("/login");
                }, 2000); // Redirect setelah 2 detik
            } catch (error) {
                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.message
                ) {
                    this.message = error.response.data.message;
                } else {
                    this.message = "Gagal mereset password. Silakan coba lagi.";
                }
            }
        },
    },
};
</script>

<style scoped>
.container {
    display: flex;
    min-height: 100vh;
    overflow-y: auto;
    font-family: "Inter", sans-serif;
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
    padding-bottom: 60px;
    border-radius: 10px;
    color: white;
    box-sizing: border-box;
    overflow: hidden;
    position: relative;
}

.left-box h2 {
    color: white;
    margin-bottom: 5px;
    font-size: 35px !important;
}

.left-box p {
    font-size: 16px;
    color: #b0b3b5;
    max-width: 80%;
    line-height: 1.5;
    font-weight: normal;
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
    width: 60%;
}

h2 {
    font-size: 35px;
    margin-bottom: 10px;
    font-weight: bold;
    color: #074a5d;
}

.subtitle {
    font-size: 14px;
    color: gray;
    margin-bottom: 25px;
}
.input-group {
    display: flex;
    align-items: flex-start;
    flex-direction: column;
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
    width: 100%;
    padding: 12px;
    font-size: 16px;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-sizing: border-box;
}
.submit {
    width: 100%;
    padding: 15px;
    /* Pastikan ukuran tombol cukup besar */
    background-color: #4f93af;
    color: white;
    background-color: #f44336;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    margin-top: 15px;
}

.submit:hover {
    background-color: #165d52;
}
.message {
    margin: 10px 0;
    padding: 8px;
    color: white;
    background-color: #f44336;
    border-radius: 5px;
}
/* Media Query Responsiveness */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        padding: 20px;
    }

    .left-box,
    .right-box {
        width: 100%;
        max-width: 100%;
        padding: 0 20px 40px 20px;
    }

    .left-box h2 {
        font-size: 24px !important;
        margin-bottom: 10px;
    }

    .left-box p {
        font-size: 14px;
        max-width: 100%;
        line-height: 1.4;
    }

    .left-image,
    .left-image2 {
        height: 150px;
        width: 100%;
        margin: 0;
    }

    .logo-wrapper {
        margin-left: 0;
        margin-bottom: 30px;
        gap: 8px;
    }

    .logo-image {
        width: 50px;
    }

    .logo-text {
        font-size: 20px;
    }

    .signin-container {
        width: 100%;
        padding: 0 10px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 15px;
    }

    .subtitle {
        font-size: 13px;
        margin-bottom: 20px;
    }

    .input-group label {
        font-size: 14px;
        margin-bottom: 6px;
    }

    .input-group input {
        font-size: 14px;
        padding: 10px;
    }

    .submit {
        padding: 12px;
        font-size: 16px;
    }

    .message {
        font-size: 14px;
    }
}
</style>
