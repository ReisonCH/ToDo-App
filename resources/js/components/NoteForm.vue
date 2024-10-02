<template>
    <div class="container">
        <div class="note-form-container">
            <h2>{{ isEditMode ? "Editar nota" : "Crear nota" }}</h2>
            <form @submit.prevent="submitForm">
                <div class="form-group">
                    <label for="title">Título</label>
                    <input
                        type="text"
                        id="title"
                        v-model="noteData.title"
                        required
                        :class="{ 'is-invalid': errors.title }"
                    />
                    <div v-if="errors.title" class="error">
                        {{ errors.title }}
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea
                        id="description"
                        v-model="noteData.description"
                        required
                        :class="{ 'is-invalid': errors.description }"
                    ></textarea>
                    <div v-if="errors.description" class="error">
                        {{ errors.description }}
                    </div>
                </div>

                <div class="form-group">
                    <label for="due_date">Fecha de vencimiento</label>
                    <input
                        type="date"
                        id="due_date"
                        v-model="noteData.due_date"
                    />
                </div>

                <div class="form-group">
                    <label for="image">Imagen</label>
                    <input type="file" id="image" @change="handleImageUpload" />
                </div>

                <div class="form-group">
                    <label for="tags">Etiquetas</label>
                    <div class="tag-container">
                        <div v-for="tag in tags" :key="tag.id" class="tag-item">
                            <input
                                type="checkbox"
                                :value="tag.id"
                                :id="`tag_${tag.id}`"
                                :checked="noteData.tags.includes(tag.id)"
                                @change="updateTags"
                            />
                            <label :for="`tag_${tag.id}`">{{ tag.name }}</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    {{ isEditMode ? "Actualizar" : "Crear" }}
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
    props: {
        note: {
            type: Object,
            default: () => ({
                title: "",
                description: "",
                due_date: "",
                image: null,
                tags: [],
            }),
        },
    },
    data() {
        return {
            noteData: {
                title: "",
                description: "",
                due_date: "",
                image: "",
                tags: [],
            },
            tags: [],
            errors: {
                title: null,
                description: null,
            },
        };
    },
    computed: {
        isEditMode() {
            return !!this.$route.params.id;
        },
    },
    async created() {
        if (this.isEditMode) {
            const response = await axios.get(
                `/api/notes/${this.$route.params.id}`
            );
            if (response.data.success) {
                const note = response.data.data;
                this.noteData = {
                    ...note,
                    tags: note.tags.map((tag) => tag.id),
                };

                if (note.due_date) {
                    this.noteData.due_date = note.due_date.split(" ")[0];
                }
            } else {
                this.$router.push("/notes");
            }
        }

        await this.fetchTags();
    },
    methods: {
        handleImageUpload(event) {
            const file = event.target.files[0];
            this.noteData.image = file;
        },

        validateForm() {
            this.errors = {};
            if (!this.noteData.title) {
                this.errors.title = "Él título es requerido.";
            }
            if (!this.noteData.description) {
                this.errors.description = "La descripción es requerida.";
            }
            return Object.keys(this.errors).length === 0;
        },

        async fetchTags() {
            const response = await axios.get("/api/tags");
            this.tags = response.data.data;
        },

        updateTags(event) {
            const tag = Number(event.target.value);

            if (event.target.checked) {
                if (!this.noteData.tags.includes(tag)) {
                    this.noteData.tags.push(tag);
                }
            } else {
                this.noteData.tags = this.noteData.tags.filter(
                    (t) => t !== tag
                );
            }
            console.log(this.noteData);
        },

        async submitForm() {
            if (!this.validateForm()) return;

            const formData = new FormData();
            formData.append("title", this.noteData.title);
            formData.append("description", this.noteData.description);
            if (this.noteData.due_date)
                formData.append("due_date", this.noteData.due_date);
            if (this.noteData.image instanceof File) {
                formData.append("image", this.noteData.image);
            }

            if (Array.isArray(this.noteData.tags)) {
                this.noteData.tags.forEach((tag, index) => {
                    formData.append(`tags[${index}]`, tag);
                });
            }

            try {
                if (this.isEditMode) {
                    formData.append("_method", "PUT");
                    const response = await axios.post(
                        `/api/notes/${this.$route.params.id}`,
                        formData
                    );

                    if (response.data.success) {
                        this.$router.push("/notes");

                        toast.success("Nota editada correctamente", {
                            autoClose: 2000,
                        });
                    }
                } else {
                    const response = await axios.post("/api/notes", formData);

                    if (response.data.success) {
                        this.$router.push("/notes");

                        toast.success("Nota creada correctamente", {
                            autoClose: 2000,
                        });
                    }
                }
            } catch (error) {
                if (error.response.data.errors) {
                    const errors = Object.values(error.response.data.errors);
                    errors.forEach((error, index) => {
                        toast.warning(error, {
                            autoClose: 2000,
                        });
                    });
                } else {
                    toast.warning("Ocurrió un error", {
                        autoClose: 2000,
                    });
                }
            }
        },
    },
};
</script>

<style scoped>
.container {
    display: flex;
    height: 100vh;
    align-items: center;
}

.note-form-container {
    width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

.form-group {
    width: 100%;
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

input,
textarea {
    width: -webkit-fill-available;
    padding: 10px 0 10px 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}

textarea {
    resize: none;
    height: 100px;
}

input.is-invalid,
textarea.is-invalid {
    border-color: #ff4d4d;
}

.error {
    color: #ff4d4d;
    font-size: 12px;
    margin-top: 5px;
}

.preview-image {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    margin-top: 10px;
}

.submit-btn {
    display: block;
    width: 100%;
    padding: 10px 0;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.submit-btn:hover {
    background-color: #0056b3;
}

.tag-container {
    display: flex;
    justify-content: center;
    gap: 32px;
}
</style>
