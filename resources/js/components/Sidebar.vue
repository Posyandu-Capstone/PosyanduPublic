<template>
    <div>
        <!-- Hamburger Button (mobile only) -->
        <button class="hamburger-btn" @click="toggleSidebar">☰</button>

        <!-- Sidebar -->
        <aside
            class="sidebar"
            :class="{ 'sidebar-hidden': !isSidebarOpen && isMobile }"
        >
            <!-- Logo -->
            <div class="logo-wrapper">
                <img :src="logoImage" alt="Logo" class="logo-image" />
                <span class="logo-text">PosyanduCare</span>
            </div>

            <!-- Menu Utama -->
            <div class="menu-section">
                <p class="menu-label">Main Menu</p>
                <ul class="menu-list">
                    <router-link
                        v-if="role !== 'Admin Verifikator'"
                        to="/dashboard"
                        class="router-link-wrapper"
                    >
                        <li
                            class="menu-item"
                            :class="{ active: activeMenu === 'dashboard' }"
                        >
                            <img
                                src="@/assets/dashboard.svg"
                                class="icon"
                                alt="Dashboard"
                            />
                            <span>Dashboard</span>
                        </li>
                    </router-link>

                    <router-link
                        v-if="role === 'Admin Desa'"
                        to="/posko-posyandu"
                        class="router-link-wrapper"
                    >
                        <li
                            class="menu-item"
                            :class="{ active: activeMenu === 'posko' }"
                            @click="setActive('posko')"
                        >
                            <img
                                src="@/assets/posko1.svg"
                                class="icon"
                                alt="posko"
                            />
                            <span>Data Posko Posyandu</span>
                        </li>
                    </router-link>

                    <router-link
                        v-if="role !== 'Admin Verifikator'"
                        to="/pasien"
                        class="router-link-wrapper"
                    >
                        <li
                            class="menu-item"
                            :class="{ active: activeMenu === 'pasien' }"
                        >
                            <img
                                src="@/assets/data1.svg"
                                class="icon"
                                alt="Data Pasien"
                            />
                            <span>Data Pasien Posyandu</span>
                        </li>
                    </router-link>

                    <router-link
                        v-if="role !== 'Admin Verifikator'"
                        to="/laporan-kesehatan"
                        class="router-link-wrapper"
                    >
                        <li
                            class="menu-item"
                            :class="{ active: activeMenu === 'laporan' }"
                            @click="setActive('laporan')"
                        >
                            <img
                                src="@/assets/laporan1.svg"
                                class="icon"
                                alt="Laporan"
                            />
                            <span>Laporan Kesehatan</span>
                        </li>
                    </router-link>

                    <router-link
                        v-if="role === 'Kader'"
                        to="/berita"
                        class="router-link-wrapper"
                    >
                        <li
                            class="menu-item"
                            :class="{ active: activeMenu === 'berita' }"
                        >
                            <img
                                src="@/assets/data1.svg"
                                class="icon"
                                alt="Berita"
                            />
                            <span>Berita Posyandu</span>
                        </li>
                    </router-link>

                    <router-link
                        v-if="role === 'Admin Verifikator'"
                        to="/admin-verifikator"
                        class="router-link-wrapper"
                    >
                        <li
                            class="menu-item"
                            :class="{ active: activeMenu === 'verifikasi' }"
                        >
                            <img
                                src="@/assets/profil.svg"
                                class="icon"
                                alt="Verifikasi"
                            />
                            <span>Verifikasi Data Admin</span>
                        </li>
                    </router-link>

                    <router-link
                        v-if="role === 'Admin Verifikator'"
                        to="/warga-verifikator"
                        class="router-link-wrapper"
                    >
                        <li
                            class="menu-item"
                            :class="{ active: activeMenu === 'verifikasi2' }"
                        >
                            <img
                                src="@/assets/profil.svg"
                                class="icon"
                                alt="Verifikasi"
                            />
                            <span>Verifikasi Data Warga</span>
                        </li>
                    </router-link>
                </ul>
            </div>

            <!-- Menu Admin -->
            <div class="menu-section">
                <p class="menu-label">Admin</p>
                <ul class="menu-list">
                    <router-link to="/profil" class="router-link-wrapper">
                        <li
                            class="menu-item"
                            :class="{ active: activeMenu === 'profile' }"
                        >
                            <img
                                src="@/assets/profil.svg"
                                class="icon"
                                alt="Profile"
                            />
                            <span>Profil</span>
                        </li>
                    </router-link>
                    <li class="menu-item" @click="showModalConfirm = true">
                        <img
                            src="@/assets/SignOut.svg"
                            class="icon"
                            alt="Keluar"
                        />
                        <span>Keluar</span>
                    </li>
                </ul>
            </div>

            <ModalConfirm
                :visible="showModalConfirm"
                title="Konfirmasi Logout"
                message="Apakah Anda yakin ingin keluar?"
                @confirm="logout"
                @cancel="showModalConfirm = false"
            />
        </aside>
    </div>
</template>

<script>
import axios from "axios";
import logoImage from "@/assets/posyandu.svg";
import ModalConfirm from "@/components/ModalConfirm.vue";

export default {
    name: "Sidebar",
    components: {
        ModalConfirm,
    },
    props: ["activeMenu"],
    emits: ["update:activeMenu"],
    data() {
        return {
            logoImage,
            showModalConfirm: false,
            role: localStorage.getItem("userRole") || "",
            isSidebarOpen: true,
            isMobile: window.innerWidth < 768,
        };
    },
    mounted() {
        window.addEventListener("resize", this.checkScreenSize);
    },
    beforeUnmount() {
        window.removeEventListener("resize", this.checkScreenSize);
    },
    methods: {
        setActive(menu) {
            this.$emit("update:activeMenu", menu);
        },
        logout() {
            axios
                .post(
                    "https://capstonesi.online/api/auth/logout",
                    {},
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                )
                .then(() => {
                    localStorage.clear();
                    sessionStorage.clear();
                    this.showModalConfirm = false;
                    this.$router.push("/login");
                })
                .catch((error) => {
                    console.error("Logout gagal:", error);
                    alert("Gagal logout. Silakan coba lagi.");
                });
        },
        toggleSidebar() {
            if (this.isMobile) {
                this.isSidebarOpen = !this.isSidebarOpen;
            }
        },
        checkScreenSize() {
            this.isMobile = window.innerWidth < 768;
            if (!this.isMobile) {
                this.isSidebarOpen = true;
            }
        },
    },
};
</script>

<style scoped>
.hamburger-btn {
    position: fixed;
    top: 10px;
    left: 10px;
    z-index: 1001;
    background-color: #fff;
    border: 1px solid #ddd;
    padding: 8px 12px;
    font-size: 18px;
    cursor: pointer;
    display: none;
}
.sidebar {
    width: 270px;
    background-color: #ffffff;
    border-right: 1px solid #e5e7eb;
    padding: 20px;

    height: 100vh;
    flex-direction: column;
    gap: 24px;
}

@media (max-width: 768px) {
    .hamburger-btn {
        display: block;
    }
}

.sidebar {
    width: 270px;
    background-color: #ffffff;
    border-right: 1px solid #e5e7eb;
    padding: 20px;
    display: flex;
    height: 100vh;
    flex-direction: column;
    gap: 24px;
    transition: transform 0.3s ease;
    z-index: 1000;
}

.sidebar-hidden {
    transform: translateX(-100%);
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    background-color: white;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 5px;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 10px;
}

.logo-image {
    width: 35px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
    margin-top: -5px;
}

.logo-text {
    font-family: "Protest Strike", sans-serif;
    font-size: 20px;
    color: #08607a;
}

.menu-section {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: -15px;
}

.menu-label {
    font-size: 12px;
    font-weight: 600;
    color: #b0b385;
    margin-bottom: 4px;
}

.menu-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    color: #7d7f81;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.menu-list li:hover {
    background-color: #f1f5f9;
}

.menu-list li.active {
    background-color: #84bbd1;
    color: #074a5d;
    font-weight: bold;
}

.menu-list a {
    text-decoration: none;
    color: inherit;
}

.router-link-wrapper {
    text-decoration: none;
    color: inherit;
    display: block;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    transition: background-color 0.2s;
    color: #7d7f81;
    cursor: pointer;
    width: 100%;
}

.menu-item:hover {
    background-color: #f1f5f9;
}

.menu-item.active {
    background-color: #84bbd1;
    color: #074a5d;
    font-weight: bold;
}
</style>
