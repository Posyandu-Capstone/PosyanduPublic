    import { createRouter, createWebHistory } from "vue-router";

    // Import components
    import Login from "./components/Login.vue";
    import Register from "./components/Register.vue";
    import Dashboard from "./components/Dashboard.vue";
    import ForgotPassword from "./components/ForgotPassword.vue";
    import ResetPassword from "./components/ResetPassword.vue";
    import Status from "./components/Status.vue";
    import LoginTransition from "./components/LoginTransition.vue";
    import Pemeriksaan from "./components/PemeriksaanKesehatan.vue";
    import RiwayatPemeriksaan from "./components/RiwayatPemeriksaan.vue";
    import FormPemeriksaan from "./components/FormPemeriksaan.vue";
    import TambahForm from "./components/TambahForm.vue";
    import UpdateForm from "./components/UpdateForm.vue";
    import Antrian from "./components/Antrian.vue";
    import AntrianAdd from "./components/Antrian-add.vue";
    import RegisterStep1 from "./components/RegisterNIK.vue";
    import ConfirmNIK from "./components/ConfirmNIK.vue";
    import RegisterStep2 from "./components/RegisterPassword.vue";
    import Verifikasi from "./components/Verifikasi.vue";
    import DataPasien from "./components/DataPasien.vue";
    import DataPasienDetail from "./components/DataPasien-detail.vue";
    import TambahAnggota from "./components/TambahAnggota.vue";
    import EditAnggota from "./components/EditAnggota.vue";
    import InformasiLengkap from "./components/InformasiLengkap.vue";
    import LaporanKesehatan from "./components/LaporanKesehatan.vue";
    import PoskoPosyandu from "./components/PoskoPosyandu.vue";
    import TambahPosko from "./components/TambahPosko.vue";
    import EditPosko from "./components/Posko-edit.vue";
    import PoskoInfo from "./components/Posko-info.vue";
    import Berita from "./components/Berita.vue";
    import BeritaAdd from "./components/Berita-add.vue";
    import BeritaEdit from "./components/Berita-edit.vue";
    import BeritaInfo from "./components/Berita-info.vue";
    import AdminVerifikator from "./components/AdminVerifikator.vue";
    import AdminVerifikatorDetail from "./components/AdminVerifikator-detail.vue";
    import WargaVerifikator from "./components/WargaVerifikator.vue";
    import WargaVerifikatorDetail from "./components/WargaVerifikator-detail.vue";
    import Profile from "./components/profile.vue";
    import ProfileEdit from "./components/profil-edit.vue";
    import UbahPassword from "./components/UbahPassword.vue";

    // Fungsi validasi token
    const isTokenValid = () => {
        const token = localStorage.getItem("token");
        const expiry = localStorage.getItem("token_expiry");
        return token && expiry && Date.now() < parseInt(expiry);
    };

    // Daftar route dengan pemisahan yang memerlukan token dan yang tidak
    const routes = [
        // Public routes (Tidak memerlukan token)
        { path: "/", redirect: "/login", meta: { title: "Login" } },
        { path: "/login", component: Login, meta: { title: "Login" } },
        { path: "/register", component: Register, meta: { title: "Register" } },
        { path: "/register/:token", component: Register, meta: { title: "Register" } },
        { path: "/loginTransition", component: LoginTransition, meta: { title: "Login Transition" } },
        { path: "/forgot-password", component: ForgotPassword, meta: { title: "Forgot Password" } },
        { path: "/reset-password/:token", component: ResetPassword, meta: { title: "Reset Password" } },
        { path: "/verifikasi", component: Verifikasi, meta: { title: "Verifikasi Akun" } },
        { path: "/registerstep1", component: RegisterStep1, meta: { title: "Register - Step 1" } },
        { path: "/confirmnik", component: ConfirmNIK, meta: { title: "Confirm NIK" } },
        { path: "/registerstep2", component: RegisterStep2, meta: { title: "Register - Step 2" } },
        { path: "/pending", component: Status, meta: { title: "Status" } },
        { path: "/denied", component: Status, meta: { title: "Access Denied" } },

        // Protected routes (Memerlukan token)
        { path: "/dashboard", component: Dashboard, meta: { requiresAuth: true, title: "Dashboard" } },
        { path: "/pemeriksaan", component: Pemeriksaan, meta: { requiresAuth: true, title: "Pemeriksaan Kesehatan" } },
        { path: "/riwayatpasien/:nik", component: RiwayatPemeriksaan, meta: { requiresAuth: true, title: "Riwayat Pemeriksaan Pasien" } },
        { path: "/formpemeriksaan/:nik/:id", component: FormPemeriksaan, meta: { requiresAuth: true, title: "Form Pemeriksaan" } },
        { path: "/updatepemeriksaan/:nik/:id", component: UpdateForm, meta: { requiresAuth: true, title: "Update Pemeriksaan" } },
        { path: "/formtambah/:nik", component: TambahForm, meta: { requiresAuth: true, title: "Tambah Form Pemeriksaan" } },
        { path: "/antrian/:nik", name: "antrian",component: Antrian, meta: { requiresAuth: true, title: "Antrian Pemeriksaan" } },
        { path: "/antrian-add/:beritaId", component: AntrianAdd, meta: { requiresAuth: true, title: "Tambah Antrian" } },
        { path: "/pasien", component: DataPasien, meta: { requiresAuth: true, title: "Data Pasien" } },
        { path: "/datapasien",  name: "DataPasienDetail",component: DataPasienDetail, meta: { requiresAuth: true, title: "Detail Data Pasien" } },
        { path: "/tambahanggota", component: TambahAnggota, meta: { requiresAuth: true, title: "Tambah Anggota Keluarga" } },
        { path: "/editanggota/:id", component: EditAnggota, meta: { requiresAuth: true, title: "Edit Anggota Keluarga" } },
        { path: "/informasilengkap/:id", component: InformasiLengkap, meta: { requiresAuth: true, title: "Informasi Lengkap Pasien" } },
        { path: "/laporan-kesehatan", component: LaporanKesehatan, meta: { requiresAuth: true, title: "Laporan Kesehatan" } },
        { path: "/posko-posyandu", component: PoskoPosyandu, meta: { requiresAuth: true, title: "Posko Posyandu" } },
        { path: "/tambah-posko", component: TambahPosko, meta: { requiresAuth: true, title: "Tambah Posko Posyandu" } },
        { path: "/posko-edit/:id", component: EditPosko, meta: { requiresAuth: true, title: "Edit Posko Posyandu" } },
        { path: "/posko-informasi/:id", component: PoskoInfo, meta: { requiresAuth: true, title: "Informasi Posko Posyandu" } },
        { path: "/berita", component: Berita, meta: { requiresAuth: true, title: "Berita" } },
        { path: "/berita-add", component: BeritaAdd, meta: { requiresAuth: true, title: "Tambah Berita" } },
        { path: "/berita-edit/:id", component: BeritaEdit, meta: { requiresAuth: true, title: "Edit Berita" } },
        { path: "/berita-informasi/:id", component: BeritaInfo, meta: { requiresAuth: true, title: "Informasi Berita" } },
        { path: "/admin-verifikator", name: "AdminVerifikator", component: AdminVerifikator, meta: { requiresAuth: true, title: "Admin Verifikator" } },
        { path: "/admin-verifikator-detail/:nik",  name: "AdminVerifikatorDetail", component: AdminVerifikatorDetail, meta: { requiresAuth: true, title: "Detail Admin Verifikator" } },
        { path: "/warga-verifikator", name: "WargaVerifikator", component: WargaVerifikator, meta: { requiresAuth: true, title: "Warga Verifikator" } },
        { path: "/warga-verifikator-detail/:nik",   name: "WargaVerifikatorDetail", component: WargaVerifikatorDetail, meta: { requiresAuth: true, title: "Detail Warga Verifikator" } },
        { path: "/profil", component: Profile, meta: { requiresAuth: true, title: "Profil" } },
        { path: "/profile-edit", component: ProfileEdit, meta: { requiresAuth: true, title: "Edit Profil" } },
        { path: "/ubah-password", component: UbahPassword, meta: { requiresAuth: true, title: "Ubah Password" } },
    ];

    // Membuat router dengan history mode
    const router = createRouter({
        history: createWebHistory(),
        routes,
    });

    // Middleware untuk memastikan token validasi sebelum melanjutkan
    router.beforeEach((to, from, next) => {
        if (to.meta.requiresAuth && !isTokenValid()) {
            next("/login");
        } else {
            next();
        }
    });

    // Set judul halaman jika tersedia di meta
    router.afterEach((to) => {
        if (to.meta?.title) {
            document.title = to.meta.title;
        }
    });

    export default router;