<template>
    <div class="container">
        <div class="header">
            <h1 class="title">Lista de Notas</h1>
            <button @click="handleLogout" class="btn btn-outline btn-sm">
                Salir
            </button>
        </div>
        <div class="create-btn-container">
            <router-link to="/notes/new" class="create-btn"
                >Crear nueva nota</router-link
            >
        </div>
        <div class="notes-grid">
            <div
                v-for="note in notes"
                :key="note.id"
                class="card"
                @click="openModal(note.image)"
            >
                <div class="card-header">
                    <h2 class="card-title">{{ note.title }}</h2>
                    <p class="card-description">{{ note.description }}</p>
                </div>
                <div class="card-content">
                    <div class="status-row">
                        <div class="note-tags">
                            <span
                                v-for="tag in note.tags"
                                :key="tag.id"
                                class="tag-bubble"
                            >
                                {{ tag.name }}
                            </span>
                        </div>
                    </div>
                    <span class="created-date">
                        Creado:
                        {{
                            note.created_at ? formatDate(note.created_at) : "-"
                        }}
                    </span>
                    <div class="card-footer">
                        <div class="due-date">
                            Vencimiento:
                            {{
                                note.due_date ? formatDate(note.due_date) : "-"
                            }}
                        </div>
                        <div class="note-buttons">
                            <div class="note-actions">
                                <router-link
                                    :to="`/notes/edit/${note.id}`"
                                    class="edit-button"
                                    @click.stop
                                >
                                    Editar
                                </router-link>
                            </div>
                            <div class="note-actions">
                                <button
                                    @click.stop="deleteNote(note.id)"
                                    class="delete-button"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showModal" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <span class="close-btn" @click="closeModal">&times;</span>
                <img
                    :src="modalImage"
                    alt="Imagen de la nota"
                    class="modal-image"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { mapState } from "vuex";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
    data() {
        return {
            notes: [],
            showModal: false,
            modalImage: null,
        };
    },
    computed: {
        ...mapState(["notes"]),
    },
    async created() {
        await this.fetchNotes();
    },

    methods: {
        async fetchNotes() {
            try {
                const response = await axios.get("/api/notes");

                if (response.data.success) {
                    this.notes = response.data.data;
                }
            } catch (error) {
                console.error("Error fetching notes:", error);
            }
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },

        handleLogout() {
            this.$store.dispatch("logout");
        },

        async deleteNote(noteId) {
            if (confirm("¿Estás seguro de que deseas eliminar esta nota?")) {
                try {
                    const response = await axios.delete(`/api/notes/${noteId}`);
                    if (response.data.success) {
                        // alert("Nota eliminada correctamente");
                        toast.success("Nota eliminada correctamente", {
                            autoClose: 2000,
                        });
                        await this.fetchNotes();
                        this.$router.push("/notes");
                    } else {
                        toast.warning("Ocurrió un error al eliminar la nota", {
                            autoClose: 2000,
                        });
                    }
                } catch (error) {
                    toast.warning("Ocurrió un error al eliminar la nota", {
                        autoClose: 2000,
                    });
                }
            }
        },

        openModal(image) {
            if (image) {
                this.modalImage = image;
                this.showModal = true;
            }
        },

        closeModal() {
            this.showModal = false;
            this.modalImage = null;
        },
    },
};
</script>

<style scoped>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
}

.create-btn-container {
    display: flex;
    justify-content: center;
}

.create-btn {
    display: inline-flex;
    padding: 10px 20px;
    background-color: #0056b3;
    color: #ffffff;
    text-align: center;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
    margin: 16px 0;
}

.create-btn:hover {
    background-color: #0056b3;
}

/* Header styles */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.title {
    font-size: 2rem;
    font-weight: bold;
}

.btn {
    padding: 0.5rem 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: transparent;
    cursor: pointer;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
}

.btn-sm {
    font-size: 0.75rem;
}

.btn-outline {
    border-color: #ccc;
    color: #333;
}

.icon {
    margin-right: 0.5rem;
    height: 1rem;
    width: 1rem;
}

/* Grid for notes */
.notes-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 768px) {
    .notes-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .notes-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Card styles */
.card {
    max-width: 350px;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    background-color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    cursor: pointer;
}

.card-header {
    margin-bottom: 1rem;
}

.card-title {
    font-size: 1.25rem;
    font-weight: bold;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;
}

.card-description {
    font-size: 1rem;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.note-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.tag-bubble {
    background-color: #007bff;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    display: inline-block;
    cursor: default;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    white-space: nowrap;
    transition: background-color 0.3s;
}

.tag-bubble:hover {
    background-color: #0056b3;
}

/* Status row and badge styles */
.status-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
    text-transform: capitalize;
}

.badge-completed {
    background-color: green;
    color: white;
}

.badge-progress {
    background-color: orange;
    color: white;
}

.badge-default {
    background-color: gray;
    color: white;
}

/* Date styles */
.created-date,
.due-date {
    font-size: 0.875rem;
    color: #666;
}

.card-footer {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
}

.note-buttons {
    display: flex;
    justify-content: space-between;
}

.note-actions {
    flex: 1;
}

.note-buttons {
    gap: 5px;
}

.edit-button,
.delete-button {
    display: inline-block;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: bold;
    border-radius: 5px;
    text-align: center;
    cursor: pointer;
    width: 100%;
    box-sizing: border-box;
}

.edit-button {
    background-color: #007bff;
    color: white;
    border: none;
    text-decoration: none;
}

.edit-button:hover {
    background-color: #0056b3;
}

.delete-button {
    background-color: #dc3545;
    color: white;
    border: none;
}

.delete-button:hover {
    background-color: #c82333;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    position: relative;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    max-width: 80%;
    max-height: 80%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-image {
    max-width: 100%;
    max-height: 100%;
    border-radius: 5px;
}

.close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 24px;
    cursor: pointer;
    color: #333;
}

.note-thumbnail {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
    cursor: pointer;
}
</style>
