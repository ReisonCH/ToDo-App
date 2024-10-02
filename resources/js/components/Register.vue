<template>
    <div class="register">
        <div class="register-container">
            <div class="register-header">
                <h3>Registro</h3>
            </div>
            <form @submit.prevent="register()">
                <div class="input-group">
                    <label for="name">Nombre:</label>
                    <input
                        type="text"
                        id="name"
                        class="txt-input"
                        v-model="user.name"
                        placeholder="Introduce tu nombre"
                        required
                    />
                </div>
                <div class="input-group">
                    <label for="email">Correo:</label>
                    <input
                        type="email"
                        id="email"
                        class="txt-input"
                        v-model="user.email"
                        placeholder="Introduce tu correo electrónico"
                        required
                    />
                </div>
                <div class="input-group">
                    <label for="password">Contraseña:</label>
                    <input
                        type="password"
                        id="password"
                        class="txt-input"
                        v-model="user.password"
                        placeholder="Introduce tu contraseña"
                        required
                    />
                </div>
                <div class="input-group">
                    <label for="password_con">Confirmar Contraseña:</label>
                    <input
                        type="password"
                        id="password_con"
                        class="txt-input"
                        v-model="user.password_confirmation"
                        placeholder="Confirma tu contraseña"
                        required
                    />
                </div>
                <error
                    v-if="errors && errors.length"
                    v-for="(error, index) in errors"
                    :key="index"
                    :error="error"
                ></error>
                <div class="login-link-container">
                    <router-link to="/login" class="link-login"
                        >Inicio de sesión</router-link
                    >
                </div>
                <div class="input-group register-btn-wrap">
                    <input
                        type="submit"
                        value="Registrar"
                        class="register-btn"
                    />
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import error from "./Error.vue";

export default {
    data() {
        return {
            user: {
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
            errors: [],
        };
    },
    methods: {
        async register() {
            const success = await this.$store.dispatch("register", this.user);
            if (success) {
                this.$router.push("/login");
            }
        },
    },
    components: {
        error,
    },
};
</script>

<style scoped>
html,
body {
    height: 100%;
    margin: 0;
}

.register {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f7f9fc;
}

.register-container {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.register-header {
    text-align: center;
    margin-bottom: 20px;
}

.input-group {
    margin-bottom: 20px;
}

.txt-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s;
}

.txt-input:focus {
    border-color: #007bff;
    outline: none;
}

.register-btn {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
    font-weight: bold;
}

.register-btn:hover {
    background-color: #0056b3;
}

.login-link-container {
    text-align: center;
    margin-top: 1rem;
    padding: 10px 0;
}

.link-login {
    text-decoration: none;
    color: #0056b3;
}

.error {
    color: red;
    margin-top: 10px;
    font-size: 0.9em;
    text-align: center;
}
</style>
