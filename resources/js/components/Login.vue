<template>
    <div class="login">
        <div class="login-container">
            <div class="login-header">
                <h3>Inicio de sesión</h3>
            </div>
            <form @submit.prevent="login()">
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
                <error
                    v-if="$store.state.loginMessages"
                    v-for="(error, index) in $store.state.loginMessages"
                    :key="index"
                    :error="error"
                ></error>

                <div class="register-link-container">
                    <router-link to="/register" class="link-register"
                        >Registro</router-link
                    >
                </div>

                <div class="input-group login-btn-wrap">
                    <input type="submit" value="Ingresar" class="login-btn" />
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
                email: "",
                password: "",
            },
        };
    },
    methods: {
        async login() {
            const success = await this.$store.dispatch("login", this.user);
            if (success) {
                this.$router.push("/notes");
            }
        },
    },
    components: { error },
};
</script>

<style scoped>
html,
body {
    height: 100%;
    margin: 0;
}

.login {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f7f9fc;
}

.login-container {
    background-color: #ffffff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

.login-header {
    text-align: center;
    margin-bottom: 20px;
}

.input-group {
    margin-bottom: 20px;
}

.label {
    font-weight: bold;
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

.login-btn {
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

.login-btn:hover {
    background-color: #0056b3;
}

.register-link-container {
    text-align: center;
    margin-top: 1rem;
    padding: 10px 0;
}

.link-register {
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
