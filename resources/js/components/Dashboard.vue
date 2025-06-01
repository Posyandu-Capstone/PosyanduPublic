<template>
    <div class="layout">
        <!-- Sidebar -->
        <Sidebar
            :activeMenu="activeMenu"
            @update:activeMenu="updateActiveMenu"
        />

        <!-- Main Content -->
        <div class="main-content">
            <HeaderBar title="Dashboard" />
            <div class="separator"></div>

            <div class="dashboard-header">
                <div class="welcome-text">
                    <h3>Selamat Datang, {{ userRole }} {{ userName }} 👋</h3>
                </div>
                <div class="date-box">
                    <img class="icon" :src="iconKalender" alt="icon" />
                    <span>{{ formattedDate }}</span>
                </div>
            </div>

            <div class="quick-menu">
                <div
                    class="menu-card"
                    v-for="menu in quickMenus"
                    :key="menu.title"
                    @click="navigateTo(menu.path)"
                >
                    <div class="icon-wrapper">
                        <img :src="menu.icon" class="icon" :alt="menu.title" />
                    </div>
                    <p>{{ menu.title }}</p>
                </div>
            </div>

            <!-- Kelompok Usia -->
            <h3 class="section-title">Kelompok Usia Posyandu</h3>
            <div class="stats-section">
                <div
                    class="stat-card"
                    v-for="group in ageGroups"
                    :key="group.label"
                >
                    <p class="label">{{ group.label }}</p>
                    <p class="value">{{ group.count }} orang</p>
                </div>
            </div>

            <!-- Grafik Balita -->
            <h3 class="section-title balita">Grafik Pemantauan Balita</h3>
            <div class="chart-section">
                <div class="chart-row">
                    <ChartCard
                        title="Grafik Persebaran Berat Badan"
                        :chartData="chartDataBalita"
                    />
                    <ChartCard
                        title="Grafik Persebaran Gizi Balita"
                        :chartData="chartDataGizi"
                    />
                    <ChartCard
                        title="Grafik Persebaran BB/Umur Balita"
                        :chartData="chartDataBBUmur"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import HeaderBar from "@/components/HeaderBar.vue";
import logoImage from "@/assets/posyandu.svg";
import iconPosyandu from "@/assets/posko.svg";
import iconStetoskop from "@/assets/stetoskop.svg";
import iconPasien from "@/assets/pasien.svg";
import iconLaporan from "@/assets/laporan.svg";
import ChartCard from "@/components/ChartCard.vue";
import iconKalender from "@/assets/kalender.svg";
import axios from "axios";

export default {
    name: "DashboardComponent",
    components: {
        ChartCard,
        Sidebar,
        HeaderBar,
    },
    data() {
        return {
            logoImage,
            iconKalender,
            userName: "",
            userRole: "",
            activeMenu: "dashboard",
            today: new Date(),
            quickMenus: [],
            ageGroups: {},
            chartDataBalita: {
                labels: ["BB Kurang", "BB Normal", "BB Lebih"],
                datasets: [
                    {
                        label: "Jumlah Balita",
                        data: [],
                        backgroundColor: ["#D9D9D9", "#84BBD1", "#D9D9D9"],
                        borderRadius: 12,
                    },
                ],
            },
            chartDataGizi: {
                labels: ["Gizi Kurang", "Gizi Normal", "Gizi Lebih"],
                datasets: [
                    {
                        label: "Status Gizi Balita",
                        data: [],
                        backgroundColor: ["#D9D9D9", "#84BBD1", "#D9D9D9"],
                        borderRadius: 12,
                    },
                ],
            },
            chartDataBBUmur: {
                labels: ["Kurang", "Normal", "Lebih"],
                datasets: [
                    {
                        label: "Status BB Balita",
                        data: [],
                        backgroundColor: ["#D9D9D9", "#84BBD1", "#D9D9D9"],
                        borderRadius: 12,
                    },
                ],
            },
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
    methods: {
        updateActiveMenu(menu) {
            this.activeMenu = menu;
        },
        navigateTo(path) {
            if (path) {
                this.$router.push(path);
            }
        },
        updateChartData(chartKey, newData) {
            console.log("Updating chart:", chartKey, newData);
            const oldChart = this[chartKey];
            this[chartKey] = {
                ...oldChart,
                datasets: [
                    {
                        ...oldChart.datasets[0],
                        data: newData,
                    },
                ],
            };
        },
        async fetchAgeGroups() {
            try {
                const apiUrl =
                    import.meta.env.VITE_APP_URL || "http://localhost:8000/api";
                const token = localStorage.getItem("token");

                if (!token) {
                    console.warn(
                        "Token tidak ditemukan. User mungkin belum login."
                    );
                    return;
                }

                const response = await axios.get(`${apiUrl}/auth/statistics`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });

                if (response && response.data && response.data.data) {
                    const raw = response.data.data;
                    this.ageGroups = Object.entries(raw).map(
                        ([key, value]) => ({
                            label: key.charAt(0).toUpperCase() + key.slice(1),
                            count: value,
                        })
                    );
                } else {
                    console.warn("Data tidak ditemukan pada response.");
                }
            } catch (error) {
                console.error("Error fetching data:", error.message);
            }
        },
        async fetchStatisticsBalita() {
            try {
                const apiUrl =
                    import.meta.env.VITE_APP_URL || "http://localhost:8000/api";
                const token = localStorage.getItem("token");

                if (!token) {
                    console.warn(
                        "Token tidak ditemukan. User mungkin belum login."
                    );
                    return;
                }

                const response = await axios.get(
                    `${apiUrl}/auth/statisticsBalita`,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                console.log("Response dari API:", response.data);

                const data = response.data;

                if (
                    !data ||
                    !data.berat_badan ||
                    !data.gizi ||
                    !data.tinggi_badan
                ) {
                    console.warn(
                        "Data statistik balita tidak ditemukan pada response."
                    );
                    this.updateChartData("chartDataBalita", [1, 0, 0]);
                    return;
                }

                if (response.status !== 200) {
                    console.error(
                        `API Error: ${response.status}, Message: ${response.statusText}`
                    );
                    return;
                }

                this.updateChartData("chartDataBalita", [
                    Number(data.berat_badan.Kurang || 0),
                    Number(data.berat_badan.Normal || 0),
                    Number(data.berat_badan.Lebih || 0),
                ]);

                this.updateChartData("chartDataGizi", [
                    Number(data.gizi.Kurang || 0),
                    Number(data.gizi.Normal || 0),
                    Number(data.gizi.Lebih || 0),
                ]);

                this.updateChartData("chartDataBBUmur", [
                    Number(data.tinggi_badan.Kurang || 0),
                    Number(data.tinggi_badan.Normal || 0),
                    Number(data.tinggi_badan.Lebih || 0),
                ]);
            } catch (error) {
                console.error("Gagal fetch statistik balita:", error);
            }
        },
    },
    mounted() {
        this.userName = localStorage.getItem("userName") || "";
        this.userRole = localStorage.getItem("userRole") || "";
        if (this.userRole === "Admin Desa") {
            this.quickMenus = [
                {
                    icon: iconPosyandu,
                    title: "Data Posko Posyandu",
                    path: "/posko-posyandu",
                },
                {
                    icon: iconStetoskop,
                    title: "Pemeriksaan Kesehatan",
                    path: "/pemeriksaan",
                },
                {
                    icon: iconPasien,
                    title: "Data Pasien Posyandu",
                    path: "/datapasien",
                },
                {
                    icon: iconLaporan,
                    title: "Laporan Kesehatan",
                    path: "/laporan-kesehatan",
                },
            ];
        } else if (this.userRole === "Kader") {
            this.quickMenus = [
                {
                    icon: iconPasien,
                    title: "Data Pasien Posyandu",
                    path: "/datapasien",
                },
                {
                    icon: iconStetoskop,
                    title: "Pemeriksaan Kesehatan",
                    path: "/pemeriksaan",
                },
                {
                    icon: iconLaporan,
                    title: "Laporan Kesehatan",
                    path: "/laporan-kesehatan",
                },
                {
                    icon: iconPasien,
                    title: "Berita Posyandu",
                    path: "/berita",
                },
            ];
        }
        this.fetchAgeGroups();
        this.fetchStatisticsBalita();
        console.log("chartDataBalita:", this.chartDataBalita);
    },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap");

* {
    font-family: "Inter", sans-serif;
    box-sizing: border-box;
}

.layout {
    display: flex;
    height: 100vh;
    background-color: #f7f7f7;
    flex-wrap: wrap;
}

.main-content {
    flex: 1;
    background-color: #ffffff;
    padding: 30px;
    padding-top: 10px;
}

.separator {
    border-bottom: 1px solid #e5e7eb;
    margin: 10px 0 20px 0;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: -10px;
}

.quick-menu {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-top: 20px;
}

.date-box {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 800;
    color: #333436;
    background-color: white;
}

.menu-card {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: bold;
    color: #074a5d;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.2s, background-color 0.2s;
}

.menu-card:hover {
    background-color: #f1f5f9;
}

.menu-card:active {
    background-color: #08607a;
    color: white;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
}

.menu-card .icon-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
}

.icon {
    width: 24px;
    height: 24px;
    object-fit: cover;
}

.stats-section {
    margin-top: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 16px;
}

.stat-card {
    background-color: #ffffff;
    border: 1px solid #ccc;
    font-weight: bold;
    border-radius: 10px;
    padding: 16px;
    text-align: center;
}

.stat-card .label {
    font-size: 14px;
    color: #6b7280;
}

.stat-card .value {
    font-size: 20px;
    font-weight: bold;
    color: #08607a;
}

.section-title {
    margin-bottom: 5px;
    margin-top: 30px;
    font-weight: bold;
    font-size: 18px;
}

.chart-section {
    margin-top: 2rem;
}

.chart-row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.chart-row > * {
    flex: 1 1 calc(33.33% - 20px);
    /* Lebar 33% - gap */
    min-width: 250px;
    /* Minimum lebar card */
    max-width: 100%;
    box-sizing: border-box;
    /* Agar padding dan border dihitung dalam lebar total */
    padding: 10px;
}

.section-title.balita {
    margin-top: 30px;
    /* atau berapa pun sesuai kebutuhan */
    margin-bottom: -5px;
}

.section-title.ibu-hamil {
    margin-top: 30px;
    /* atau berapa pun sesuai kebutuhan */
    margin-bottom: -10px;
}

/* ========== RESPONSIVE STYLES ========== */
@media (max-width: 768px) {
    .layout {
        flex-direction: column;
    }

    .main-content {
        padding: 16px;
    }

    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .quick-menu {
        grid-template-columns: 1fr 1fr;
    }

    .stats-section {
        grid-template-columns: 1fr 1fr;
    }

    .chart-row {
        flex-direction: column;
    }

    .chart-row > * {
        flex: 1 1 auto;
        width: 100%;
        max-height: 300px; /* Tambahkan untuk batasi tinggi di HP */
    }

    .section-title {
        font-size: 16px;
    }

    .date-box {
        font-size: 12px;
        padding: 4px 10px;
    }

    .menu-card {
        font-size: 14px;
        padding: 16px;
    }

    .icon {
        width: 20px;
        height: 20px;
    }

    .stat-card .value {
        font-size: 18px;
    }

    .stat-card .label {
        font-size: 12px;
    }
}
</style>
