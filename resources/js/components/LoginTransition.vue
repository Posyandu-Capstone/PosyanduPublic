<template>
    <div v-if="loading" class="loading-container">
        <p>Memproses login Anda...</p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            loading: true,
        };
    },
    async mounted() {
        const url = window.location.href;
        const tokenMatch = url.match(/token=([^&#]+)/);
        const emailMatch = url.match(/email=([^&#]+)/);

        if (tokenMatch && emailMatch) {
            const token = decodeURIComponent(tokenMatch[1]);
            const email = decodeURIComponent(emailMatch[1]);
            const apiUrl = "http://localhost:8000/api";

            try {
                const userResponse = await fetch(
                    `${apiUrl}/auth/user?email=${email}`,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                const user = await userResponse.json();

                if (user.status === "success") {
                    localStorage.setItem("token", token);
                    localStorage.setItem("email", email);

                    const meResponse = await axios.post(
                        `${apiUrl}/auth/me`,
                        {},
                        {
                            headers: {
                                Authorization: `Bearer ${token}`,
                            },
                        }
                    );

                    const role = meResponse.data.posisi;
                    const nama_lengkap = meResponse.data.nama_lengkap;

                    localStorage.setItem("userRole", role);
                    localStorage.setItem("userName", nama_lengkap);

                    console.log("Login berhasil:", email, "->", nama_lengkap);

                    setTimeout(() => {
                        this.$router.push("/dashboard");
                    }, 1000);
                } else {
                    localStorage.clear();
                    console.warn("Akses ditolak");
                    this.$router.push("/login");
                }
            } catch (error) {
                console.error("Gagal memproses login:", error);
                this.$router.push("/login");
            }
        } else {
            this.$router.push("/login");
        }
    },
};
</script>

<style scoped>
.loading-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-size: 1.5rem;
    color: #333;
}
</style>
