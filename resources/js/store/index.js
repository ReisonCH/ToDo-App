import { createStore } from "vuex";
import axios from "axios";
import router from "../router";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const store = createStore({
    state: {
        user: null,
        token: localStorage.getItem("token") || null,
        errors: [],
    },
    mutations: {
        SET_USER(state, user) {
            state.user = user;
        },
        SET_TOKEN(state, token) {
            state.token = token;
            localStorage.setItem("token", token);
        },
        SET_ERRORS(state, errors) {
            state.errors = errors;
        },
        CLEAR_ERRORS(state) {
            state.errors = [];
        },
        LOGOUT(state) {
            state.user = null;
            state.token = null;
            localStorage.removeItem("token");
        },
    },
    actions: {
        async register({ commit }, user) {
            commit("CLEAR_ERRORS");
            try {
                const response = await axios.post("/api/auth/register", user);

                if (response.data.success === true) {
                    return true;
                } else {
                    commit("SET_ERRORS", response.data.errors);

                    return false;
                }
            } catch (error) {
                commit("SET_ERRORS", ["Registration failed."]);
                if (error.response.data.errors) {
                    const errors = Object.values(error.response.data.errors);
                    errors.forEach((error, index) => {
                        toast.warning(error, {
                            autoClose: 2000,
                        });
                    });
                }

                return false;
            }
        },
        async login({ commit }, credentials) {
            commit("CLEAR_ERRORS");
            try {
                const response = await axios.post(
                    "/api/auth/login",
                    credentials
                );

                if (response.data.success === true) {
                    commit("SET_USER", response.data.user);
                    commit("SET_TOKEN", response.data.token);
                    return true;
                } else {
                    commit("SET_ERRORS", response.data.errors);
                    return false;
                }
            } catch (error) {
                commit("SET_ERRORS", ["Login failed."]);
                return false;
            }
        },
        logout({ commit }) {
            commit("LOGOUT");
            router.push("/login");
        },
    },
});
export default store;
