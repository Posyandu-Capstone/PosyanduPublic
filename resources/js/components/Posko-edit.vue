<template>
    <div class="posko-container">
        <div class="main-content">
            <SideBar activeMenu="posko" />
            <div class="content-wrapper">
                <h2 class="page-title">Detail Posko Posyandu</h2>

                <div class="container">
                    <h1 class="head-name">Informasi Posko Posyandu</h1>

                    <form @submit.prevent="updatePosko">
                        <div class="form-group">
                            <label for="namaPosyandu">Nama Posyandu</label>
                            <input
                                type="text"
                                v-model="form.namaPosyandu"
                                id="namaPosyandu"
                            />
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input
                                type="text"
                                v-model="form.alamat"
                                id="alamat"
                            />
                        </div>

                        <div class="form-group">
                            <label for="dusun">Dusun/Desa/Kelurahan</label>
                            <select v-model="form.desa_id" id="dusun">
                                <option
                                    v-for="desa in daftarDesa"
                                    :key="desa.code"
                                    :value="desa.code"
                                >
                                    {{ desa.Nama_Desa }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <select v-model="form.kecamatan_id" id="kecamatan">
                                <option
                                    v-for="kec in daftarKecamatan"
                                    :key="kec.code"
                                    :value="kec.code"
                                >
                                    {{ kec.Nama_Kecamatan }}
                                </option>
                            </select>
                        </div>

                        <SuccessAlert
                            :visible="showSuccessAlert"
                            :message="successMessage"
                        />

                        <div class="btn-add">
                            <button
                                type="button"
                                class="left-btn"
                                @click="handleCancel"
                            >
                                Batal
                            </button>
                            <button type="submit" class="right-btn">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import SideBar from "@/components/SideBar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import SuccessAlert from "@/components/SuccessAlert.vue";

export default {
    name: "PoskoInfo",
    components: {
        SideBar,
        HeaderBar,
        SuccessAlert,
    },
    data() {
        return {
            form: {
                namaPosyandu: "",
                alamat: "",
                desa_id: "",
                kecamatan_id: "",
            },
            daftarKecamatan: [],
            daftarDesa: [],
            loading: true,
            error: null,
            showSuccessAlert: false,
            successMessage: "",
        };
    },
    created() {
        this.loadReferensi();
    },
    methods: {
        loadReferensi() {
            const token = localStorage.getItem("token");

            const kecamatanRequest = axios.get(
                "http://localhost:8000/api/auth/kecamatan",
                {
                    headers: { Authorization: `Bearer ${token}` },
                }
            );

            const desaRequest = axios.get(
                "http://localhost:8000/api/auth/desa",
                {
                    headers: { Authorization: `Bearer ${token}` },
                }
            );

            Promise.all([kecamatanRequest, desaRequest])
                .then(([resKecamatan, resDesa]) => {
                    this.daftarKecamatan =
                        resKecamatan.data.data || resKecamatan.data;
                    this.daftarDesa = resDesa.data.data || resDesa.data;
                    this.loadPosko();
                })
                .catch((err) => {
                    this.error = "Gagal memuat data referensi kecamatan/desa";
                    console.error(err);
                    this.loading = false;
                });
        },

        loadPosko() {
            const id = this.$route.params.id;
            const token = localStorage.getItem("token");

            axios
                .get(`http://localhost:8000/api/auth/posyandu/${id}`, {
                    headers: { Authorization: `Bearer ${token}` },
                })
                .then((response) => {
                    const data = response.data.data;
                    this.form.namaPosyandu = data.nama_posyandu;
                    this.form.alamat = data.alamat;
                    this.form.desa_id = data.desa_id;
                    this.form.kecamatan_id = data.kecamatan_id;
                    this.loading = false;
                })
                .catch((err) => {
                    this.error = "Gagal memuat data posyandu";
                    console.error(err);
                    this.loading = false;
                });
        },

        updatePosko() {
            const id = this.$route.params.id;
            const token = localStorage.getItem("token");

            console.log("Kecamatan ID yang dikirim:", this.form.kecamatan_id);

            const selectedDesa = this.daftarDesa.find(
                (d) => d.code === this.form.desa_id
            );

            axios
                .put(
                    `http://localhost:8000/api/auth/posyandu/${id}`,
                    {
                        nama_posyandu: this.form.namaPosyandu,
                        alamat: this.form.alamat,
                        desa_id: this.form.desa_id,
                        kecamatan_id: this.form.kecamatan_id,
                        nama_desa: selectedDesa ? selectedDesa.Nama_Desa : "",
                    },
                    {
                        headers: { Authorization: `Bearer ${token}` },
                    }
                )
                .then(() => {
                    this.successMessage = "Data posyandu berhasil diupdate!";
                    this.showSuccessAlert = true;

                    setTimeout(() => {
                        this.showSuccessAlert = false;
                        this.$router.push("/posko-posyandu"); // Redirect ke daftar posko
                    }, 2500); // kasih waktu 2.5 detik sebelum redirect
                })
                .catch((err) => {
                    console.error("Gagal mengupdate posyandu", err);
                    alert("Gagal mengupdate data posyandu.");
                });
        },

        handleCancel() {
            this.$router.go(-1); // kembali ke halaman sebelumnya
        },
    },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap");

.posko-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    font-family: "Inter", sans-serif;
    background-color: #ffffff;
}

.main-content {
    display: flex;
    flex: 1;
}

.content-wrapper {
    flex: 1;
    padding: 0px 24px;
}

.page-title {
    font-size: 20px;
    font-weight: 600;
    color: #1e293b;
    margin: 24px 0;
    padding-bottom: 18px;
    border-bottom: 1px solid #edf2f7;
}

.container {
    border-radius: 12px;
    border: 1px solid #e5e5e5;
    width: 100%;
    background-color: #fff;
}

.head-name {
    color: #111216;
    padding: 16px;
    border-bottom: 1px solid #e5e5e5;
    font: 600 14px "Inter", sans-serif;
}

.sub-name {
    font-size: 14px;
    text-align: center;
    padding: 8px;
}

.form-group {
    display: flex;
    padding: 12px 54px;
    font: 600 14px "Inter", sans-serif;
    flex-wrap: nowrap;
}

label {
    margin-bottom: 6px;
    width: 300px; /* bisa disesuaikan */
    flex-shrink: 0;
}

input,
select {
    padding: 10px 17px;
    border: 1px solid #e5e5e5;
    border-radius: 10px;
    flex: 1;
    padding: 0.8rem;
    min-width: 0;
}

.btn-add {
    display: flex;
    justify-content: space-between;
    padding: 20px 54px !important;
    align-items: center;
}

.left-btn,
.right-btn {
    flex: 0 0 auto;
}

.left-btn {
    font-size: 16px;
    font-weight: 400;
    border-radius: 90px; /* buat button bulat */
    background-color: transparent;
    cursor: pointer;
    border: 2px solid #08607a; /* border warna biru */
    color: #08607a; /* teks warna biru */
    padding: 10px 20px; /* tambahkan padding supaya tombolnya nyaman */
}

.right-btn {
    font-size: 16px;
    font-weight: 400;
    border-radius: 90px; /* border radius kotak agak membulat */
    background-color: #08607a; /* background biru */
    color: white; /* teks putih */
    cursor: pointer;
    padding: 11px 20px;
    border: none; /* hapus border */
}

/* Responsive untuk layar kecil, misal max-width 768px (tablet ke bawah) */
@media screen and (max-width: 768px) {
    .form-group {
        flex-direction: column; /* label dan input jadi stack vertikal */
        padding: 12px 16px; /* padding diperkecil agar pas */
        gap: 0.75rem;
    }

    label {
        width: 100%; /* label full width */
        margin-bottom: 6px;
    }

    select {
        width: 100%; /* input full width */
        flex: unset; /* reset flex agar input lebar 100% */
        min-width: auto;
    }

    .btn-add {
        flex-direction: column; /* tombol jadi stack vertikal */
        padding: 20px 16px !important;
        gap: 12px; /* jarak antar tombol */
        align-items: stretch; /* tombol lebar penuh */
    }

    .left-btn,
    .right-btn {
        flex: unset; /* reset flex agar tombol bisa lebar penuh */
        width: 100%;
    }

    .left-btn button,
    .right-btn button {
        width: 100%; /* tombol lebar penuh */
        font-size: 14px; /* font sedikit lebih kecil agar pas */
        padding: 12px 0; /* padding vertikal untuk tombol */
    }
}
</style>
