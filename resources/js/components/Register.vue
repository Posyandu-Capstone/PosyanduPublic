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
                <h2 :style="{ color: '#074A5D' }">Lengkapi Data Diri Anda</h2>
                <p class="subtitle">
                    Silakan lengkapi data diri Anda pada formulir di bawah ini
                    untuk melanjutkan pendaftaran
                </p>

                <form @submit.prevent="register">
                    <div class="input-group">
                        <label>Nama</label>
                        <div class="input-with-icon full-width">
                            <img
                                :src="informIcon"
                                alt="Info Icon"
                                class="form-icon-right"
                            />
                            <input
                                type="text"
                                v-model="form.nama_lengkap"
                                placeholder="Masukkan nama anda"
                                required
                            />
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Nomor Telepon</label>
                        <div class="input-with-icon full-width">
                            <img
                                :src="informIcon"
                                alt="Info Icon"
                                class="form-icon-right"
                            />
                            <input
                                type="text"
                                v-model="form.kontak"
                                placeholder="+62"
                                required
                            />
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Posisi</label>
                        <select
                            v-model="form.posisi"
                            class="styled-select"
                            :class="{ placeholder: form.posisi === '' }"
                            required
                        >
                            <option disabled value="">
                                -- Pilih Posisi --
                            </option>
                            <option value="Admin Desa">Admin Desa</option>
                            <option value="Kader">Kader</option>
                        </select>
                    </div>

                    <div class="input-group" v-if="form.posisi === 'Kader'">
                        <label>Posko</label>
                        <select
                            v-model="form.posko"
                            class="styled-select"
                            :class="{ placeholder: form.posko === '' }"
                            required
                        >
                            <option disabled value="">-- Pilih Posko --</option>
                            <option
                                v-for="posko in poskoList"
                                :key="posko.id"
                                :value="posko.id"
                            >
                                {{ posko.nama_posyandu }}
                            </option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Foto KTP</label>
                        <input
                            type="file"
                            @change="onFileChange('foto_ktp', $event)"
                            accept="image/*"
                            required
                        />
                    </div>

                    <div class="input-group">
                        <label>Foto Profil</label>
                        <input
                            type="file"
                            @change="onFileChange('foto_profil', $event)"
                            accept="image/*"
                            required
                        />
                    </div>

                    <SuccessAlert
                        :visible="showSuccessAlert"
                        :message="successMessage"
                    />

                    <button type="submit" class="signin-btn">Sign Up</button>
                </form>

                <p v-if="message" :class="messageClass">{{ message }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

import eyeIcon from "@/assets/eye.svg";
import eyeOffIcon from "@/assets/eye-off.svg";
import babyImage from "@/assets/baby.jpg";
import dokterImage from "@/assets/dokter.png";
import logoImage from "@/assets/logo.png";
import googleLogo from "@/assets/Google.svg";
import appleLogo from "@/assets/Apple.svg";
import informIcon from "@/assets/inform.svg";
import SuccessAlert from "@/components/SuccessAlert.vue";

export default {
    components: {
        SuccessAlert,
    },
    data() {
        return {
            form: {
                nama_lengkap: "",
                kontak: "",
                posisi: "",
                posko: "",
                email: "",
                password: "",
            },
            poskoList: [],
            files: {
                foto_ktp: null,
                foto_profil: null,
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
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    mounted() {
        this.fetchPosko();
        const url = window.location.href;
        const emailMatch = url.match(/email=([^&#]+)/); // Cari email setelah tanda '=' sebelum '#' atau '&'
        const tokenMatch = url.match(/token=([^&#]+)/); // Cari token dengan cara yang sama

        if (tokenMatch && emailMatch) {
            const token = decodeURIComponent(tokenMatch[1]);
            const email = decodeURIComponent(emailMatch[1]);

            localStorage.setItem("auth_token", token);
            localStorage.setItem("email", email);
            localStorage.setItem("password", "from_google_oauth");
            console.log("Token & email dari Google berhasil disimpan:", email);
            window.history.replaceState({}, document.title, "/register");
        }
    },
    methods: {
        async fetchPosko() {
            try {
                const apiUrl =
                    import.meta.env.VITE_APP_URL || "http://localhost:8000/api";
                const token = localStorage.getItem("auth_token");

                console.log("Token yang digunakan untuk ambil posko:", token);

                const response = await axios.get(`${apiUrl}/auth/poskos`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                console.log("Data posko berhasil diambil:", response.data);

                this.poskoList = response.data.data; // asumsi data berupa array posko [{id, nama}, ...]
            } catch (error) {
                console.error("Gagal ambil data posko:", error);
                this.message = "Gagal mengambil data posko.";
                this.messageClass = "error";
            }
        },
        onFileChange(field, event) {
            const file = event.target.files[0];
            if (file) {
                this.files[field] = file;
            }
        },
        async register() {
            const token = localStorage.getItem("auth_token");
            const email = localStorage.getItem("email");
            const password =
                localStorage.getItem("password") || "from_google_oauth";
            const nik = localStorage.getItem("nik");

            if (!email || !nik) {
                console.warn("Data tidak lengkap di localStorage:");
                console.warn("email:", email);
                console.warn("nik:", nik);
                // console.warn("token:", token);

                this.message =
                    "Data dari tahap sebelumnya tidak ditemukan. Silakan ulangi pendaftaran.";
                this.messageClass = "error";
                return;
            }

            const headers = { Authorization: `Bearer ${token}` };
            try {
                const apiUrl = "http://localhost:8000/api";

                // const checkResponse = await axios.get(
                //     `${apiUrl}/auth/user?email=${email}`,
                //     { headers }
                // );

                // if (checkResponse.data) {
                //     await axios.patch(
                //         `${apiUrl}/auth/update-nik`,
                //         { email, nik },
                //         { headers }
                // );

                //     }
                const formData = new FormData();
                formData.append("_method", "PATCH"); // penting!
                formData.append("nik", nik);
                formData.append("email", email);
                formData.append("password", password);

                formData.append("nama_lengkap", this.form.nama_lengkap);
                formData.append("kontak", this.form.kontak);
                formData.append("posisi", this.form.posisi);

                if (this.form.posisi === "Kader") {
                    if (!this.form.posko) {
                        this.message = "Posko wajib dipilih.";
                        this.messageClass = "error";
                        return;
                    }
                    formData.append("posyandu_id", Number(this.form.posko));
                }

                if (this.files.foto_ktp) {
                    formData.append("foto_ktp", this.files.foto_ktp);
                } else {
                    this.message = "Foto KTP wajib diunggah.";
                    this.messageClass = "error";
                    return;
                }

                if (this.files.foto_profil) {
                    formData.append("foto_profil", this.files.foto_profil);
                } else {
                    this.message = "Foto Profil wajib diunggah.";
                    this.messageClass = "error";
                    return;
                }

                // Debug
                for (let [key, val] of formData.entries()) {
                    console.log(`${key}:`, val);
                }

                try {
                    const apiUrl =
                        import.meta.env.VITE_APP_URL ||
                        "http://localhost:8000/api";

                    const response = await axios.post(
                        `${apiUrl}/auth/update`,
                        formData,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data",
                                Accept: "application/json",
                            },
                        }
                    );

                    this.successMessage = "Pendaftaran berhasil!";
                    this.showSuccessAlert = true;

                    // ✅ Sembunyikan alert & redirect ke login setelah 2 detik
                    setTimeout(() => {
                        this.showSuccessAlert = false;
                        this.$router.push("/login");
                    }, 2000);
                } catch (error) {
                    console.error("Error response:", error.response);
                    console.error("Full error detail:", error);

                    if (error.response?.status === 422) {
                        const errData = error.response.data.errors;
                        this.message = Object.values(errData)[0][0];
                    } else {
                        this.message =
                            error.response?.data?.message || "Gagal mendaftar.";
                    }
                    this.messageClass = "error";
                }
            } catch (error) {}
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
    color: #333;
    /* default warna teks saat dipilih */
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
    padding-top: 50px;
}

h2 {
    font-size: 29px;
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
    padding-right: 40px;
    /* Tambah padding agar teks tidak tertimpa ikon */
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
    flex-direction: row;
    /* Mengatur tombol agar sejajar */
    justify-content: center;
    /* Pusatkan tombol */
    gap: 10px;
    /* Jarak antar tombol */
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
    text-align: center;
    /* Pusatkan teks */
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
        font-size: 28px;
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
        flex-direction: column;
    }

    .input-group label {
        font-size: 14px;
    }

    .styled-select {
        appearance: auto;
        -webkit-appearance: auto;
        -moz-appearance: auto;
        position: relative;
        z-index: 10;
    }
    select.styled-select:focus {
        outline: none;
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
