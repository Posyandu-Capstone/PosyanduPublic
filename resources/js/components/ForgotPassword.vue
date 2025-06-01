<template>
    <div class="container">
        <div class="left-box">
            <img :src="babyImage" alt="Baby Image" class="left-image" />
            <img :src="dokterImage" alt="Dokter Image" class="left-image2" />
            <h2>Dashboard Admin Posyandu</h2>
            <p>
                Mengelola informasi kesehatan ibu dan anak, laporan kegiatan,
                dan data kunjungan dalam satu platform praktis.
            </p>
        </div>

        <div class="right-box">
            <div class="logo-wrapper">
                <img :src="logoImage" alt="Logo Image" class="logo-image" />
                <span class="logo-text">PosyanduCare</span>
            </div>

            <div class="signin-container">
                <h2>Lupa Password</h2>
                <p class="subtitle">
                    Silakan isi email untuk dikirim link reset password
                </p>
                <form @submit.prevent="sendResetLink">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" v-model="form.email" required />
                    </div>
                    <button type="submit" class="submit">
                        Kirim Link Reset Password
                    </button>
                </form>

                <!-- Menampilkan pesan respons -->
                <div v-if="form.message" class="message">
                    {{ form.message }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import babyImage from "@/assets/baby.jpg";
import dokterImage from "@/assets/dokter.png";
import logoImage from "@/assets/logo.png";
import axios from "axios";

export default {
    data() {
        return {
            form: {
                email: "",
                message: "", // untuk menampilkan pesan sukses/gagal
            },
            babyImage,
            dokterImage,
            logoImage,
        };
    },
    methods: {
        sendResetLink() {
            axios
                .post("https://capstonesi.online/api/auth/forgot-password", {
                    email: this.form.email,
                })
                .then((response) => {
                    this.form.message = response.data.message;
                })
                .catch((error) => {
                    // Untuk debugging atau menunjukkan error spesifik dari server
                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.message
                    ) {
                        this.form.message = error.response.data.message;
                    } else {
                        this.form.message =
                            "Terjadi kesalahan saat mengirim permintaan.";
                    }
                });
        },
    },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Protest+Strike&display=swap");

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
    font-size: 35px;
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
    border: none;
    border-radius: 20px;
    cursor: pointer;
    margin-top: 15px;
}

.submit:hover {
    background-color: #165d52;
}
.message {
    margin-top: 15px;
    font-weight: bold;
    color: green;
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
    }

    .left-image,
    .left-image2 {
        height: 150px;
    }

    .left-box h2 {
        font-size: 22px;
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
