<template>
    <div class="container">
        <h2>Manage Locations</h2>

        <!-- Add or Edit Section -->
        <div
            v-if="currentAction === 'create' || currentAction === 'edit'"
            class="form-container"
        >
            <form @submit.prevent="submitForm">
                <div>
                    <label>Nama Kecamatan:</label>
                    <input type="text" v-model="form.Nama_Kecamatan" required />
                </div>

                <!-- Parent ID field is removed from the form during edit -->
                <!-- <div v-if="currentAction === 'edit'">
          <label>Parent ID:</label>
          <input type="number" v-model="form.parent_id" />
        </div> -->

                <button type="submit" class="submit-btn">
                    {{ currentAction === "create" ? "Create" : "Update" }}
                    Location
                </button>
            </form>
        </div>

        <!-- List Locations Section -->
        <div>
            <h3>List of Locations</h3>
            <button @click="fetchLocations" class="refresh-btn">Refresh</button>
            <table>
                <thead>
                    <tr>
                        <th>Nama Kecamatan</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(location, index) in locations"
                        :key="location.id"
                    >
                        <td>{{ location.Nama_Kecamatan }}</td>
                        <td>
                            <button
                                @click="editLocation(location.id)"
                                class="edit-btn"
                            >
                                Edit
                            </button>
                            <button
                                @click="deleteLocation(location.id)"
                                class="delete-btn"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Display messages for success or errors -->
        <p v-if="message" :class="messageClass">{{ message }}</p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            form: {
                Nama_Kecamatan: "",
                parent_id: null, // Keep it null for create, but do not send it during update
            },
            locations: [],
            message: "",
            messageClass: "",
            currentAction: null, // 'create' or 'edit'
        };
    },
    methods: {
        // Fetch list of locations
        async fetchLocations() {
            try {
                const apiUrl =
                    import.meta.env.APP_URL || "https://capstonesi.online/api";
                const response = await axios.get(`${apiUrl}/auth/kecamatan`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                });
                this.locations = response.data;
            } catch (error) {
                this.message =
                    error.response?.data?.message ||
                    "Failed to fetch locations.";
                this.messageClass = "error";
            }
        },

        // Submit form data (Create or Update)
        async submitForm() {
            // Prepare the data to send, excluding parent_id if it's null or not required
            const dataToSubmit = { ...this.form };

            // Only send parent_id if it's not null and during Create action
            if (this.currentAction === "edit") {
                delete dataToSubmit.parent_id; // Remove parent_id from the request during update
            }

            const apiUrl =
                import.meta.env.APP_URL || "https://capstonesi.online/api";
            const endpoint =
                this.currentAction === "create"
                    ? "kecamatan"
                    : `kecamatan/${this.form.id}`;

            try {
                const method = this.currentAction === "create" ? "post" : "put";
                const response = await axios[method](
                    `${apiUrl}/auth/${endpoint}`,
                    dataToSubmit,
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem(
                                "token"
                            )}`,
                        },
                    }
                );

                this.message = `${
                    this.currentAction === "create" ? "Created" : "Updated"
                } successfully!`;
                this.messageClass = "success";
                this.fetchLocations();
                this.resetForm();
            } catch (error) {
                this.message =
                    error.response?.data?.message || "Failed to submit form.";
                this.messageClass = "error";
            }
        },

        // Delete a location
        async deleteLocation(id) {
            try {
                const apiUrl =
                    import.meta.env.APP_URL || "https://capstonesi.online/api";
                await axios.delete(`${apiUrl}/auth/kecamatan/${id}`, {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem(
                            "token"
                        )}`,
                    },
                });

                this.message = "Location deleted successfully!";
                this.messageClass = "success";
                this.fetchLocations();
            } catch (error) {
                this.message =
                    error.response?.data?.message ||
                    "Failed to delete location.";
                this.messageClass = "error";
            }
        },

        // Pre-fill form for editing a location
        editLocation(id) {
            const location = this.locations.find(
                (location) => location.id === id
            );
            this.form = { ...location };
            this.currentAction = "edit";
        },

        // Reset form fields
        resetForm() {
            this.form = { Nama_Kecamatan: "", parent_id: null };
            this.currentAction = "create";
        },
    },
    mounted() {
        this.fetchLocations();
        this.resetForm();
    },
};
</script>

<style scoped>
.container {
    width: 80%;
    margin: auto;
    padding: 20px;
}

form {
    margin-bottom: 20px;
}

.form-container {
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 20px;
    background-color: #f9f9f9;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    padding: 10px 15px;
    color: white;
    background-color: #4caf50;
    border: none;
    cursor: pointer;
    border-radius: 4px;
}

button:hover {
    background-color: #45a049;
}

.refresh-btn {
    background-color: #008cba;
}

.submit-btn {
    background-color: #4caf50;
}

.edit-btn {
    background-color: #ff9800;
    margin-right: 5px;
}

.delete-btn {
    background-color: #f44336;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th,
table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.success {
    color: green;
}

.error {
    color: red;
}
</style>
