<template>
    <div class="layout">
        <!-- Sidebar -->
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="activeMenu = $event"
        />

        <div class="main-content">
            <HeaderBar title="Data Pasien" />
            <div class="separator"></div>

            <!-- 👇 quick-menu sekarang di dalam main-content -->
            <div class="quick-menu">
                <router-link
                    v-for="menu in quickMenus"
                    :key="menu.title"
                    :to="menu.path"
                    class="menu-card"
                    style="text-decoration: none"
                >
                    <div class="icon-wrapper">
                        <img :src="menu.icon" class="icon" :alt="menu.title" />
                    </div>
                    <div class="title-wrapper">
                        <p>{{ menu.title }}</p>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import logoImage from "@/assets/posyandu.svg";
import iconStetoskop from "@/assets/Stetoskop1.svg";
import iconPasien from "@/assets/pasien2.svg";
import ChartCard from "@/components/ChartCard.vue";
import iconKalender from "@/assets/kalender.svg";

export default {
    name: "DataPasienComponent",
    components: {
        ChartCard,
        Sidebar,
        HeaderBar,
    },
    data() {
        return {
            logoImage,
            iconKalender,
            activeMenu: "pasien",
            today: new Date(),
            quickMenus: [
                {
                    icon: iconStetoskop,
                    title: "Pemeriksaan Kesehatan",
                    path: "/pemeriksaan",
                },
                {
                    icon: iconPasien,
                    title: "Data Pasien",
                    path: "/datapasien",
                },
            ],
        };
    },
    computed: {
        formattedDate() {
            const options = { day: "numeric", month: "long" };
            const dateStr = this.today.toLocaleDateString("id-ID", options);
            const [day, month] = dateStr.split(" ");
            return `${month} ${day}`;
        },
    },
    methods: {},
    mounted() {},
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

.quick-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-top: 20px;
}

.menu-card {
    background-color: #4f93af;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 5px; /* Jarak antar ikon dan teks */
    font-weight: 500;
    color: #ffffff;
    font-weight: bold;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Bayangan */
    transition: box-shadow 0.2s, background-color 0.2s; /* Animasi transisi */
}

.menu-card:hover {
    background-color: #08607a; /* Perubahan warna saat hover */
}

.menu-card:active {
    background-color: #08607a; /* Perubahan warna saat diklik */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Bayangan lebih tajam saat diklik */
}

.menu-card .icon-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-left: 15px; /* Geser ikon ke kiri */
}

.menu-card p {
    color: #ffffff; /* Ubah warna teks */
    font-size: 20px; /* Ubah ukuran teks */
    font-weight: 600; /* Ubah ketebalan teks */
    margin: 0; /* Hapus margin biar nggak geser */
    line-height: 2.5; /* Tinggi baris teks */
    text-align: center;
}

.title-wrapper {
    flex: 1; /* Ambil sisa ruang */
    display: flex;
    justify-content: center; /* Teks di tengah secara horizontal */
}

.icon {
    width: 35px; /* Ukuran ikon bisa disesuaikan */
    height: 35px;
    object-fit: cover;
}

.icon path {
    fill: white; /* Warna ikon */
}

.date-box {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-family: "Inter", sans-serif;
    font-size: 13px;
    font-weight: 800;
    color: #333436;
    background-color: white;
}

@media (max-width: 768px) {
    .layout {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
        flex-direction: row;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* buat scroll smooth di iOS */
    }

    .menu-section {
        flex: 1;
        margin-top: 0;
    }

    .main-content {
        padding: 16px;
        width: 100%; /* supaya isi main-content full lebar container */
        box-sizing: border-box; /* pastikan padding tidak keluar */
    }

    /* Perbaiki grid quick-menu di mobile: jadi 1 kolom */
    .quick-menu {
        grid-template-columns: 1fr !important;
        gap: 12px;
    }

    /* Perbaiki ukuran font dan padding di .menu-card */
    .menu-card {
        padding: 16px;
        font-size: 16px;
        gap: 10px;
    }

    .menu-card .icon-wrapper {
        margin-left: 10px;
    }

    .menu-card p {
        text-align: left; /* responsive: rata kiri */
        font-size: 16px; /* opsional, bisa juga lebih kecil */
    }
    .title-wrapper {
        justify-content: flex-start; /* responsive: kiri */
    }

    .sidebar-footer {
        display: none; /* Jika ingin, bisa dibuat tombol floating */
    }
}
</style>
